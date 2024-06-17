<?php

namespace App\Http\Controllers\InvoiceReport;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ReturnLog;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class InvoiceReportController extends Controller
{

    public function daily(Request $request){
        if($request->get('date')){
            $data['date'] = $request->get('date');
            $data['status'] = $request->get('status');
        }else{
            $data['date'] = date('Y-m-d');
            $data['status'] = "COMPLETE";
        }
        $data['title'] = "Daily Invoice Report";
        $data['invoices'] = Invoice::with(['created_user','customer'])->where('warehousestore_id', getActiveStore()->id)->where('status', $data['status'])->where('invoice_date', $data['date'])->get();
        return view('invoicereport.daily',$data);
    }

    public function monthly(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['status'] = $request->get('status');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
            $data['status'] = "COMPLETE";
        }
        $data['title'] = "Monthly Invoice Report";
        $data['invoices'] = Invoice::with(['created_user','customer'])->where('warehousestore_id', getActiveStore()->id)->where('status', $data['status'])->whereBetween('invoice_date', [$data['from'],$data['to']])->get();
        return view('invoicereport.monthly',$data);
    }


    public function customer_monthly(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['customer'] = $request->get('status');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
            $data['customer'] = 1;
        }
        $data['customers'] = Customer::all();
        $data['title'] = "Monthly Customer Invoice Report";
        $data['invoices'] = Invoice::with(['created_user','customer'])->where('warehousestore_id',getActiveStore()->id)->where('customer_id', $data['customer'])->where('status', "COMPLETE")->whereBetween('invoice_date', [$data['from'],$data['to']])->get();
        return view('invoicereport.customer_monthly',$data);
    }

    public function store_monthly(Request $request){


        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['stores'] = getMyAccessStore('name_and_id');
        $data['warehousestore_id'] = $request->get('warehousestore_id', getActiveStore()->id);

        $data['customers'] = Customer::all();
        $data['title'] = "Monthly Invoice Report By Store";
        $data['invoices'] = Invoice::with(['created_user','customer'])->where('warehousestore_id',$data['warehousestore_id'])->where('status', "COMPLETE")->whereBetween('invoice_date', [$data['from'],$data['to']])->get();
        return view('invoicereport.store_monthly',$data);
    }


    public function product_monthly(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['product'] = $request->get('product');
            $data['product_name'] = Stock::find($request->get('product'))->name;
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
            $data['product'] = 1;
            $data['product_name'] = Stock::find(1)->name;
        }

        $lists = InvoiceItem::with(['invoice','customer','stock'])
            ->where('stock_id', $data['product'])
            ->whereHas('invoice',function($q) use($data){
                $q->whereBetween('invoice_date', [$data['from'],$data['to']])
                    ->where('status','COMPLETE');
            })->get();


        $data['customers'] = Customer::all();
        $data['title'] = "Product Invoice Report";
        $data['invoices'] =  $lists ;
        return view('invoicereport.product_monthly',$data);
    }


    public function sales_analysis(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['status'] =  "COMPLETE";
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
            $data['status'] = "COMPLETE";
        }

        $lists = InvoiceItem::select('stock_id',
            DB::raw( 'SUM(quantity) as total_qty'),
            DB::raw( 'SUM(quantity * (selling_price - cost_price)) as total_profit'),
            DB::raw( 'SUM(quantity * (cost_price)) as total_cost_total'),
            DB::raw( 'SUM(quantity * (selling_price)) as total_selling_price')
        )->where('warehousestore_id', getActiveStore()->id)->whereHas('invoice',function($q) use(&$data){
            $q
                ->whereBetween('invoice_date',[$data['from'],$data['to']])
                ->where(function($qq) use(&$data){
                    $qq->where("status",$data['status']);
                });
        })
            ->groupBy('stock_id')
            ->get();

        $data['title'] = "Sales Analysis";
        $data['invoices'] = $lists;
        return view('invoicereport.sales_analysis',$data);

    }


    public function return_logs(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }
        $lists = ReturnLog::whereBetween('date_added',[$data['from'],$data['to']])->where('warehousestore_id', getActiveStore()->id)->get();
        $data['title'] = "Sales Returns Report";
        $data['logs'] = $lists;
        return view('invoicereport.return_logs',$data);
    }


    public function full_invoice_report(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }
        $data['invoices'] = Invoice::with(['created_user','customer'])->where('warehousestore_id',getActiveStore()->id)->whereBetween('invoice_date', [$data['from'],$data['to']])->get();

        PDF::loadView("pdf.full_invoice_report",$data)->save(public_path('pdf/report.pdf'));

        $data['title'] = "Complete Invoice Report";
        return view('invoicereport.full_invoice_report',$data);
    }


}
