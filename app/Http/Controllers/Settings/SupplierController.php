<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    public function index(){
        $data['title'] = "List Supplier(s)";
        $data['title2'] = "Add Supplier";
        $data['suppliers'] = Supplier::all();
        return setPageContent('settings.supplier.list-supplier',$data);
    }


    public function create(){

    }



    public function store(Request $request){

        $request->validate(Supplier::$validate);

        $data = $request->only(Supplier::$fields);

        $data['status'] = 1;

        Supplier::create($data);

        return redirect()->route('supplier.index')->with('success','Supplier as been created successful!');
    }


    public function toggle($id){

        $this->toggleState(Supplier::find($id));

        return redirect()->route('supplier.index')->with('success','Operation successful!');
    }


    public function edit($id){
        $data['title'] = "Update Supplier";
        $data['supplier'] = Supplier::find($id);
        return setPageContent('settings.supplier.edit',$data);
    }

    public function update(Request $request, $id){

        $request->validate(Supplier::$validate);

        $data = $request->only(Supplier::$fields);

        Supplier::find($id)->update($data);

        return redirect()->route('supplier.index')->with('success','Supplier as been updated successful!');

    }


}
