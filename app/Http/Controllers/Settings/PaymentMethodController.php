<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use App\Models\PaymentMethod;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(){
        $data['title'] = "List Payment Method";
        $data['title2'] = "Add Payment Method";
        $data['methods'] = PaymentMethod::all();
        return setPageContent('settings.payment_method.list-payment-method',$data);
    }


    public function create(){

    }


    public function edit($id){

        $data['title'] = "Update Payment Method";

        $data['payment_method'] = PaymentMethod::find($id);

        return setPageContent('settings.payment_method.edit',$data);
    }


    public function toggle($id){

        $this->toggleState(PaymentMethod::find($id));

        return redirect()->route('payment_method.index')->with('success','Operation Successful');

    }


    public function store(Request $request){

        $request->validate(PaymentMethod::$validation);

        PaymentMethod::create($request->only(PaymentMethod::$fields));

        return redirect()->route('payment_method.index')->with('success','Operation Successful');

    }


    public function update(Request $request, $id){

        $request->validate(PaymentMethod::$validation);

        PaymentMethod::find($id)->update($request->only(PaymentMethod::$fields));

        return redirect()->route('payment_method.index')->with('success','Operation Successful');
    }

}
