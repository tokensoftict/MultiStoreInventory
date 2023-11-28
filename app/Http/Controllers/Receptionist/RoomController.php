<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(){
        $data['title'] = "List Rooms";
        $data['rooms'] = Room::all();
        $data['types'] = RoomType::where('status',1)->get();
        return setPageContent('receptionist.rooms.list-rooms',$data);
    }


    public function create(){
        $data['title'] = "Create New Room";
        $data['types'] = RoomType::where('status',1)->get();
        $data['room'] = new Room();
        return setPageContent('receptionist.rooms.form',$data);
    }


    public function edit($id){
        $data['title'] = "Update Room";
        $data['types'] = RoomType::where('status',1)->get();
        $data['room'] = Room::findorfail($id);
        return setPageContent('receptionist.rooms.form',$data);
    }



    public function store(Request $request){

        $request->validate(Room::$validation);

        Room::create($request->only(Room::$fields));

        return redirect()->route('room.index')->with('success','Operation Successful');

    }


    public function update(Request $request, $id){
        $request->validate(Room::$validation);
        Room::find($id)->update($request->only(Room::$fields));
        return redirect()->route('room.index')->with('success','Operation Successful');
    }


    public function destroy($id){
        Room::find($id)->delete();
        return redirect()->route('room.index')->with('success','Operation Successful');
    }

}
