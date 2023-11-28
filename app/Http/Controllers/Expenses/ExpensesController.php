<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpensesType;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index(){
        $data['title'] = "List Expenses";
        $data['expenses'] = Expense::with(['expenses_type','user'])->where('expense_date',dailyDate())->get();
        return setPageContent('expenses.list',$data);
    }


    public function create(){
        $data['title'] = "New Expenses";
        $data['expenses_types'] = ExpensesType::all();
        $data['expenses'] = new Expense();
        return setPageContent('expenses.new',$data);
    }


    public function store(Request $request){

        $request->validate(Expense::$validate);

        $data = $request->only(Expense::$fields);

        $data['user_id'] = auth()->id();

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success','Expenses as been saved successful!');
    }



    public function edit($id){
        $data['title'] = "Update Expenses";
        $data['expenses'] = Expense::find($id);
        $data['expenses_types'] = ExpensesType::all();
        return setPageContent('expenses.new',$data);
    }

    public function update(Request $request, $id){

        $request->validate(Expense::$validate);

        $data = $request->only(Expense::$fields);

        Expense::find($id)->update($data);

        return redirect()->route('expenses.index')->with('success','Expenses as been updated successful!');

    }


    public function destroy($id){

        $ex = Expense::findorfail($id);

        $ex->delete();

        return redirect()->route('expenses_type.index')->with('success','Expenses as been deleted successful!');

    }


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


    public function expenses_report_by_department(Request $request){
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['department'] = $request->get('department');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }
        $data['title'] = "List Expenses";
        $data['expenses'] = Expense::with(['expenses_type','user'])->where('department', $data['department'])->whereBetween('expense_date',[ $data['from'], $data['to']])->get();
        return setPageContent('expenses.list_department',$data);
    }

}
