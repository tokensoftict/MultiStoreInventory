<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\StockLogUsagesType;
use Illuminate\Http\Request;

class StockLogUsageTypeController extends Controller
{
    public function index(){
        $data['title'] = "List Stock Log Type";
        $data['title2'] = "Add Stock Log Type";
        $data['stock_log_usage_types'] = StockLogUsagesType::all();
        return setPageContent('settings.stock_log_type.list-types',$data);
    }


    public function create(){

    }



    public function store(Request $request){

        $request->validate(StockLogUsagesType::$validate);

        $data = $request->only(StockLogUsagesType::$fields);

        $data['status'] = 1;

        StockLogUsagesType::create($data);

        return redirect()->route('stock_log_usage_type.index')->with('success','Stock Type as been created successful!');
    }


    public function toggle($id){

        $this->toggleState(StockLogUsagesType::find($id));

        return redirect()->route('stock_log_usage_type.index')->with('success','Operation successful!');
    }


    public function edit($id){
        $data['title'] = "Update Expenses Type";
        $data['expenses_type'] = StockLogUsagesType::find($id);
        return setPageContent('settings.stock_log_type.edit',$data);
    }

    public function update(Request $request, $id){

        $request->validate(StockLogUsagesType::$validate);

        $data = $request->only(StockLogUsagesType::$fields);

        StockLogUsagesType::find($id)->update($data);

        return redirect()->route('stock_log_usage_type.index')->with('success','Stock Type as been updated successful!');

    }
}
