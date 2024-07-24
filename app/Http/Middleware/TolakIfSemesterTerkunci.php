<?php

namespace App\Http\Middleware;

use Closure,Sia;

class TolakIfSemesterTerkunci
{

    public function handle($request, Closure $next)
    {
      if ( !Sia::kunciSemester() ) {
          return $next($request);
      }

      echo "<center><h1>Akses di tolak, periode telah terkunci</h1></center>";
      exit();
    }
}
