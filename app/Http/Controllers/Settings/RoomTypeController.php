<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index(){
        $data['title'] = "List Room Type";
        $data['title2'] = "Add Room Type";
        $data['room_types'] = RoomType::all();
        return setPageContent('settings.room_types.list-types',$data);
    }


    public function create(){

    }



    public function store(Request $request){

        $request->validate(RoomType::$validate);

        $data = $request->only(RoomType::$fields);

        $data['status'] = 1;

        RoomType::create($data);

        return redirect()->route('room_type.index')->with('success','Room type as been created successful!');
    }


    public function toggle($id){

        $this->toggleState(RoomType::find($id));

        return redirect()->route('room_type.index')->with('success','Operation successful!');
    }


    public function edit($id){
        $data['title'] = "Update Room Type";
        $data['room_type'] = RoomType::find($id);
        return setPageContent('settings.room_types.edit',$data);
    }

    public function update(Request $request, $id){

        $request->validate(RoomType::$validate);

        $data = $request->only(RoomType::$fields);

        RoomType::find($id)->update($data);

        return redirect()->route('room_type.index')->with('success','Room Type as been updated successful!');

    }

}
