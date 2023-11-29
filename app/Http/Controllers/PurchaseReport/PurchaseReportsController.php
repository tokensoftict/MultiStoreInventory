<?php

namespace App\Http\Controllers\PurchaseReport;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder as Po;
use App\Models\Supplier;
use App\Models\SupplierCreditPaymentHistory;
use App\Models\User;
use Illuminate\Http\Request;

class PurchaseReportsController extends Controller
{
    public function daily(Request $request){
        $data['date'] = $request->get('date', date('Y-m-d'));
        $data['title'] = 'Daily Purchase Orders';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->where('date_created',$data['date'])
            ->orderBy('id','DESC')->get();
        return view('purchasereport.daily', $data);
    }

    public function monthly(Request $request){

        $data['from'] = $request->get('from',  date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));

        $data['title'] = 'Monthly Purchase Orders';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->whereBetween('date_created',[$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return setPageContent('purchasereport.monthly', $data);
    }

    public function monthly_by_supplier(Request $request){

        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['supplier_id'] = $request->get('supplier_id', 1);
        $data['suppliers'] = Supplier::all();

        $data['title'] = 'Monthly Purchase Orders By Supplier';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->where('supplier_id', $data['supplier_id'])
            ->whereBetween('date_created',[$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return view('purchasereport.monthly_supplier', $data);
    }


    public function monthly_by_store(Request $request){
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['warehousestore_id'] = $request->get('warehousestore_id', getActiveStore()->id);
        $data['stores'] = getMyAccessStore('name_and_id');
        $data['title'] = 'Monthly Purchase Orders By Store';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->where('warehousestore_id',  $data['warehousestore_id'])
            ->whereBetween('date_created',[$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return view('purchasereport.monthly_store', $data);
    }


    public function monthly_by_user(Request $request){

        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['user_id'] = $request->get('user_id', $request->user()->id);

        $data['users'] = User::where('status', 1)->get();

        $data['title'] = 'Monthly Purchase Orders By User';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])
            ->whereIn('warehousestore_id', getMyAccessStore('id'))
            ->whereBetween('date_created',[$data['from'],$data['to']])
            ->where('created_by', $data['user_id'])
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
        return setPageContent('purchasereport.payment_report',$data);
    }



    public function balance_sheet(Request $request){
        $data['customers'] = Supplier::all();
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['customer_id'] = $request->get('customer_id', 1);

        $data['opening'] = SupplierCreditPaymentHistory::where('supplier_id' ,$data['customer_id'])->where('payment_date','<', $data['from'])->sum('amount');

        $data['histories'] = SupplierCreditPaymentHistory::where('supplier_id', $data['customer_id'])->whereBetween('payment_date',[ $data['from'], $data['to']])->get();

        $data['title'] = "Balance Sheet";

        return setPageContent('purchasereport.balance_sheet',$data);
    }

}
