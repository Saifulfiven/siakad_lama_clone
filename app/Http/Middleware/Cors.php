<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        
//         header("Access-Control-Allow-Origin: *");

        // ALLOW OPTIONS METHOD
//         $headers = [
//             'Access-Control-Allow-Origin' => '*',
//              'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
//              'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
//         ];
        
//        if($request->getMethod() == "OPTIONS" || $request->getMethod() == "POST" ) {
//             // The client-side application can set only headers allowed in Access-Control-Allow-Headers
//             return Response::make('OK', 200, $headers);
//         }

        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin' , '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');

        return $response;

        // foreach($headers as $key => $value) 
        //     $response->header($key, $value);
        // return $response;
    }
}
