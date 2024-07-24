<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt;
use App\InformasiModels\Album, App\InformasiModels\Galeri;
use DB, Storage;

class AlbumController extends Controller
{
    private $f = 'informasi.album.';


    public function index(Request $r)
    {

        $data['album'] = Album::orderBy('urutan','asc')->get();

        return view($this->f."index", $data);
    }

    public function urutan(Request $r)
    {
        $urutan = explode("&",$r->urutan);
        $no = 1;

        foreach( $urutan as $val ){
            $val = explode("=", $val);

            DB::table('album')->where('id', $val[1])->update(['urutan' => $no]);
            $no++;
        }
    }

    public function store(Request $r)
    {
        $this->validate($r, [
                'judul' => 'required|max:255'
            ]);

        if ( $r->id ) {
            $data = Album::find($r->id);
            $data->judul = $r->judul;
            $data->save();
        } else {
            $data = new Album;
            $data->judul        = $r->judul;
            $data->urutan       = $r->urutan;
            $data->save();
            // Storage::disk('galeri')->makeDirectory($data->id);
            mkdir(storage_path().'/galeri/'.$data->id);
        }

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('album'));
    }

    public function delete($id)
    {
        $galeri = Galeri::where('id_album', $id)->count();
        if ( $galeri > 0 ) {
            Rmt::Error('Gagal, hapus isi album dahulu');
            return redirect()->back();
        }
        
        Album::where('id', $id)->delete();
        mkdir(storage_path().'/galeri/'.$id);

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
