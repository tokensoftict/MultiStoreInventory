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
        return view('customermanager.new',$data);
    }

    public function store(Request $request){

        $request->validate(Customer::$validate);

        $data = $request->only(Customer::$fields);

        if(empty($data['credit_limit'])){
            $data['credit_limit'] = 0;
        }


        $customer = Customer::create($data);

        if(isset($request->credit_bought_forward) && $request->credit_bought_forward > 0) {
            $log = [
                'payment_id' =>NULL,
                'user_id' => auth()->id(),
                'payment_method_id' => NULL,
                'customer_id' => $customer->id,
                'invoice_number' => "CREDIT-PAYMENT",
                'invoice_id' => NULL,
                'amount' => -($request->credit_bought_forward),
                'payment_date' => date('Y-m-d'),
            ];

          CreditPaymentLog::create($log);
        }


        if(!isset($request->ajax))
        {
            return redirect()->route('customer.index')->with('success', 'Customer added successfully');
        }
        return response()->json(['status'=>true,"id"=>$customer->id,"value"=>$request->get('firstname')." ".$request->get('lastname')]);
    }

    public function edit($id){

        $data['title'] = "Update Customer";
        $data['customer'] = Customer::find($id);

        return view('customermanager.edit',$data);
    }


    public function update(Request $request, $id){

        $request->validate([
            'firstname'=>'required',
            //'lastname'=>'required',
            //'phone_number' => 'required|unique:customers,phone_number,'.$id,
            'email'=>'sometimes|nullable|email|unique:customers,email,'.$id,
        ]);

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
                        'payment_id' => $payment->id,
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

                return redirect()->route('reports.customerReport.payment_report')->with('success','Payment has been added successfully!');
            });

        }

        $data['title'] = "Add Customer Credit Payment";
        $data['payments'] = PaymentMethod::where('status',1)->where('id','<>',4)->get();
        $data['banks'] = BankAccount::where('status',1)->get();
        $data['customers'] = Customer::where('id','>',1)->get();
        return setPageContent('customermanager.add_payment',$data);
    }


    public function edit_payment(Request $request, $id){
        $payment = Payment::find($id);
        if($request->getMethod() == "POST"){
            return DB::transaction(function() use ($request, $id){
                $payment = Payment::find($id);
                $payment->payment_method_tables()->delete();
                Cashbook::where('cashbookable_type',get_class($payment))->where('cashbookable_id', $payment->id)->delete();
                $payment->delete();
                CreditPaymentLog::where('payment_id',$payment->id)->delete();

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
                        'payment_id' => $payment->id,
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

                return redirect()->route('reports.customerReport.payment_report')->with('success','Payment has been added successfully!');
            });
        }

        $data['title'] = "Edit Customer Credit Payment";
        $data['payments'] = PaymentMethod::where('status',1)->where('id','<>',4)->get();
        $data['banks'] = BankAccount::where('status',1)->get();
        $data['customers'] = Customer::where('id','>',1)->get();
        $data['payment_edit'] = $payment;
        return view('customermanager.edit_payment',$data);
    }

}
