<?php

namespace App\Http\Controllers\PurchaseReport;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder as Po;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\SupplierCreditPaymentHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReportsController extends Controller
{
    public function general_purchase_order(Request $request){
        $data['from'] = $request->get('from',  date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['title'] = 'General Purchase Orders / Returns Report';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereBetween('date_created',[$data['from'],$data['to']])
            ->orderBy('id','DESC')->get();
        return view('purchasereport.general', $data);
    }

    public function daily(Request $request){
        $data['date'] = $request->get('date', date('Y-m-d'));
        $data['type'] = $request->get('type', 'PURCHASE');
        $data['title'] = 'Daily Purchase Orders / Returns';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->where('date_created',$data['date'])
            ->where('type', $data['type'])
            ->orderBy('id','DESC')->get();
        return view('purchasereport.daily', $data);
    }

    public function monthly(Request $request){

        $data['from'] = $request->get('from',  date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['type'] = $request->get('type', 'PURCHASE');

        $data['title'] = 'Monthly Purchase Orders / Returns';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->where('type', $data['type'])
            ->whereBetween('date_created',[$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return view('purchasereport.monthly', $data);
    }

    public function monthly_by_supplier(Request $request){

        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['type'] = $request->get('type','PURCHASE');

        $data['supplier_id'] = $request->get('supplier_id', 1);
        $data['suppliers'] = Supplier::all();

        $data['title'] = 'Monthly Purchase Orders / Returns By Supplier';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->where('supplier_id', $data['supplier_id'])
            ->where('type',  $data['type'])
            ->whereBetween('date_created',[$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return view('purchasereport.monthly_supplier', $data);
    }


    public function monthly_by_store(Request $request){
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['type'] = $request->get('type', 'PURCHASE');

        $data['warehousestore_id'] = $request->get('warehousestore_id', getActiveStore()->id);
        $data['stores'] = getMyAccessStore('name_and_id');
        $data['title'] = 'Monthly Purchase Orders / Returns By Store';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->where('warehousestore_id',  $data['warehousestore_id'])
            ->where('type',  $data['type'])
            ->whereBetween('date_created',[$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return view('purchasereport.monthly_store', $data);
    }


    public function monthly_by_user(Request $request){

        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['user_id'] = $request->get('user_id', $request->user()->id);
        $data['type'] = $request->get('type', 'PURCHASE');

        $data['users'] = User::where('status', 1)->get();

        $data['title'] = 'Monthly Purchase Orders / Returns By User';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->whereBetween('date_created',[$data['from'],$data['to']])
            ->where('created_by', $data['user_id'])
            ->where('type',  $data['type'])
            ->orderBy('id','DESC')->get();
        return view('purchasereport.monthly_user', $data);
    }


    public function credit_report(Request $request){

        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));

        $history = SupplierCreditPaymentHistory::where('amount','<',0)
            ->whereBetween('payment_date',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Supplier Credit Report";
        $data['histories'] = $history;
        return view('purchasereport.credit_report',$data);
    }

    public function payment_report(Request $request){
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));

        $history = SupplierCreditPaymentHistory::where('amount','>',0)
            ->whereBetween('payment_date',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Supplier Payment Report";
        $data['histories'] = $history;
        return view('purchasereport.payment_report',$data);
    }



    public function balance_sheet(Request $request){
        $data['customers'] = Supplier::where('status', 1)->get();
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['customer_id'] = $request->get('customer_id', 1);

        $data['opening'] = SupplierCreditPaymentHistory::where('supplier_id' ,$data['customer_id'])->where('payment_date','<', $data['from'])->sum('amount');

        $data['histories'] = SupplierCreditPaymentHistory::where('supplier_id', $data['customer_id'])->whereBetween('payment_date',[ $data['from'], $data['to']])->get();

        $data['title'] = "Balance Sheet";

        return view('purchasereport.balance_sheet',$data);
    }

    public function monthly_product(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['type'] = $request->get('type', 'PURCHASE');
        $data['status'] = "COMPLETE";
        $data['title'] = "Purchase Orders / Returns Analysis Report";

        $data['datas'] =  PurchaseOrderItem::select('stock_id',
            DB::raw( 'SUM(qty) as total_qty'),
            DB::raw( 'SUM(qty * (cost_price)) as total_cost_total'),
            DB::raw( 'SUM(qty * (selling_price)) as total_selling_price')
        )->whereHas('purchase_order',function($q) use(&$data){
            $q->whereBetween('date_created',[$data['from'],$data['to']])
                ->where("status",$data['status'])
                ->where('type', $data['type'])
                ->where('warehousestore_id', getActiveStore()->id);
        })->groupBy('stock_id')
            ->get();

        return view('purchasereport.monthly_product',$data);
    }

    public function monthly_by_product(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['type'] = $request->get('type', 'PURCHASE');
        $data['status'] = "COMPLETE";
        $data['title'] = "Monthly Purchase Orders / Returns By Product";
        $data['product'] = $request->get('product', 1);
        $data['product_name'] = Stock::find($data['product'])->name;



        $data['datas'] = PurchaseOrderItem::with(['purchase_order.supplier','user', 'stock'])
            ->whereHas('purchase_order', function($query) use($data){
                $query->where('warehousestore_id', getActiveStore()->id)
                    ->where('stock_id', $data['product'])
                    ->where('status', $data['status'])
                    ->where('type', $data['type'])
                    ->whereBetween('date_created',[$data['from'],$data['to']]);
            })
            ->orderBy('id','DESC')->get();

        return view('purchasereport.monthly_by_product',$data);
    }


}
