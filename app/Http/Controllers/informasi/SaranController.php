<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Absen, Session, Rmt, Response;
use App\InformasiModels\Saran;
use DB;

class SaranController extends Controller
{
    private $f = 'informasi.saran.';
    private $prefix;
    
    public function __construct()
    {
        $this->prefix = env('DB_TABLE_PREFIX');
    }

    public function index()
    {
        $data['saran'] = Saran::orderBy('id', 'desc')->paginate(20);

        return view($this->f.'index', $data);
    }

    public function delete($id)
    {
        $val = explode(",", $id);

        foreach ($val as $v) {
            Saran::where('id', $v)->delete();
        }

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
