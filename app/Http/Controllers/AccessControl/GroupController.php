<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class GroupController extends Controller
{

    public function index()
    {
        if(auth()->user()->group_id == 1){
            $data['groups'] = Group::all();
        }else {
            $data['groups'] = Group::where('id', '>', 1)->get();
        }
        return setPageContent('access-control.group-controller',$data);
    }

    public function create()
    {
        $data['title'] = "Add User Group";
        return setPageContent('access-control.add-user-group', $data);
    }

    public function store(Request $request)
    {
        $res['status'] = false;
        $res['msgtype'] = 'error';
        $res['msg'] = "There was an error creating User group";

        $data = $request->all();

        // if the validator fails, redirect back to the form
        $validator = Validator::make($data, Group::$rules);

        if ($validator->fails()) {
            $request->flash();
            return back()->withInput()->withErrors($validator);
        }

        $data['status'] = "1";
        $group = new Group();
        $group = $group->create($data);
        if ($group) {
            $res['status'] = true;
            $res['msgtype'] = 'success';
            $res['msg'] = "User created successfully";
        }

        return redirect()->route('user.group.create')->with($res['msgtype'], $res['msg']);
    }

    public function show($id)
    {
        $data['title'] = "View User Group";
        $data['group'] = Group::findOrFail($id);
        return setPageContent('access-control.view-user-group', $data)->layout('layouts.base');
    }

    public function edit($id)
    {
        $data['title'] = "Edit User Group Details";
        $data['group'] = Group::findOrFail($id);
        return setPageContent('access-control.edit-user-group', $data);
    }


    public function update(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, Group::$rules_update);
        if ($validator->fails()) {
            $request->flash();
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $group = Group::findOrFail($id);
        $group = $group->update($data);
        if ($group)
            return redirect()->route('user.group.show', [$id])->with('success', 'User Group updated successfully');
        else
            return redirect()->route('user.group.show', [$id])->with('error', 'There was an error updating User Group');
    }


    public function destroy($id)
    {
        //
    }

    public function permission(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $request->session()->flash('error', 'Privileges was not assigned successfully');
            if (!empty($postData['privileges'])) {
                $grpassign = $this->assignGroupPrivileges($postData);
               return  !empty($grpassign) ? redirect()->back()->with('success','Privileges assigned successfully') : redirect()->back()->with('error','Privileges was not assigned successfully');
            }
        }

        $group = Group::find($id);

        //---- get all modules and tasks that belong to a group and that have been assigned too.
        $modules = Module::where('status', '=', '1')
            ->with(['tasks','tasks.permissions' => function ($q) use ($id) {
                $q->where('group_id', '=', $id);
            }])
            ->get(['id', 'name','label' ,'icon']);

        $data['title'] = "Assign Privileges to User Group (" . $group->name . ")";
        $data['modules'] = $modules;
        $data['group'] = $group;
        return setPageContent('access-control.permission-user-group', $data);
    }

    public function fetch_task($id)
    {

    }

    public function toggle($id)
    {
        $res = parent::toggleState(Group::find($id));
        if ($res->status == 1)
            return redirect()->back()->with('success', "Group activated successfully");
        else
            return redirect()->back()->with('error', "Group deactivated successfully");
    }


    private   function assignGroupPrivileges($data){
        $group = Group::find($data['group_id']);
        $values = [];
        foreach($data['privileges'] as $key => $val){
            array_push($values,$key);
        }
        return $group->group_tasks()->sync($values);
    }

}
