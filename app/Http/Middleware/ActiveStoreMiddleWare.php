<?php

namespace App\Http\Middleware;

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

        return $next($request);
    }
}
