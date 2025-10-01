<?php

namespace App\Http\Controllers\IncentiveManager;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Incentive;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class IncentiveController extends Controller
{
    public function index(Request $request){

        $data['title'] = "List Incentives";

        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }

        $data['incentives'] = Incentive::where(function ($query) {
            $query->orWhere("warehousestore_id", getActiveStore()->id)
                ->orWhereNull("warehousestore_id");
        })  ->whereBetween('payment_date',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        return view('incentive.list-incentives',$data);
    }

    public function create(){
        $data['title'] = "Add Incentive";
        $data['title2'] = "Add Incentive";
        $data['payments'] = PaymentMethod::where('status',1)->where('id','<>',4)->get();
        $data['banks'] = BankAccount::where('status',1)->get();
        return view('incentive.add_incentive',$data);
    }

    public function edit($id){
        $data['title'] = "Update Incentive";
        $data['title2'] = "Update Incentive";
        $data['incentive'] = Incentive::find($id);
        return view('incentive.add_incentive',$data);
    }


    public function update(Request $request, $id)
    {
        $incentive = Incentive::find($id);
        if($incentive){
            $incentive->update($request->only($request->all()));
        }
        return redirect()->route('incentive.index')->with('success','Incentive has been updated successfully');
    }


    public function store(Request $request){
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $data['warehousestore_id'] = getActiveStore()->id;
        Incentive::create($data);
        return redirect()->route('incentive.index')->with('success','Incentive has been added successfully');
    }
}
