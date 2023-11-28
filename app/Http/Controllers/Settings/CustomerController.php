<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(){
        $data['title'] = "List Customer";
        $data['title2'] = "Add Customer";
        $data['customers'] = Customer::all();
        return setPageContent('customermanager.list-customer',$data);
    }

    public function create(){

    }

    public function store(Request $request){

        $request->validate(Customer::$validate);

        Customer::create($request->only(Customer::$fields));

        return redirect()->route('customer.index')->with('success','Customer added successfully');
    }

    public function edit($id){

        $data['title'] = "Update Customer";
        $data['customer'] = Customer::find($id);

        return setPageContent('customermanager.edit',$data);
    }


    public function update(Request $request, $id){

        $request->validate(Customer::$validate);

        $customer = Customer::find($id);

        $customer->update($request->only(Customer::$fields));

        return redirect()->route('customer.index')->with('success','Customer updated successfully');
    }

}
