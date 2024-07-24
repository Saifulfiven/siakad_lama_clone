<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt;
use App\InformasiModels\Galeri;
use DB, Response;

class GaleriController extends Controller
{
    private $f = 'informasi.galeri.';


    public function index(Request $r)
    {

        $data['galeri'] = Galeri::where('id_album', $r->id)->get();

        return view($this->f."index", $data);
    }

    public function create()
    {
        return view($this->f."create");
    }

    public function store(Request $r)
    {

        if ( count($r->file('files')) == 0 ) {
            return Response::json(['error' => 'File masih kosong'], 422);
        }

        // Cek ekstensi
        foreach( $r->file('files') as $f ){
            $accept = ['png','jpg','jpeg'];
            $ext = $f->getClientOriginalExtension();
            if ( !in_array($ext, $accept) ) {
                return Response::json(['error' => 'Ekstensi gambar salah'], 422);
            }
        }

        try {
            // Upload Gambar
            $destination = storage_path('galeri/'.$r->id_album);
            $gambar_arr = Rmt::UploadMultiple($r->file('files'), $destination);

            foreach( $gambar_arr as $val )
            {
                $data = new Galeri;
                $data->gambar = $val;
                $data->id_album = $r->id_album;
                $data->save();
                $sampul = $val;
            }

            DB::table('album')->where('id', $r->id_album)->update(['sampul' => 'galeri/'.$r->id_album.'/small-'.$sampul]);

        } catch( \Exception $e) {
            return Response::json(['error' => $e->getMessage()], 422);
        }

    }

    public function delete($id)
    {
        $r = Galeri::find($id);
        if ( !empty($r->gambar) && file_exists(storage_path().'/galeri/'.$r->id_album.'/'.$r->gambar) ) {
            unlink(storage_path().'/galeri/'.$r->id_album.'/'.$r->gambar);
            unlink(storage_path().'/galeri/'.$r->id_album.'/small-'.$r->gambar);
        }
        Galeri::where('id', $id)->delete();

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
