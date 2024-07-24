<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, DB, Rmt;
use App\InformasiModels\Gbpp;

class GbppController extends Controller
{
    private $f = 'informasi.gbpp.';
    private $prefix;
    
    public function __construct()
    {
        $this->prefix = 'x_';
    }

    public function index(Request $r)
    {
        if ( $r->prodi ) {
            $data['gbpp'] = Gbpp::where('prodi', $r->prodi)->orderBy('judul','asc')->paginate(20);
        } else {
            $data['gbpp'] = Gbpp::orderBy('judul','asc')->paginate(20);
        }

        return view($this->f."index", $data);
    }

    public function create()
    {
        return view($this->f."create");
    }

    public function store(Request $r)
    {
        $this->validate($r, [
                'judul' => 'required|max:255',
                'prodi' => 'required|max:255',
                'link' => 'required|max:255',
            ]);

        $data = new gbpp;
        $data->judul    = $r->judul;
        $data->prodi    = $r->prodi;
        $data->link      = $r->link;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('gbpp'));
    }

    public function edit($id)
    {
        $data['gbpp'] = Gbpp::find($id);

        return view($this->f.'edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
                'judul' => 'required|max:255',
                'prodi' => 'required|max:255',
                'link' => 'required|max:255',
            ]);

        $data = Gbpp::find($r->id);
        $data->judul    = $r->judul;
        $data->prodi    = $r->prodi;
        $data->link      = $r->link;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('gbpp'));
    }

    public function delete($id)
    {
        $val = explode(",", $id);

        foreach ($val as $v) {
            Gbpp::where('id', $v)->delete();
        }

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
