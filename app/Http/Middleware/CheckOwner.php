<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Route;
use Auth;

class CheckOwner
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
        if($user!=null){
            // dd($group.'---'.$action);
            if($user->is_owner){
                return $next($request);
            }
            abort(401);
        }
        
        return $next($request);
    }
}
