<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ExpensesType;
use Illuminate\Http\Request;

class ExpensesTypeController extends Controller
{

    public function index(){
        $data['title'] = "List Expenses Type";
        $data['title2'] = "Add Expenses Type";
        $data['expenses_types'] = ExpensesType::all();
        return setPageContent('settings.expenses_type.list-types',$data);
    }


    public function create(){

    }



    public function store(Request $request){

        $request->validate(ExpensesType::$validate);

        $data = $request->only(ExpensesType::$fields);

        $data['status'] = 1;

        ExpensesType::create($data);

        return redirect()->route('expenses_type.index')->with('success','Expenses type as been created successful!');
    }


    public function toggle($id){

        $this->toggleState(ExpensesType::find($id));

        return redirect()->route('expenses_type.index')->with('success','Operation successful!');
    }


    public function edit($id){
        $data['title'] = "Update Expenses Type";
        $data['expenses_type'] = ExpensesType::find($id);
        return setPageContent('settings.expenses_type.edit',$data);
    }

    public function update(Request $request, $id){

        $request->validate(ExpensesType::$validate);

        $data = $request->only(ExpensesType::$fields);

        ExpensesType::find($id)->update($data);

        return redirect()->route('expenses_type.index')->with('success','Expenses Type as been updated successful!');

    }

}
