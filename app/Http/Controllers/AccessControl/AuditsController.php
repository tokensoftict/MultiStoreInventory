<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditsController extends Controller
{
    public function index(Request $request)
    {

        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
        }

        $data['title'] = "User Activity Log";

        $data['activities'] = Activity::whereBetween('created_at',[$data['from'],$data['to']])->orderBy('id','DESC')->get();

        return setPageContent('access-control.audit_log', $data);
    }
}
