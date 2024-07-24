<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt;
use App\InformasiModels\Slide;
use DB;

class SlideController extends Controller
{
    private $f = 'informasi.slide.';

    public function index()
    {

        $data['slide'] = Slide::orderBy('urutan','asc')->get();

        return view($this->f."index", $data);
    }

    public function order(Request $r)
    {
        $order = explode("&",$r->order);
        $no = 1;

        foreach( $order as $val ){
            $val = explode("=", $val);

            DB::table('slide')->where('id', $val[1])->update(['urutan' => $no]);
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
                'keterangan' => 'required|max:255'
            ]);

        if ( $r->hasFile('gambar') ) {
            $nm_gambar = Rmt::uploadGambar($r->keterangan, $r->file('gambar'),'slide');
            if ( !$nm_gambar ) {
                Rmt::Error('Gagal mengupload gambar');
                return redirect()->back()->withInput();
            }
        } else {
            Rmt::Error('Gambar masih kosong');
            return redirect()->back()->withInput();
        }

        $data = new Slide;
        $data->ket        = $r->keterangan;
        $data->gambar       = $nm_gambar;
        $data->urutan       = $r->order;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('slide'));
    }

    public function edit(Request $r)
    {
        $data['r'] = Slide::find($r->id);

        return view($this->f.'edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
                'keterangan' => 'required',
            ]);

        $data = Slide::find($r->id);

        if ( $r->hasFile('gambar') ) {
            $nm_gambar = Rmt::uploadGambar($r->keterangan, $r->file('gambar'),'slide');
            if ( !$nm_gambar ) {
                Rmt::Error('Gagal mengupload gambar');
                return redirect()->back()->withInput();
            }

            $data->gambar = $nm_gambar;

            if ( file_exists(storage_path().'/slide/'.$r->gambar_lama) ) {
                unlink(storage_path().'/slide/'.$r->gambar_lama);
            }
        }

        $data->ket    = $r->keterangan;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('slide'));
    }

    public function delete($id)
    {
        $data = Slide::find($id);
        if ( !empty($data->gambar) && file_exists(storage_path().'/slide/'.$data->gambar) ) {
            unlink(storage_path().'/slide/'.$data->gambar);
        }
        Slide::where('id', $id)->delete();

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
