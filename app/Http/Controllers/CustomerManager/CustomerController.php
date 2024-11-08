<?php

namespace App\Http\Controllers\CustomerManager;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cashbook;
use App\Models\CreditPaymentLog;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(){
        $data['title'] = "List Customer";
        $data['title2'] = "Add Customer";
        $data['customers'] = Customer::all();
        return view('customermanager.list-customer',$data);
    }

    public function create(){
        $data['title'] = "List Customer";
        $data['title2'] = "Add Customer";
        $data['customer'] = new Customer();
        return setPageContent('customermanager.new',$data);
    }

    public function store(Request $request){

        $request->validate(Customer::$validate);

        $data = $request->only(Customer::$fields);

        if(empty($data['credit_limit'])){
            $data['credit_limit'] = 0;
        }

        $customer = Customer::create($data);

        if(!isset($request->ajax))
        {
            return redirect()->route('customer.index')->with('success', 'Customer added successfully');
        }
        return response()->json(['status'=>true,"id"=>$customer->id,"value"=>$request->get('firstname')." ".$request->get('lastname')]);
    }

    public function edit($id){

        $data['title'] = "Update Customer";
        $data['customer'] = Customer::find($id);

        return setPageContent('customermanager.edit',$data);
    }


    public function update(Request $request, $id){

        $request->validate(Customer::$validate);

        $customer = Customer::find($id);

        $customer->update($request->only(Customer::$fields));

        return redirect()->route('customer.index')->with('success','Customer updated successfully');
    }



    public function add_payment(Request $request){

        if($request->getMethod() == "POST"){

            return DB::transaction(function() use ($request){

                $payment = Payment::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $request->customer_id,
                    'invoice_number' => "CREDIT-PAYMENT",
                    'invoice_id' => 0,
                    'invoice_type'=>"App\\Models\\CreditPaymentLog",
                    'warehousestore_id' => getActiveStore()->id,
                    'subtotal' => $request->amount,
                    'total_paid' => $request->amount,
                    'payment_time' => Carbon::now()->toTimeString(),
                    'payment_date' => $request->payment_date,
                ]);

                $pmethod = new PaymentMethodTable(
                    [
                        'user_id' => auth()->id(),
                        'customer_id' => $request->customer_id,
                        'payment_method_id' =>$request->payment_method,
                        'invoice_id' => 0,
                        'invoice_type'=>"App\\Models\\CreditPaymentLog",
                        'warehousestore_id' => getActiveStore()->id,
                        'payment_date' => $request->payment_date,
                        'amount' => $request->amount,
                        'payment_info' => json_encode($request->bank ? ['payment_method_id'=>$request->payment_method ,'bank_id' =>$request->bank] : [])
                    ]
                );

                $pmethod->save();

                if($request->payment_method == "3" && isset($request->bank)) {
                    $cashbookData = [
                        "type" => "Credit",
                        "bank_account_id" => $request->bank,
                        "amount" =>$request->amount,
                        "comment" => "Customer Payment Credit Payment",
                        "transaction_date" => $request->payment_date,
                        "last_updated" => auth()->id(),
                        "user_id" => auth()->id(),
                        "cashbookable_type" => Payment::class,
                        "cashbookable_id" => $payment->id,
                    ];

                    Cashbook::create($cashbookData);
                };


                $log = [
                    'payment_id' => $payment->id,
                    'user_id' => auth()->id(),
                    'payment_method_id' =>$pmethod->id,
                    'customer_id' => $request->customer_id,
                    'invoice_number' => "CREDIT-PAYMENT",
                    'invoice_id' => NULL,
                    'amount' => $request->amount,
                    'payment_date' => $request->payment_date,
                ];

                $log = CreditPaymentLog::create($log);

                $payment->invoice_id = $log->id;
                $payment->update();

                $pmethod->invoice_id = $log->id;

                $pmethod->update();

                return redirect()->route('customer.add_payment')->with('success','Payment has been added successfully!');
            });

        }

        $data['title'] = "Add Customer Credit Payment";
        $data['payments'] = PaymentMethod::where('status',1)->where('id','<>',4)->get();
        $data['banks'] = BankAccount::where('status',1)->get();
        $data['customers'] = Customer::where('id','>',1)->get();
        return setPageContent('customermanager.add_payment',$data);
    }

}
