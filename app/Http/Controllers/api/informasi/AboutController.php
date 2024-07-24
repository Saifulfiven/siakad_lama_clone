<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt;
use App\InformasiModels\About, App\InformasiModels\Faspeta;
use DB, Response;

class AboutController extends Controller
{

    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

/* Profil */
    public function profil(Request $r)
    {

        $r = About::where('key', 'profil')->first();
        $result = ['error' => 0, 'data' => $r->value];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

/* Visi Misi */
    public function visi(Request $r)
    {

        $r = About::where('key', 'visi_sarjana')->first();
        $r2 = About::where('key', 'visi_pascasarjana')->first();

        $data = ['sarjana' => $r->value, 'pascasarjana' => $r2->value];
        $result = ['error' => 0, 'data' => $data ];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }


/* Keunggulan */
    public function keunggulan(Request $r)
    {

        $r = About::where('key', 'keunggulan')->first();
        $result = ['error' => 0, 'data' => $r->value];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

/* Prodi */
    public function prodi(Request $r)
    {

        $r = About::where('key', 'prodi_sarjana')->first();
        $r2 = About::where('key', 'prodi_pascasarjana')->first();

        $data = ['sarjana' => $r->value, 'pascasarjana' => $r2->value];
        $result = ['error' => 0, 'data' => $data ];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }


/* Fasilitas */
    public function fasilitas(Request $r)
    {

        $fasilitas = Faspeta::where('jenis','fasilitas')->orderBy('urutan','asc')->get();

        $data = [];
        foreach( $fasilitas as $f ) {
            $data[] = $f;
        }

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

/* Peta */
    public function peta(Request $r)
    {

        $peta = Faspeta::where('jenis','peta')->orderBy('urutan','asc')->get();

        $data = [];
        foreach( $peta as $f ) {
            $data[] = $f;
        }

        $result = ['error' => 0, 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }
}