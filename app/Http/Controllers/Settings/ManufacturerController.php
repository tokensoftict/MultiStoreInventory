<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function index(){
        $data['title'] = "List Manufacturer";
        $data['title2'] = "Add Manufacturer";
        $data['categories'] = Manufacturer::all();
        return setPageContent('settings.manufacturer.list-manufacturer',$data);
    }


    public function create(){

    }


    public function edit($id){

        $data['title'] = "Update Manufacturer";

        $data['category'] = Manufacturer::find($id);

        return setPageContent('settings.manufacturer.edit',$data);
    }


    public function toggle($id){

        $this->toggleState(Manufacturer::find($id));

        return redirect()->route('manufacturer.index')->with('success','Operation Successful');

    }


    public function store(Request $request){

        $request->validate(Manufacturer::$validation);

        Manufacturer::create($request->only(Manufacturer::$fields));

        return redirect()->route('manufacturer.index')->with('success','Operation Successful');

    }


    public function update(Request $request, $id){

        $request->validate(Manufacturer::$validation);

        Manufacturer::find($id)->update($request->only(Manufacturer::$fields));

        return redirect()->route('manufacturer.index')->with('success','Operation Successful');
    }

}
