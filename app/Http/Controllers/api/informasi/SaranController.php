<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Absen, Session, Rmt, Response;
use App\InformasiModels\Saran;
use DB;

class SaranController extends Controller
{
    
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    public function store(Request $r)
    {

        if ( empty($r->subjek ) ) {
            $fail = ['error' => 1, 'msg' => 'Subjek Masih Kosong'];
            return json_encode($fail);
        }

        if ( empty($r->saran) ) {
            $fail = ['error' => 1, 'msg' => 'Saran Masih Kosong'];
            return json_encode($fail);
        }
        
        $data = new Saran;
        $data->from = $r->from;
        $data->subjek = $r->subjek;
        $data->saran = $r->saran;
        $data->save();

        $result = ['error' => 0, 'msg' => 'Berhasil mengirim saran'];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

}
