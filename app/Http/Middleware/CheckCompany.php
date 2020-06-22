<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Route;
use Auth;

class CheckCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        // dd($user);
        if($user!=null){
            if($user->activeCompany()==null){
                return redirect()->route('company.register');
            }   
        }
        // if($request->currentRouteName()=='dcru.index'){
        // }
        // if (!$request->user()->hasRole($role)) {
        //     // Redirect...
        // }
        return $next($request);
    }

}
