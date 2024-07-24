<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB, Sia, Rmt, Response, Session, Auth, File, Carbon;
use App\DokumenMhs, App\Mahasiswa;

trait MahasiswaDokumen
{

    public function dokumenStore(Request $r)
    {

        try {
            if ( $r->hasFile('file') ) {

                $name = $r->file->getClientOriginalName();

                $destinationPath = config('app.mhs-files').'/'.$r->id_mhs;
                // $destinationPath = config('app.mhs-files');
                $r->file->move($destinationPath, $name);

                $data = new DokumenMhs;
                $data->id_mhs = $r->id_mhs;
                $data->judul = Rmt::removeExtensi($name);
                $data->file = $name;
                $data->save();

                Rmt::success('Berhasil menyimpan dokumen');
                return Response::json(['id' => $data->id]);

            } else {
                return Response::json('Tidak ada file', 422);
            }
        } catch( \Exception $e ) {
            return Response::json($e->getMessage(), 422);
        }
    }

    public function dokumenDownload(Request $r)
    {
        try {

            $data = DokumenMhs::findOrFail($r->id);
            $file = config('app.mhs-files').'/'.$data->id_mhs.'/'.$data->file;
            
            return Response::download($file);

        } catch(\Exception $e){
            Rmt::error('Gagal mendownload data: '.$e->getMessage());
            return redirect()->back();
        }

    }

    public function dokumenDelete(Request $r, $id)
    {
        $data = DokumenMhs::findOrFail($id);

        $file = config('app.mhs-files').'/'.$data->id_mhs.'/'.$data->file;
        if ( file_exists($file) ) {
            unlink($file);
        }

        $data->delete();

        Rmt::success('Berhasil Menghapus dokumen');

        $previousUrl = app('url')->previous();

        return redirect()->to($previousUrl.'?'. http_build_query(['tab_aktif'=>'doc']));

        // return redirect(route('mahasiswa_detail', ['id' => $r->id_mhs]).'?tab_aktif=doc');
    }

    public function dokumentEdit(Request $r)
    {

    }
}