<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, DB, Rmt;
use App\InformasiModels\Literatur;

class LiteraturController extends Controller
{
    private $f = 'informasi.literatur.';
    private $prefix;
    
    public function __construct()
    {
        $this->prefix = env('DB_TABLE_PREFIX');
    }

    public function index(Request $r)
    {

        $data['literatur'] = Literatur::orderBy('judul','asc')->paginate(20);

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
                'dosen' => 'required|max:255',
                'url' => 'required|max:255',
            ]);

        $data = new Literatur;
        $data->judul    = $r->judul;
        $data->dosen    = $r->dosen;
        $data->url      = $r->url;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('literatur'));
    }

    public function edit($id)
    {
        $data['lite'] = Literatur::find($id);

        return view($this->f.'edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
                'judul' => 'required|max:255',
                'dosen' => 'required|max:255',
                'url' => 'required|max:255',
            ]);

        $data = Literatur::find($r->id);
        $data->judul    = $r->judul;
        $data->dosen    = $r->dosen;
        $data->url      = $r->url;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('literatur'));
    }

    public function delete($id)
    {
        $val = explode(",", $id);

        foreach ($val as $v) {
            Literatur::where('id', $v)->delete();
        }

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
