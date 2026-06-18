<?php

namespace App\Http\Controllers\AccessControl;

use App\Models\Warehousestore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Models\Group;
use App\Models\User;
use App\Http\Controllers\Controller;


class UserController extends Controller
{

    public function index()
    {
        if(auth()->user()->group_id == 1) {
            $users = User::with(['price_categories', 'userstoremappers'])->get();
        }else{
            $users = User::with(['price_categories', 'userstoremappers'])->where('group_id', '>', 1)->get();
        }
        $data['title'] = "System Users";
        $data['users'] = $users;
        return view('user.list-users', $data);
    }


    public function create()
    {
        $data['title'] = "Add new System User";
        $data['groups'] = Group::where('status', '1')->get(['id', 'name']);
        $data['user'] = new User();
        $data['stores'] = Warehousestore::where('status', 1)->get();
        $data['mystores'] = \request()->user()->userstoremappers->pluck('warehousestore_id')->toArray();
        
        $settings = app(\App\Classes\Settings::class);
        $data['active_price_categories'] = [];
        if ($settings->store()->allow_dynamic_pricing ?? false) {
            $data['active_price_categories'] = \App\Models\PriceCategory::where('status', 1)->get();
        }
        $data['my_price_categories'] = [];

        return view('user.add-user', $data);
    }


    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, User::$rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                $res['msg'] = $validator->errors()->toJson();
                return response()->json($res);
            }
            $request->flash();
            return back()->withInput()->withErrors($validator);
        }

        $data['status'] = 1;



        $res['status'] = false;
        $res['msgtype'] = 'error';
        $res['msg'] = "There was an error creating User";

        $userdata = $data;
        unset($userdata['_token']);

        $userdata['password'] = bcrypt( $userdata['password']);

        DB::transaction(function () use ($data, $userdata, &$res, &$request){
            $new_user = new User();
            unset($userdata['store'],$userdata['undefined'],$userdata['price_categories']);

            $new_user = $new_user->updateOrCreate($userdata);

            $id = $new_user->id;
            $userStore = $request->get('store', []);

            $userStore = collect($userStore)->map(function($store) use(&$id){
                return [
                    'user_id' => $id,
                    'warehousestore_id' => $store
                ];
            })->toArray();

            $new_user->userstoremappers()->delete();

            $new_user->userstoremappers()->createMany($userStore);

            $priceCategories = $request->get('price_categories', []);
            $new_user->price_categories()->sync($priceCategories);

            $res['status'] = true;
            $res['msgtype'] = 'success';
            $res['msg'] = "User created successfully";
        });

        if ($request->ajax()) {
            return response()->json($res);
        } else {
            return redirect()->route('user.create')->with('success', 'User added successfully!');

        }
    }


    public function edit($id)
    {
        $user = User::with(['userstoremappers', 'price_categories'])->findorfail($id);
        $data['title'] = "Edit System User";
        $data['groups'] = Group::where('status', '1')->get(['id', 'name']);
        $data['user'] = $user;
        $data['stores'] = Warehousestore::where('status', 1)->get();
        $data['mystores'] = $user->userstoremappers->pluck('warehousestore_id')->toArray();
        
        $settings = app(\App\Classes\Settings::class);
        $data['active_price_categories'] = [];
        if ($settings->store()->allow_dynamic_pricing ?? false) {
            $data['active_price_categories'] = \App\Models\PriceCategory::where('status', 1)->get();
        }
        $data['my_price_categories'] = $user->price_categories->pluck('id')->toArray();

        return view('user.add-user', $data);
    }



    public function update(Request $request, $id)
    {
        $res['status'] = false;
        $res['msgtype'] = 'error';
        $res['msg'] = "There was an error updating User";

        $data = $request->all();

        $validator = Validator::make($data, User::$rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                $res['msg'] = $validator->errors()->toJson();
                return response()->json($res);
            }
            $request->flash();
            return back()->withInput()->withErrors($validator);
        }

        $userdata = $data;
        unset($userdata['_token']);
        unset($userdata['_method']);

        $userStore = $request->get('store', []);

        unset($userdata['store'], $userdata['price_categories']);

        if(empty($request->password)){
            unset($userdata['password']);
        }else{
            $userdata['password'] = bcrypt( $userdata['password']);
        }


        DB::transaction(function () use ($data, $id,$userdata, &$res, &$userStore, &$request) {
            $new_user = User::find($id);
            $new_user->update($userdata);

            $userStore = collect($userStore)->map(function($store) use(&$id){
                return [
                    'user_id' => $id,
                    'warehousestore_id' => $store
                ];
            })->toArray();

            $new_user->userstoremappers()->delete();

            $new_user->userstoremappers()->createMany($userStore);

            $priceCategories = $request->get('price_categories', []);
            $new_user->price_categories()->sync($priceCategories);

            $res['status'] = true;
            $res['msgtype'] = 'success';
            $res['msg'] = "User updated successfully";
        });

        if ($request->ajax()) {
            return response()->json($res);
        } else {
            return redirect()->route('user.index')->with('success', 'User updated successfully!');
        }

    }


    public function toggle($id)
    {
        $res = parent::toggleState(User::find($id));
        if ($res->status == 1)
            return redirect()->route('user.index')->with('success', "User activated successfully");
        else
            return redirect()->route('user.index')->with('error', "User deactivated successfully");
    }


}
