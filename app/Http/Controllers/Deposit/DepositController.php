<?php

namespace App\Http\Controllers\Deposit;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cashbook;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\DepositPaymentLog;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
class DepositController extends Controller
{
    protected $settings;
    public function __construct(Settings $_settings)
    {
        $this->settings = $_settings;
    }


    public function index()
    {
        $data = [];
        $data['title'] = "Today's Deposit List";
        $date = date('Y-m-d');
        if(isset($request->date)){
            $date = $request->date;
        }
        $data['date'] = $date;
        $data['deposit'] = Deposit::with(['created_user','customer'])->where('warehousestore_id', getActiveStore()->id)->where('deposit_date', $date)->orderBy("id", "DESC")->get();
        return view('deposit.list',$data);
    }

    public function allow_user_to_change_deposit_date()
    {

    }

    public function new()
    {
        $data = [];
        $data['customers'] = Customer::query()->get();
        $data['c'] = Customer::query()->find(1);
        $data['settings'] =  $this->settings;
        $data['deposit_number'] = "";
        if(config('app.generate_invoice_number')) {
            $data['deposit_number'] = generateRandomString(10);
        }
        return setPageContent('deposit.new-deposit',$data);
    }


    public function view($id)
    {
        $data = [];
        $data['title'] = 'View Deposit';
        $data['deposit'] = Deposit::with(['created_by','customer','deposit_items'])->find($id);
        return setPageContent('deposit.view',$data);
    }


    public function edit($id)
    {
        $data = [];
        $data['customers'] = Customer::query()->get();
        $data['invoice'] = Deposit::with(['created_by','customer','deposit_items'])->findorfail($id);
        $data['settings'] =  $this->settings;
        return view('deposit.update-deposit',$data);
    }


    public function destroy($id)
    {
        $deposit = Deposit::find($id);
        if($deposit) {
            $deposit->delete();
        }

        return redirect()->route('deposit.list_deposit')->with('success', 'Deposit has been deleted successfully');
    }


    public function create(Request $request)
    {
        $reports = Deposit::validateDepositProduct(json_decode($request->get('data'), true), 'quantity');

        if ($reports['status'] == true) return response()->json(['status' => false, 'error' => $reports['errors']]);

        $deposit = Deposit::createDeposit($request, $reports, false);

        $success_view = view('deposit.success', ['deposit_id' => $deposit->id])->render();

        return json(['status' => true, 'html' => $success_view]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Deposit::find($id);

        $reports = Deposit::validateDepositProduct(json_decode($request->get('data'), true), 'quantity');

        if($reports['status'] == true) return response()->json(['status'=>false,'error'=>$reports['errors']]);

        $deposit = Deposit::updateDeposit($request,$reports, $invoice);

        $success_view = view('deposit.success',['deposit_id'=> $deposit->id])->render();

        return json(['status'=>true,'html'=>$success_view]);
    }

    public function print_pos($id){
        $data = [];
        $deposit = Deposit::with(['created_user','customer','deposit_items'])->find($id);
        $data['deposit'] =$deposit;
        $data['store'] =  $deposit->warehousestore_id == 1 ? $this->settings->store() : $deposit->warehousestore;
        $page_size = $deposit->deposit_items()->get()->count() * 15;
        $page_size += 180;
        $pdf = PDF::loadView('print.deposit_pos', $data,[],[
            'format' => [80,$page_size],
            'margin_left'          => 0,
            'margin_right'         => 0,
            'margin_top'           => 0,
            'margin_bottom'        => 0,
            'margin_header'        => 0,
            'margin_footer'        => 0,
            'orientation'          => 'P',
            'display_mode'         => 'fullpage',
            'custom_font_dir'      => '',
            'custom_font_data' 	   => [],
            'default_font_size'    => '12',
        ]);
        return $pdf->stream();
    }

    public function print_afour($id)
    {
        $data = [];
        $deposit = Deposit::with(['created_user','customer','deposit_items'])->find($id);
        $data['deposit'] = $deposit;
        $data['store'] =  $deposit->warehousestore_id == 1 ? $this->settings->store() : $deposit->warehousestore;
        $pdf = PDF::loadView("print.pos_afour_deposit",$data);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($deposit->status));
        $pdf->getMpdf()->showWatermarkText = true;
        return $pdf->stream('document.pdf');
    }

    public function print_afive($id)
    {
        $data = [];
        $deposit = Deposit::with(['created_user','customer','deposit_items'])->find($id);
        $data['deposit'] = $deposit;
        $data['store'] =  $deposit->warehousestore_id == 1 ? $this->settings->store() : $deposit->warehousestore;
        $page_size = $deposit->deposit_items()->get()->count() * 15;
        $page_size += 180;

        $pdf = PDF::loadView("print.pos_afive_deposit", $data,[],[
            'format' => [148,210],
            'display_mode'         => 'fullpage',
            'orientation'          => 'P',
        ]);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($deposit->name));
        $pdf->getMpdf()->showWatermarkText = true;
        return $pdf->stream('document.pdf');
    }

    public function print_way_bill($id){
        $data = [];
        $deposit = Deposit::with(['created_user','customer','invoice_items'])->find($id);
        $data['invoice'] = $deposit;
        $data['store'] =  $deposit->warehousestore_id == 1 ? $this->settings->store() : $deposit->warehousestore;
        $pdf = PDF::loadView("print.pos_afour_waybill_deposit",$data);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($deposit->name));
        $pdf->getMpdf()->showWatermarkText = true;
        return $pdf->stream('document.pdf');
    }



    public function add_depsoit_payment($id, Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'customer_id' => 'required',
                'amount' => 'required',
                'payment_date' => 'required',

                'payment_method' => 'required|integer',
                'bank' => 'required_if:payment_method,2,3|string',
            ]);

            return DB::transaction(function() use ($request, $id){

                $payment = Payment::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $request->customer_id,
                    'invoice_number' => "DEPOSIT-PAYMENT",
                    'invoice_id' => 0,
                    'invoice_type'=>DepositPaymentLog::class,
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
                        'invoice_type'=>DepositPaymentLog::class,
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
                        "comment" => "Customer Payment Deposit Payment",
                        "transaction_date" => $request->payment_date,
                        "last_updated" => auth()->id(),
                        "user_id" => auth()->id(),
                        "cashbookable_type" => Payment::class,
                        "cashbookable_id" => $payment->id,
                    ];

                    Cashbook::create($cashbookData);
                }


                $log = [
                    'payment_id' => $payment->id,
                    'user_id' => auth()->id(),
                    'payment_method_id' =>$pmethod->id,
                    'customer_id' => $request->customer_id,
                    'invoice_number' => "DEPOSIT-PAYMENT",
                    'invoice_id' => NULL,
                    'amount' => $request->amount,
                    'payment_date' => $request->payment_date,
                ];

                $log = CreditPaymentLog::create($log);

                $payment->invoice_id = $log->id;
                $payment->update();

                $pmethod->invoice_id = $log->id;

                $pmethod->update();

                return route('deposit.view', $id)-with('success', 'Deposit payment added successfully.');
            });

        } else {
            $deposit = Deposit::find($id);
            $data['customer'] = $deposit->customer;
            $data['deposit'] = $deposit;
            $data['payments'] = PaymentMethod::where('status',1)->where('id','<>',4)->get();
            $data['banks'] = BankAccount::where('status',1)->get();
            $data['title'] = 'Add Deposit Payment';
            return view('deposit.add_deposit_payment', $data);
        }
    }

}
