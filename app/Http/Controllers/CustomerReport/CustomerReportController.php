<?php

namespace App\Http\Controllers\CustomerReport;

use App\Http\Controllers\Controller;
use App\Models\CreditPaymentLog;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerReportController extends Controller
{
    public function credit_report(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }

        $history = CreditPaymentLog::where('amount','<',0)
            ->whereBetween('payment_date',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Customer Credit Report";
        $data['histories'] = $history;
        return setPageContent('customermanager.credit_report',$data);
    }

    public function payment_report(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }

        $history = CreditPaymentLog::where('amount','>',0)
            ->whereBetween('payment_date',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Customer Payment Report";
        $data['histories'] = $history;
        return setPageContent('customermanager.payment_report',$data);
    }



    public function balance_sheet(Request $request){
        $data['customers'] = Customer::where('id','>',1)->get();
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

        $data['opening'] = CreditPaymentLog::where('customer_id', $data['customer_id'])->where('payment_date','<', $data['from'])->sum('amount');

        $data['histories'] = CreditPaymentLog::where('customer_id', $data['customer_id'])->whereBetween('payment_date',[ $data['from'], $data['to']])->get();

        $data['title'] = "Balance Sheet";

        return setPageContent('customermanager.balance_sheet',$data);
    }

}
