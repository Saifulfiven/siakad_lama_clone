<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt, Response;
use App\InformasiModels\Slide;
use DB;

class SlideController extends Controller
{
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index()
    {

        $slide = Slide::orderBy('urutan','asc')->get();

        $data = [];
        foreach( $slide as $f ) {
            $data[] = $f;
        }

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

}
