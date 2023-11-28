<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankController extends Controller
{

    public function index(){
        $data['title'] = "List Bank";
        $data['title2'] = "Add Bank";
        $data['accounts'] = BankAccount::all();
        $data['banks'] = Bank::all();
        return setPageContent('settings.bank.list-bank',$data);
    }


    public function create(){

    }



    public function store(Request $request){

        $request->validate(BankAccount::$validate);

        $data = $request->only(BankAccount::$fields);

        $data['status'] = 1;

        BankAccount::create($data);

        return redirect()->route('bank.index')->with('success','Bank as been created successful!');
    }


    public function toggle($id){

        $this->toggleState(BankAccount::find($id));

        return redirect()->route('bank.index')->with('success','Operation successful!');
    }


    public function edit($id){
        $data['title'] = "Update Bank";
        $data['account'] = BankAccount::find($id);
        $data['banks'] = Bank::all();
        return setPageContent('settings.bank.edit',$data);
    }

    public function update(Request $request, $id){

        $request->validate(BankAccount::$validate);

        $data = $request->only(BankAccount::$fields);

        BankAccount::find($id)->update($data);

        return redirect()->route('bank.index')->with('success','Bank as been updated successful!');

    }


}
