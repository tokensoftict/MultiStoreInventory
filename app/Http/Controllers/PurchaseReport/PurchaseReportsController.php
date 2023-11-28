<?php

namespace App\Http\Controllers\PurchaseReport;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder as Po;
use App\Models\Supplier;
use App\Models\SupplierCreditPaymentHistory;
use Illuminate\Http\Request;

class PurchaseReportsController extends Controller
{
    public function daily(Request $request){
        if($request->get('date')){
            $data['date'] = $request->get('date');
            $data['status'] = $request->get('status');
        }else{
            $data['date'] = date('Y-m-d');
            $data['status'] = "COMPLETE";
        }
        $data['title'] = 'Daily Purchase Orders';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])->where('date_created',$data['date'])->orderBy('id','DESC')->get();
        return setPageContent('purchaseorderlist.daily', $data);
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
        $data['title'] = 'Monthly Purchase Orders';
        $data['purchase_orders'] = Po::with(['supplier','purchase_order_items','user','created_user'])->whereBetween('date_created',[$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return setPageContent('purchaseorderlist.monthly', $data);
    }


    public function credit_report(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }

        $history = SupplierCreditPaymentHistory::where('amount','<',0)
            ->whereBetween('payment_date',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Supplier Credit Report";
        $data['histories'] = $history;
        return setPageContent('purchaseorder.credit_report',$data);
    }

    public function payment_report(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }

        $history = SupplierCreditPaymentHistory::where('amount','>',0)
            ->whereBetween('payment_date',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Supplier Payment Report";
        $data['histories'] = $history;
        return setPageContent('purchaseorder.payment_report',$data);
    }



    public function balance_sheet(Request $request){
        $data['customers'] = Supplier::all();
        $customer_id = 0;
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['customer_id'] = $request->get('customer_id');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
            if($data['customers']->first()){
                $customer_id = $data['customers']->first()->id;
            }else{
                $customer_id = 0;
            }
            $data['customer_id'] = $customer_id;
        }

        $data['opening'] = SupplierCreditPaymentHistory::where('supplier_id' ,$data['customer_id'])->where('payment_date','<', $data['from'])->sum('amount');

        $data['histories'] = SupplierCreditPaymentHistory::where('supplier_id', $data['customer_id'])->whereBetween('payment_date',[ $data['from'], $data['to']])->get();

        $data['title'] = "Balance Sheet";

        return setPageContent('purchaseorder.balance_sheet',$data);
    }

}
