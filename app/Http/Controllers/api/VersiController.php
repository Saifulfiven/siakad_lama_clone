<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

class VersiController extends Controller
{

    public function index(Request $r)
    {

        if ( $r->platform == 'android' ) {
            $msg = 'Aplikasi Versi terbaru telah tersedia, segera perbarui aplikasi anda di Play Store';
            $versi = '4.2.5';
        } else {
            // IOS
            $msg = 'Aplikasi Versi terbaru telah tersedia, segera perbarui aplikasi anda di App Store';
            $versi = null;
        }

        $result = ['versi' => $versi, 'msg' => $msg, 'delay' => 7000 ];
        return Response::json($result,200);
    }
}
