<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Route;
use Auth;

class CheckRole
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
        $dcru = array(
            'dcru.index'=>'list', 
            'dcru.dt'=>'list', 
            'dcru.view'=>'detail', 
            'dcru.create'=>'create', 
            'dcru.duplicate'=>'create',
            'dcru.save'=>'create', 
            'dcru.edit'=>'edit', 
            'dcru.delete.file'=>'edit',
            'dcru.update'=>'edit',
            'dcru.delete'=>'delete', 
            'dcru.delete.all'=>'delete', 
        );
        
        $route = $request->route();
        $action = null;
        $group = null;
        if($route!=null){
            $route_name = Route::currentRouteName();
            $routes = explode('.', Route::currentRouteName());
            if(count($routes)<2){
                return $next($request);
            }
            $group = $routes[0];
            $action = $routes[1];
            
            if($group=='dcru'){
                $parameters = $route->parameters();
                $group = $parameters['name'];
            }
        }
        $user = $request->user();
        if($user!=null){
            // dd($group.'---'.$action);
            \App::setLocale($user->lang);
            if($user->hasAction($group, $action)){
                return $next($request);
            }else{
                abort(401);
            }
        }
        // if($request->currentRouteName()=='dcru.index'){
        // }
        // if (!$request->user()->hasRole($role)) {
        //     // Redirect...
        // }
        return $next($request);
    }

    public function listFeature(){
        return [
            //pok
            'keuangan.pok'=>'pok-list',
            'keuangan.pok.import'=>'pok-import',
            'keuangan.pok.upload'=>'pok-import',
            'keuangan.pok.rpd'=>'pok-rpd',
            'keuangan.pok.rpd.save'=>'pok-rpd',
            'keuangan.realisasi'=>'pok-realisasi',
            //belanja
            

            
            //belanja
            'keuangan.belanja.create'=>'belanja-create',
            'keuangan.belanja.save'=>'belanja-create',
            'keuangan.belanja.edit'=>'belanja-edit',
            'keuangan.belanja.update'=>'belanja-edit',
            'keuangan.belanja.view'=>'belanja-detail',
            //spm
            'keuangan.spm.create'=>'spm-create',
            'keuangan.spm.save'=>'spm-create',
            'keuangan.spm.edit'=>'spm-edit',
            'keuangan.spm.update'=>'spm-edit',
            'keuangan.spm.view'=>'spm-detail',

            //pst
            'pst.tamu'=>'pst-buku-tamu',
            'pst.tamu.kujungan'=>'pst-buku-tamu',
            'pst.tamu.save'=>'pst-buku-tamu',
            'pst.tamu.search'=>'pst-buku-tamu',
        ];
    }
}
