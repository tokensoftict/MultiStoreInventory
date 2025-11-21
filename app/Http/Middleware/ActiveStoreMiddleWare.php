<?php

namespace App\Http\Middleware;

use App\Models\Warehousestore;
use Closure;
use Illuminate\Http\Request;

class ActiveStoreMiddleWare
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
        if(!session()->has('activeStore')){
            return redirect()->route('select-store');
        }

        $store = session()->get('activeStore');

        $ware = Warehousestore::query()->find($store['id']);

        if($ware->status == "0" || $ware->status == false) {
            session()->forget('activeStore');

            if(\request()->user()->activeuserstoremappers->count() > 0) {
                return redirect()->route('select-store')->with('message', $ware->name." is currently not available");
            }

            auth()->logout();

            return redirect()->route('login')->with('message', $ware->name." is currently not available");
        }

        return $next($request);
    }
}
