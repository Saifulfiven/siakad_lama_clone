<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, DB, Rmt;
use App\InformasiModels\KalenderAkademik;

class KalenderAkademikController extends Controller
{
    private $f = 'informasi.kalender.';
    private $prefix;
    
    public function __construct()
    {
        $this->prefix = env('DB_TABLE_PREFIX');
    }

    public function index(Request $r)
    {
        $kat = !$r->kat ? 1 : $r->kat;

        $data['kalender'] = KalenderAkademik::where('kategori', $kat)->orderBy('urutan','asc')->paginate(50);

        return view($this->f."index", $data);
    }

    public function order(Request $r)
    {
        $urutan = explode("&",$r->order);
        $no = 1;

        foreach( $urutan as $val ){
            $val = explode("=", $val);

            DB::table('x_kalender_akademik')->where('id', $val[1])->update(['urutan' => $no]);
            $no++;
        }
    }

    public function create()
    {
        return view($this->f."create");
    }

    public function store(Request $r)
    {
        $this->validate($r, [
                'deskripsi' => 'required|max:255',
                'tanggal' => 'required',
                'kategori' => 'required',
            ]);

        $data = new KalenderAkademik;
        $data->deskripsi= $r->deskripsi;
        $data->kategori = $r->kategori;
        $data->tanggal  = $r->tanggal;
        $data->urutan    = $r->order;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('kalender', ['kat' => $r->kategori]));
    }

    public function edit($id)
    {
        $data['ka'] = KalenderAkademik::find($id);

        return view($this->f.'edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
                'deskripsi' => 'required|max:255',
                'tanggal' => 'required',
                'kategori' => 'required',
            ]);
        
        $data = KalenderAkademik::find($r->id);
        $data->deskripsi= $r->deskripsi;
        $data->kategori = $r->kategori;
        $data->tanggal  = $r->tanggal;
        $data->urutan    = $r->order;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('kalender', ['kat' => $r->kategori]));
    }

    public function delete($id)
    {
        $val = explode(",", $id);

        foreach ($val as $v) {
            KalenderAkademik::where('id', $v)->delete();
        }

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
