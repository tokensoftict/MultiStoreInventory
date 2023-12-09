<?php

namespace App\Http\Controllers\ExpensesReport;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpensesType;
use Illuminate\Http\Request;

class ExpensesReportController extends Controller
{
    public function expenses_report_by_type(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['type'] = $request->get('type');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
            $data['type'] = 1;
        }

        $data['types'] = ExpensesType::all();
        $data['title'] = "List Expenses";
        $data['expenses'] = Expense::with(['expenses_type','user'])->where('expenses_type_id', $data['type'])->whereBetween('expense_date',[ $data['from'], $data['to']])->get();
        return setPageContent('expenses.list_by_type',$data);
    }


    public function expenses_report_by_store(Request $request){

        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to',  date('Y-m-t'));
        $data['stores'] = getMyAccessStore('name_and_id');
        $data['warehousestore_id'] = $request->get('department', getActiveStore()->id);
        $data['title'] = "List Expenses";
        $data['expenses'] = Expense::with(['expenses_type','user'])->where('warehousestore_id', $data['warehousestore_id'])->whereBetween('expense_date',[ $data['from'], $data['to']])->get();
        return view('expenses.list_department',$data);
    }


    public function monthly_expenses_report(Request $request)
    {
        $data['from'] = $request->get('from', date('Y-m-01'));
        $data['to'] = $request->get('to',  date('Y-m-t'));
        $data['title'] = "Monthly Expenses Report";
        $data['expenses'] = Expense::with(['expenses_type','user'])->where('warehousestore_id', getActiveStore()->id)->whereBetween('expense_date',[ $data['from'], $data['to']])->get();
        return view('expenses.monthy_expenses_report',$data);
    }

}
