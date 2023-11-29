<?php

namespace App\Http\Controllers\Expenses;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpensesType;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index(){
        $data['title'] = "List Today's Expenses";
        $data['expenses'] = Expense::with(['expenses_type','user'])->where('expense_date',dailyDate())->get();
        return view('expenses.list',$data);
    }


    public function create(){
        $data['title'] = "New Expenses";
        $data['expenses_types'] = ExpensesType::all();
        $data['expenses'] = new Expense();
        $data['stores'] = getMyAccessStore('name_and_id');
        return view('expenses.new',$data);
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
        $data['stores'] = getMyAccessStore('name_and_id');
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


}
