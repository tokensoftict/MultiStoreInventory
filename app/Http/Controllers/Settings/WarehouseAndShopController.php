<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Supplier;
use App\Models\Warehousestore;
use Illuminate\Http\Request;

class WarehouseAndShopController extends Controller
{

    public function index(){
        $data['title'] = "List WareHouse & Shop";
        $data['title2'] = "New WareHouse or Shop";
        $data['warehouse_and_shops'] = Warehousestore::all();
        return setPageContent('settings.warehouse_and_shop.list-warehouse_and_shop',$data);
    }


    public function create(){

    }



    public function store(Request $request){

        $request->validate(Warehousestore::$validate);

        $data = $request->only(Warehousestore::$field);

        $data['status'] = 1;

        Warehousestore::create($data);

        return redirect()->route('warehouse_and_shop.index')->with('success',$data['type'].' as been created successful!');
    }


    public function toggle($id){

        $shop = Warehousestore::find($id);

        $shop->default = "0";
        $shop->update();

        $this->toggleState($shop);

        return redirect()->route('warehouse_and_shop.index')->with('success','Operation successful!');
    }


    public function edit($id){
        $data['title'] = "Update Warehouse Or Shop";
        $data['warehouse_and_shop'] = Warehousestore::find($id);
        return setPageContent('settings.warehouse_and_shop.edit',$data);
    }

    public function update(Request $request, $id){

        $validate =  Warehousestore::$update;

        $validate['name'] = "required|unique:warehousestores,name,".$id;

        $request->validate($validate);

        $data = $request->only(Warehousestore::$field);

        Warehousestore::find($id)->update($data);

        return redirect()->route('warehouse_and_shop.index')->with('success','Data as been updated successful!');

    }


    public function set_as_default($id){

        $shops = Warehousestore::all();

        foreach ($shops as $shop){

            $shop->default = "0";
            $shop->update();

        }

        Warehousestore::find($id)->toggleDefault();

        return redirect()->route('warehouse_and_shop.index')->with('success','Data as been updated successful!');
    }


}
