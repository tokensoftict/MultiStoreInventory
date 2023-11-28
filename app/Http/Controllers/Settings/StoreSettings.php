<?php

namespace App\Http\Controllers\Settings;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Store;
use Illuminate\Http\Request;

class StoreSettings extends Controller
{
    protected $settings;

    public function __construct(Settings $_settings){
        $this->settings = $_settings;
    }


    public function show(){
        $data['store']=  $this->settings->store();
        $data['title'] = "Store Settings";
        return setPageContent('settings.storesettings.settings',$data);
    }


    public function update(Request $request){

        $store = $this->settings->all();

        $file = $request->file('logo');

        $val = Settings::$validation;

        if($file){
            $val['logo'] = 'mimes:jpeg,jpg,png,gif|required|max:10000';
        }

        $request->validate($val);

        $data = $request->except(['_token','_method']);

        if ($file) {
            $imageName = time().'.'. $request->logo->getClientOriginalExtension();

            $request->logo->move(public_path('img'), $imageName);

            $data['logo'] = $imageName;


            if(!empty($store->logo)) {
                @unlink(public_path('img/' . $store->logo));
            }
        }

        $this->settings->put($data);

        return redirect()->route('store_settings.view')->with('success','Settings updated successful!');
    }

}
