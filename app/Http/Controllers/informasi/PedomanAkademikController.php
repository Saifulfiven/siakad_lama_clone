<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, DB, Rmt;
use App\InformasiModels\PedomanAkademik;

class PedomanAkademikController extends Controller
{
    private $f = 'informasi.pedoman.';
    private $prefix;
    
    public function __construct()
    {
        $this->prefix = env('DB_TABLE_PREFIX');
    }

    public function index(Request $r)
    {

        $data['pedoman'] = PedomanAkademik::orderBy('urutan','asc')->get();

        return view($this->f."index", $data);
    }

    public function order(Request $r)
    {
        $urutan = explode("&",$r->order);
        $no = 1;

        foreach( $urutan as $val ){
            $val = explode("=", $val);

            DB::table('x_pedoman_akademik')->where('id', $val[1])->update(['urutan' => $no]);
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
                'judul' => 'required|max:255',
                'konten' => 'required',
            ]);

        $data = new PedomanAkademik;
        $data->judul = $r->judul;
        $data->konten = $r->konten;
        $data->urutan    = $r->order;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('pedoman_create', ['order' => $r->order + 1]));
    }

    public function edit($id)
    {
        $data['ped'] = PedomanAkademik::find($id);

        return view($this->f.'edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
                'judul' => 'required|max:255',
                'konten' => 'required',
            ]);
        
        $data = PedomanAkademik::find($r->id);
        $data->konten= $r->konten;
        $data->judul = $r->judul;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('pedoman'));
    }

    public function delete($id)
    {
        $val = explode(",", $id);

        foreach ($val as $v) {
            PedomanAkademik::where('id', $v)->delete();
        }

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
