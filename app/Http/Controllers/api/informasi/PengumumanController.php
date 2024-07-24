<?php

namespace App\Http\Controllers\api\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Session, Rmt;
use App\InformasiModels\Pengumuman;
use DB, Response;

class PengumumanController extends Controller
{
    
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        
        $pengumuman = Pengumuman::where('kategori', $r->level)->orderBy('created_at','desc')->paginate(20);

        if ( $pengumuman->total() > 0 ) {
            foreach( $pengumuman as $p ) {
                $data[] = ['id' => $p->id, 'judul' => $p->judul, 'deskripsi' => $p->deskripsi, 'tanggal' => Rmt::format_tgl($p->created_at)];
            }
        } else {
            $data = [];
        }

        $result = ['error' => 0, 'count' => $pengumuman->total(), 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function detail(Request $r, $id)
    {

        $pengumuman = Pengumuman::find($id);

        if ( $pengumuman ) {
            $data = ['id' => $pengumuman->id, 'judul' => $pengumuman->judul, 'konten' => $pengumuman->konten, 'tanggal' => Rmt::format_tgl($pengumuman->created_at)];
        } else {
            $data = [];
        }

        $result = ['error' => 0, 'count' => count($data), 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

}
