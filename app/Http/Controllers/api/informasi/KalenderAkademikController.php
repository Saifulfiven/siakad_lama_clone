<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response;
use App\InformasiModels\KalenderAkademik;

class KalenderAkademikController extends Controller
{
    
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {

        // Kategori
        // 1. Magister
        // 2. S1


        $role = 'dosen';
        $url_s1 = '';
        $url_s2 = '';

        $kalender = KalenderAkademik::get();

        foreach( $kalender as $kal ) {

            if ( $kal->kategori == 1 ) {
                $url_s2 = $kal->deskripsi;
            } elseif ( $kal->kategori == 2 ) {
                $url_s1 = $kal->deskripsi;
            }

        }

        $data = [
            's1' => $url_s1,
            's2' => $url_s2
        ];

        $result = ['error' => 0, 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

}
