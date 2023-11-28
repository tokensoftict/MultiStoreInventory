<?php

namespace App\Http\Controllers;

use App\Classes\Settings;
use App\Models\User;
use App\Models\Warehousestore;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    protected $settings;

    public function __construct(Settings $_settings)
    {

        $this->settings = $_settings;
    }


    public function index(){
        if(auth()->check())   return redirect()->route('dashboard');
        return view('login');
    }

    public function process_login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->except(['_token']);

        if (auth()->attempt($credentials)) {
            $stores = $request->user()->userstoremappers()->get();

            if($stores->count() === 0){
                // no stores assign to this user
                return redirect()->route('no-store');
            }

            if($stores->count() > 1){
                return redirect()->route('select-store');
            }

            if($stores->count() === 1){
                session()->put('activeStore', $stores->first()->warehousestore->toArray());
                return redirect()->route('dashboard');
            }

        }else{
            session()->flash('message', 'Invalid Username or Password, Please check and try again');
            return redirect()->back();
        }
    }


    public function logout(){
        auth()->logout();
        return redirect()->route('home');
    }


    public function myprofile(Request $request)
    {
        if(!auth()->check())   return redirect()->route('home');

        $data['title'] = "My Profile";

        $data['user'] = auth()->user();

        if($request->method() == "POST")
        {
            $user = $request->only(User::$profile_fields);

            if(!empty($data['password']))
            {
                $user['password'] = bcrypt($user);
            }else
            {
                unset($user['password']);
            }

            $data['user']->update($user);

            return redirect()->route('myprofile')->with('success','Profile has been updated successfully!');
        }

        return setPageContent('myprofile',$data);
    }


    public function select_store()
    {
        $data = [];
        $data['stores'] = \request()->user()->userstoremappers;
        return view('select_store',$data);
    }


    public function selected_store(Warehousestore $warehousestore)
    {
        session()->put('activeStore', $warehousestore->toArray());
        return redirect()->route('dashboard');
    }

}
