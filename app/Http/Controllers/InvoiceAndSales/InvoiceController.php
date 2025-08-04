<?php

namespace App\Http\Controllers\InvoiceAndSales;

use App\Models\BankAccount;
use App\Models\Cashbook;
use App\Models\CreditPaymentLog;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentMethodTable;
use PDF;
use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    protected $settings;

    public function __construct(Settings $_settings){
        $this->settings = $_settings;
    }


    public function draft_invoice(){

    }

    public function complete_invoice(){

    }

    public function request_for_discount(){

    }

    public function allow_user_to_change_invoice_date(){

    }

    public function new(){
        $data = [];
        $data['customers'] = Customer::all();
        $data['payments'] = PaymentMethod::all();
        $data['banks'] = BankAccount::where('status',1)->get();
        $data['settings'] =  $this->settings;
        $data['invoice_number'] = "";
        if(config('app.generate_invoice_number')) {
            $data['invoice_number'] = generateRandomString(10);
        }
        return setPageContent('invoice.new-invoice',$data);
    }

    public function draft(Request $request){
        $data = [];
        $data['title'] = 'Draft Invoice List';
        $date = date('Y-m-d');
        if(isset($request->date)){
            $date = $request->date;
        }
        $data['date'] = $date;
        $data['invoices'] = Invoice::with(['created_user','customer'])->where('warehousestore_id', getActiveStore()->id)->where('status','DRAFT')->where('invoice_date', $date)->orderBy("id", "DESC")->get();
        return view('invoice.draft-invoice',$data);
    }

    public function paid(Request $request) {
        $data = [];
        $data['title'] = 'Completed Invoice List';
        $date = date('Y-m-d');
        if(isset($request->date)){
            $date = $request->date;
        }
        $data['date'] = $date;
        $data['invoices'] = Invoice::with(['created_user','customer', 'paymentMethodTable'])->where('warehousestore_id', getActiveStore()->id)->where('status','COMPLETE')->where('invoice_date', $date)->orderBy("id", "DESC")->get();
        return view('invoice.paid-invoice',$data);
    }


    public function paid_stock(Request $request) {
        $data = [];
        $data['title'] = 'Completed Invoice List';
        $date = date('Y-m-d');
        if(isset($request->date)){
            $date = $request->date;
        }
        $data['date'] = $date;
        $data['allInvoices'] = Invoice::with(['created_user','customer', 'paymentMethodTable', 'invoice_items'])->where('warehousestore_id', getActiveStore()->id)->where('status','COMPLETE')->where('invoice_date', $date)->orderBy("id", "DESC")->get();

        return view('invoice.paid-stock-invoice',$data);
    }

    public function discount(Request $request){
        $data = [];
        $data['title'] = 'Pending Discount Invoice List';
        $date = date('Y-m-d');
        if(isset($request->date)){
            $date = $request->date;
        }
        $data['date'] = $date;
        $data['invoices'] = Invoice::with(['created_user','customer', 'paymentMethodTable'])->where('warehousestore_id', getActiveStore()->id)->where(function($query){
            $query->orWhere('status', "DISCOUNT-APPLIED")->orWhere('status', "DISCOUNT");
        })->where('invoice_date',$date)->orderBy("id", "DESC")->get();
        return view('invoice.paid-invoice',$data);
    }

    public function update(Request $request, $id){
        $invoice = Invoice::find($id);

        $reports = Invoice::validateInvoiceUpdateProduct(json_decode($request->get('data'),true),'quantity', $invoice);

        if($reports['status'] == true) return response()->json(['status'=>false,'error'=>$reports['errors']]);

        if($request->get('payment') !== "false" && $request->get('status') == 'COMPLETE') {

            $creditStatus = Payment::validateCreditLimit(['payment_info' => $request->get('payment'), "type" => "Invoice"], $reports);

            if ($creditStatus === true) {
                return response()->json(['status' => false, 'error' => "Customer has reached the credit limit, transaction can not continue"]);
            }
        }

        $invoice = Invoice::updateInvoice($request,$reports, $invoice);

        if($request->get('payment') !== "false" && $request->get('status') == 'COMPLETE'){
            $payment = Payment::createPayment(['invoice'=>$invoice,'payment_info'=>$request->get('payment'), "type"=>"Invoice"]);

            $invoice->payment_id = $payment->id;

            $invoice->total_amount_paid = $payment->total_paid;

            $invoice->update();
        }

        $success_view = view('invoice.success-updated',['invoice_id'=> $invoice->id])->render();

        return json(['status'=>true,'html'=>$success_view]);
    }

    public function create(Request $request){

        if($request->has("no_stock")){

            $invoice = Invoice::findorfail($request->get('invoice_id'));

            $creditStatus = Payment::validateCreditLimit([ 'payment_info' =>json_decode($request->get('payment_info'), true), "type" => "Invoice"], $invoice);

            if ($creditStatus === true) {
                return redirect()->route("invoiceandsales.view", $invoice->id)->with("error", "Customer has reached the credit limit, transaction can not continue");
            }

            if($invoice->status == "COMPLETE") return redirect()->route('invoiceandsales.view',$request->get('invoice_id'))->with('success','Invoice has been completed successfully!');

            $payment = Payment::createPayment(['invoice'=>$invoice, 'payment_info' =>json_decode($request->get('payment_info'), true), "type"=>"Invoice"]);

            $invoice->payment_id = $payment->id;

            $invoice->status = "COMPLETE";

            $invoice->total_amount_paid = $payment->total_paid;

            $invoice->update();

            return redirect()->route('invoiceandsales.view',$request->get('invoice_id'))->with('success','Invoice has been completed successfully!');
        } else{
            $reports = Invoice::validateInvoiceProduct(json_decode($request->get('data'), true), 'quantity');    // validate products if the quantity is okay

            if ($reports['status'] == true) return response()->json(['status' => false, 'error' => $reports['errors']]);

            if ($request->get('payment') !== "false" && $request->get('status') == 'COMPLETE') {

                $creditStatus = Payment::validateCreditLimit(['payment_info' => json_decode($request->get('payment'), true), "type" => "Invoice"], $reports);

                if ($creditStatus === true) {
                    return response()->json(['status' => false, 'error' => "Customer has reached the credit limit, transaction can not continue"]);
                }
            }

            $invoice = Invoice::createInvoice($request, $reports, false);

            if ($request->get('payment') !== "false" && $request->get('status') == 'COMPLETE') {

                $payment = Payment::createPayment(['invoice' => $invoice, 'payment_info' => json_decode($request->get('payment'), true), "type" => "Invoice"]);

                $invoice->payment_id = $payment->id;

                $invoice->total_amount_paid = $payment->total_paid;

                $invoice->update();

            }

            $success_view = view('invoice.success', ['invoice_id' => $invoice->id])->render();

            return json(['status' => true, 'html' => $success_view]);
        }
    }


    public function print_pos($id){
        $data = [];
        $invoice = Invoice::with(['created_user','customer','invoice_items'])->find($id);
        $data['invoice'] =$invoice;
        $data['store'] =  $invoice->warehousestore_id == 1 ? $this->settings->store() : $invoice->warehousestore;
        $page_size = $invoice->invoice_items()->get()->count() * 15;
        $page_size += 180;
        $pdf = PDF::loadView('print.pos', $data,[],[
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
        return $pdf->stream('document.pdf');
    }

    public function print_afour($id){
        $data = [];
        $invoice = Invoice::with(['created_user','customer','invoice_items'])->find($id);
        $data['invoice'] = $invoice;
        $data['store'] =  $invoice->warehousestore_id == 1 ? $this->settings->store() : $invoice->warehousestore;
        $pdf = PDF::loadView("print.pos_afour",$data);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($invoice->status));
        $pdf->getMpdf()->showWatermarkText = true;
        return $pdf->stream('document.pdf');
    }

    public function print_afive($id){
        $data = [];
        $invoice = Invoice::with(['created_user','customer','invoice_items'])->find($id);
        $data['invoice'] = $invoice;
        $data['store'] =  $invoice->warehousestore_id == 1 ? $this->settings->store() : $invoice->warehousestore;
        $page_size = $invoice->invoice_items()->get()->count() * 15;
        $page_size += 180;

        $pdf = PDF::loadView("print.pos_afive", $data,[],[
            'format' => [148,210],
            'display_mode'         => 'fullpage',
            'orientation'          => 'P',
        ]);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($invoice->name));
        $pdf->getMpdf()->showWatermarkText = true;
        return $pdf->stream('document.pdf');
    }

    public function print_way_bill($id){
        $data = [];
        $invoice = Invoice::with(['created_user','customer','invoice_items'])->find($id);
        $data['invoice'] = $invoice;
        $data['store'] =  $invoice->warehousestore_id == 1 ? $this->settings->store() : $invoice->warehousestore;
        $pdf = PDF::loadView("print.pos_afour_waybill",$data);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($invoice->name));
        $pdf->getMpdf()->showWatermarkText = true;
        return $pdf->stream('document.pdf');
    }


    public function view($id){
        $data = [];
        $data['title'] = 'View Invoice';
        $data['banks'] = BankAccount::all();
        $data['payments'] = PaymentMethod::all();
        $data['invoice'] = Invoice::with(['created_by','customer','invoice_items'])->find($id);
        return setPageContent('invoice.view',$data);
    }


    public function edit($id){
        $data = [];
        $data['customers'] = Customer::all();
        $data['payments'] = PaymentMethod::all();
        $data['invoice'] = Invoice::with(['created_by','customer','invoice_items','payment'])->findorfail($id);
        $data['banks'] = BankAccount::where('status',1)->get();
        $data['settings'] =  $this->settings;
        return view('invoice.update-invoice',$data);
    }


    public function destroy($id){
        $invoice = Invoice::find($id);
        $status = $invoice->status;
        if($invoice->sub_total > -1) {
            foreach ($invoice->invoice_item_batches()->get() as $invoice_batch) {
                $batch = $invoice_batch->stockbatch;
                $batch->{$invoice_batch->store} += $invoice_batch->quantity;
                $batch->update();
            }

        }

        if($invoice->sub_total < 0) {
            foreach ($invoice->invoice_item_batches()->get() as $invoice_batch) {
                $batch = $invoice_batch->stockbatch;
                $cache =  $batch->{$invoice_batch->store};
                $batch->{$invoice_batch->store} -= $invoice_batch->quantity;
                if($batch->{$invoice_batch->store} < 0) {
                    $batch->{$invoice_batch->store} = $cache;
                }
                $batch->update();
            }
        }

        $invoice->invoice_items()->delete();

        $invoice->invoice_item_batches()->delete();

        $payment_id = $invoice->payment_id;

        $invoice->payment_id = NULL;

        $invoice->total_amount_paid = 0;

        $invoice->update();

        if($payment_id != NULL) {
            $payment = Payment::find($payment_id);
            Cashbook::where('cashbookable_id',$payment_id)->where('cashbookable_type', Payment::class)->delete();
            CreditPaymentLog::where('payment_id',$payment_id)->delete();
            $payment->delete();
        }

        $invoice->delete();

        if(in_array($status, ['PAID','COMPLETE'])) {
            $route = 'invoiceandsales.paid';
        } else {
            $route = 'invoiceandsales.draft';
        }

        return redirect()->route($route)->with('success', 'Invoice has been deleted successfully');

    }


    public function return_invoice(){
        $data = [];
        $data['customers'] = Customer::all();
        $data['payments'] = PaymentMethod::all();
        $data['banks'] = BankAccount::where('status',1)->get();
        return setPageContent('invoice.new-return-invoice',$data);
    }

    public function add_return_invoice(Request $request){

        $reports = Invoice::validateReturnInvoiceProduct(json_decode($request->get('data'),true),'quantity',$request);

        if($reports['status'] == true) return response()->json(['singleError'=>$reports['singleError'],'status'=>false,'error'=>false]);

        $invoice = Invoice::ReturnInvoice($request,$reports);

        if($request->get('payment') !== "false" && $request->get('status') == 'COMPLETE'){

            $payment = Payment::createPayment(['invoice'=>$invoice,'payment_info'=>json_decode($request->get('payment'),true),"type"=>"Invoice"]);

            $invoice->payment_id = $payment->id;

            $invoice->total_amount_paid = $payment->total_paid;

            $invoice->update();

        }


        $success_view = view('invoice.return-success',['invoice_id'=> $invoice->id])->render();

        return json(['status'=>true,'html'=>$success_view]);

    }


    public function apply_invoice_discount($id, Request $request)
    {
        if($request->method() == "POST"){
            $invoice = Invoice::find($id);
            $invoice->discount_amount = $request->get('discount');
            $invoice->discount_type = "Fixed";
            $invoice->status = "DISCOUNT-APPLIED";
            $invoice->save();
            return redirect()->route("invoiceandsales.view", $invoice)->with('success', "Discount has been applied successfully!");
        }else{
            $data = [];
            $data['title'] = 'Apply Invoice Discount';
            $data['invoice'] = Invoice::with(['created_by','customer','invoice_items'])->find($id);
            return view('invoice.apply_discount',$data);
        }
    }

    public function cancel_discount($id)
    {
        $invoice = Invoice::find($id);
        $invoice->discount_amount = 0;
        $invoice->discount_type = "none";
        $invoice->status = "DRAFT";
        $invoice->save();
        return redirect()->route("invoiceandsales.view", $invoice)->with('success', "Discount has been cancel successfully!");
    }



    public function checkoutScan()
    {
        return view('invoice.scaninvoice');
    }
}
