<?php

namespace App\Http\Controllers\PaymentReport;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodTable;
use App\Models\User;
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

    public function payment_report_user(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['user_id'] = $request->get('user_id', auth()->user()->id);

        $data['payments'] = Payment::with(['warehousestore','customer','user','invoice'])->where('user_id', $data['user_id'])->where('warehousestore_id',getActiveStore()->id)->whereBetween('payment_date', [$data['from'],$data['to']])->orderBy('id','DESC')->get();
        $data['title'] = "Monthly Payment Report By User";
        $data['users'] = User::where("status", "1")->get();
        return view('paymentreport.payment_method_by_user',$data);
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



    public function report_by_bank_transfer(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to', date('Y-m-t'));
        $data['warehousestore_id'] = $request->get('warehousestore_id', getActiveStore()->id);
        $data['stores'] = getMyAccessStore('name_and_id');
        $data['banks'] = BankAccount::where('status', '1')->get();

        $data['selected_bank'] = $request->get('selected_bank', 1);
        $data['bank_info'] = BankAccount::where('id', $data['selected_bank'])->first();

        $data['payments'] = PaymentMethodTable::with(['warehousestore','payment','customer','user','payment_method','invoice'])
            ->where("payment_info->bank_id",$data['selected_bank'] )
            ->where('warehousestore_id', $data['warehousestore_id'])->where('warehousestore_id',getActiveStore()->id)->whereBetween('payment_date', [$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Payment Report By Bank Transfer";
        return view('paymentreport.report_by_bank_transfer', $data);
    }



    public function income_analysis_by_cash(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-d'));
        $data['to'] = $request->get('to', date('Y-m-d'));


        $data['expenses'] = Expense::with(['expenses_type','user'])->where('warehousestore_id', getActiveStore()->id)->whereBetween('expense_date',[ $data['from'], $data['to']])->orderBy('id','DESC')->get();
        $data['payments'] = PaymentMethodTable::with(['warehousestore','payment','customer','user','payment_method','invoice'])->where('payment_method_id', 1)->where('warehousestore_id',getActiveStore()->id)->whereBetween('payment_date', [$data['from'],$data['to']])->orderBy('id','DESC')->get();

        $data['title'] = "Income Analysis By Cash";
        return view('paymentreport.income_analysis_by_cash',$data);
    }


    public function payment_analysis_by_user(Request $request)
    {

        $data['date'] = $request->get('from', dailyDate());

        $data['title'] = "Payment Analysis By User";

        $data['payment_methods'] = PaymentMethod::all();
        $data['user_id'] = $request->get('user_id', auth()->user()->id);
        $data['users'] = User::where("status", "1")->get();
        return view('paymentreport.payment_analysis_by_user',$data);
    }

}
