<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class LogAktivitas
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
        // $page = $request->route()->getName();
        // if ( Auth::check() && !empty($page) ) {
        //     \LogAktivitas::add($page);
        // }
        
        return $next($request); 
    }
}
