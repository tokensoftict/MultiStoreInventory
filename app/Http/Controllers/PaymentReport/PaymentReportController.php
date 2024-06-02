<?php

namespace App\Http\Controllers\PaymentReport;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodTable;
use Illuminate\Http\Request;

class PaymentReportController extends Controller
{

    public function daily_payment_reports(Request $request)
    {
        $data['date'] = $request->get('from', dailyDate());
        $data['title'] = "Daily Payment Report";
        $data['payments'] = Payment::with(['warehousestore','customer','user','payment_method_tables','invoice'])->where('warehousestore_id',getActiveStore()->id)->where('payment_date', $data['date'])->orderBy('id','DESC')->get();
        return view('paymentreport.daily_payment_reports',$data);
    }

    public function monthly_payment_reports(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));

        $data['title'] = "Monthly Payment Report";

        $data['payments'] = Payment::with(['warehousestore','customer','user','payment_method_tables','invoice'])->where('warehousestore_id',getActiveStore()->id)->whereBetween('payment_date', [$data['from'],$data['to']])->orderBy('id','DESC')->get();
        return setPageContent('paymentreport.monthly_payment_reports',$data);
    }

    public function monthly_payment_report_by_method(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));

        $data['payment_method'] = $request->get('payment_method', 1);

        $data['payments'] = PaymentMethodTable::with(['warehousestore','payment','customer','user','payment_method','invoice'])->where('payment_method_id', $data['payment_method'])->where('warehousestore_id',getActiveStore()->id)->whereBetween('payment_date', [$data['from'],$data['to']])->orderBy('id','DESC')->get();
        $data['title'] = "Monthly Payment Report By Payment Method";
        $data['pmthods'] = PaymentMethod::all();
        return setPageContent('paymentreport.monthly_payment_reports_by_method',$data);
    }

    public function payment_analysis(Request $request)
    {

        $data['date'] = $request->get('from', dailyDate());

        $data['title'] = "Payment Analysis";

        $data['payment_methods'] = PaymentMethod::all();

        return view('paymentreport.payment_analysis',$data);
    }


    public function income_analysis(Request $request)
    {

        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));


        $data['expenses'] = Expense::with(['expenses_type','user'])->where('warehousestore_id', getActiveStore()->id)->whereBetween('expense_date',[ $data['from'], $data['to']])->orderBy('id','DESC')->get();
        $data['payments'] = PaymentMethodTable::with(['warehousestore','payment','customer','user','payment_method','invoice'])->where('warehousestore_id',getActiveStore()->id)->whereBetween('payment_date', [$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Income Analysis";
        return setPageContent('paymentreport.income_analysis',$data);
    }



    public function income_analysis_by_department(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['warehousestore_id'] = $request->get('warehousestore_id', getActiveStore()->id);
        $data['stores'] = getMyAccessStore('name_and_id');

        $data['expenses'] = Expense::with(['expenses_type','user'])->where('warehousestore_id', $data['warehousestore_id'])->whereBetween('expense_date',[ $data['from'], $data['to']])->get();
        $data['payments'] = PaymentMethodTable::with(['warehousestore','payment','customer','user','payment_method','invoice'])->where('warehousestore_id', $data['warehousestore_id'])->where('warehousestore_id',getActiveStore()->id)->whereBetween('payment_date', [$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Income Analysis By Store";
        return view('paymentreport.income_analysis_by_department',$data);
    }

}
