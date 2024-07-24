<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Mahasiswareg;
use Rmt, DB, Response, Sia;

trait LmsDsnController
{
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function lmsDetail(Request $r)
    {

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
        
    }

}
