<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response;
use App\InformasiModels\PedomanAkademik;

class PedomanAkademikController extends Controller
{
    
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        $pedoman = PedomanAkademik::orderBy('urutan','asc')->get();
        $count = 0;
        $data = [];
        foreach( $pedoman as $r ) {
            $count += 1;
            $data[] = ['id' => $r->id, 'judul' => $r->judul];
        }
        $result = ['error' => 0, 'count' => $count, 'data' => $data];
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function detail(Request $r, $id=null)
    {

        if ( empty($id) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa ditampilkan']);
        }

        $r = PedomanAkademik::find($id);

        if ( $r ) {
            $data[] = ['id' => $r->id, 'judul' => $r->judul, 'konten' => $r->konten];
            $result = ['error' => 0, 'data' => $data];
        } else {
            $result = ['error' => 1, 'msg' => 'Tidak ada data yang bisa ditampilkan'];
        }

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

}