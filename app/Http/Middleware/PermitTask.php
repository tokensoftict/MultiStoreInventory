<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PermitTask
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {


        $myAccess =  $request->user()->group;

        if (!$myAccess->status) abort('403', "Unable to locate your Permission Group");

        if(!userCanView(Route::currentRouteName())){
            abort("403", "You do not have permission to perform this operation");
        }

        $validTask = $myAccess->permissions()->whereHas('task', function ($q) {
            $q->whereRoute(Route::currentRouteName());
        })->first();

        if($validTask){
            abort("403", "System error, Please contact Administrator");
        }

        $page_name = $validTask->task->name." page";


        $causedID = $request->user()->id;
        if(session()->get('past_page') != Route::currentRouteName() && $request->user()->group_id !=1)
        {
            session()->put('past_page',Route::currentRouteName());

            activity(Route::currentRouteName())
                ->causedBy($causedID)
                ->withProperties([
                    'ip' => request()->ip(),
                    'allheaders' => getallheaders()])
                ->log($request->user()->name . ' accessed ' . $page_name);

        }
        if (!$validTask) abort('403','Access Denied');//throw new UplException('access_denied');

        $request->user()->update(['last_activity' => Carbon::now()->toDateTimeString()]);

        return $next($request);
    }
}
