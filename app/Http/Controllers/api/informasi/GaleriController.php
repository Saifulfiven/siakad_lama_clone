<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt;
use App\InformasiModels\Galeri, App\InformasiModels\Album;
use DB, Response;

class GaleriController extends Controller
{
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        $data = [];
        $album = Album::orderBy('urutan', 'asc')->get();
        foreach( $album as $r ) {
            $galeri = Galeri::where('id_album', $r->id)->count();
            $data[] = ['id' => $r->id, 'judul' => $r->judul, 'sampul' => $r->sampul, 'count' => $galeri ];
        }

        $result = ['error' => 0, 'count' => count($data), 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
        
    }

    public function galeri(Request $r)
    {
        $galeri = Galeri::where('id_album', $r->id)->get();
        $data = [];
        foreach( $galeri as $r ) {
            $data[] = $r;
        }

        $result = ['error' => 0, 'count' => count($data), 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }
}
