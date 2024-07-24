<?php

namespace App\Http\Middleware;

use Closure, Auth, Response;

class Role
{

    public function handle($request, Closure $next, $role)
    {
        if ( empty(Auth::user()) ) {
            return redirect('/login');
        }

        $level = Auth::user()->level;


		$role = explode('|', $role);

        if ( in_array($level, $role) ) {
            return $next($request);
        }

        return Response::json('Akses di tolak', 403);

    }
}
