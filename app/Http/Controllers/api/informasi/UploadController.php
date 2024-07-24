<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt;
use DB, Response;

class UploadController extends Controller
{
    
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function upload(Request $r)
    {

        if ( $r->hasFile('file') ) {

            if ( $r->file('file')->isValid() ) {

                $ext = $r->file('file')->getClientOriginalExtension();
                $nama = $r->file('file')->getClientOriginalName();
                $r->file('file')->move(
                                        storage_path() . "/upload/", 
                                        $nama
                                        );
                $result = ['error' => 0, 'msg' => 'Berhasil'];
            } else {
                $result = ['error' => 1, 'msg' => 'Gagal'];
            }

        } else {

            $result = ['error' => 1, 'msg' => 'Tidak ada file dipilih'];

        }
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }


}
