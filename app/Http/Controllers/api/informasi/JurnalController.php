<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response, DB, Rmt;
use App\InformasiModels\Jurnal;

class JurnalController extends Controller
{
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        if ( $r->q ) {
            $jurnal = Jurnal::where('judul','like', '%'. $r->q .'%')->take(20)->get();
        } else {
            $jurnal = Jurnal::orderBy('judul','asc')->get();
        }

        $data = [];
        foreach( $jurnal as $r ) {
            $data[] = $r;
        }
        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }
}