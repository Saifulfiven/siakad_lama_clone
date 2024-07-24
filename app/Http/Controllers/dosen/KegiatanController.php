<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Sia, Rmt, Response, Session, Zipper, Carbon;
use App\Dosen, App\KegiatanDosen;

class KegiatanController extends Controller
{

    public function index(Request $r)
    {
        $id_dosen = Sia::sessionDsn();

        $data['dosen'] = Dosen::findOrFail($id_dosen); 
        $kegiatan = KegiatanDosen::where('id_dosen', $id_dosen)
                            ->orderBy('created_at', 'desc')
                            ->orderBy('tahun', 'desc');

        $this->applyFilter($kegiatan);

        $data['kegiatan'] = $kegiatan->paginate(20);

        $data['tahun'] = KegiatanDosen::where('id_dosen', $id_dosen)
                        ->select('tahun')
                        ->groupBy('tahun')
                        ->orderBy('tahun','desc')
                        ->get();

        $data['semester'] = KegiatanDosen::where('id_dosen', $id_dosen)
                            ->where('smt','<>','')
                            ->groupBy('smt')
                            ->get();

        return view('dsn.kegiatan.index', $data);
    }

    private function applyFilter($kegiatan)
    {
        if ( Session::has('kegiatan.tahun') ) {
            $kegiatan->where('tahun', Session::get('kegiatan.tahun'));
        }
        if ( Session::has('kegiatan.smt') ) {
            $kegiatan->where('smt', Session::get('kegiatan.smt'));
        }

        if ( Session::has('kegiatan.kategori') ) {
            $kegiatan->where('id_kategori', Session::get('kegiatan.kategori'));
        }

        if ( Session::has('kegiatan.cari') ) {
            $kegiatan->where('nama_kegiatan', 'like', '%'.Session::get('kegiatan.cari').'%');
        }
    }

    public function filter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('kegiatan.'.$r->modul);
            } else {
                Session::put('kegiatan.'.$r->modul,$r->val);
            }
        }

        if ( $r->filter_cari ) {
            if ( !empty($r->cari) ) {
                Session::put('kegiatan.cari', $r->cari);
            } else {
                Session::pull('kegiatan.cari');
            }
        }

        if ( $r->remove ) {
            Session::pull('kegiatan');
        }

        return redirect()->back();
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'kategori' => 'required',
            'nama_kegiatan' => 'required',
            'smt' => 'required',
            'tanggal_kegiatan' => 'required',
        ]);

        $tgl_kegiatan = Carbon::parse($r->tanggal_kegiatan);

        try {

            $id_dosen = Sia::sessionDsn();

            $data = new KegiatanDosen;
            $data->id_kategori = $r->kategori;
            $data->nama_kegiatan = $r->nama_kegiatan;
            $data->smt = $r->smt;
            $data->id_dosen = $id_dosen;
            $data->tgl_kegiatan = $tgl_kegiatan->format('Y-m-d');
            $data->tahun = $tgl_kegiatan->format('Y');
            // $data->file = $this->uploadFile($r->file, $data->tahun, $r->kategori);
            $data->save();

            Rmt::success('Berhasil menyimpan data');

        } catch(\Exception $e) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    private function uploadFile($file, $tahun, $kategori)
    {
        $nama_file = $tahun.'-'.$kategori.'-'.$file->getClientOriginalName();
        $path = config('app.kegiatan-dosen').'/'.Sia::sessionDsn();
        $file->move($path, $nama_file);

        return $nama_file;
    }

    public function edit($id)
    {
        $data['kegiatan'] = KegiatanDosen::where('id_dosen', Sia::sessionDsn())
                            ->where('id', $id)->firstOrFail();

        return view('dsn.kegiatan.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'kategori' => 'required',
            'nama_kegiatan' => 'required',
            'smt' => 'required',
            'tanggal_kegiatan' => 'required'
        ]);

        $tgl_kegiatan = Carbon::parse($r->tanggal_kegiatan);

        try {

            $data = KegiatanDosen::findOrFail($r->id);
            $data->id_kategori = $r->kategori;
            $data->nama_kegiatan = $r->nama_kegiatan;
            $data->smt = $r->smt;
            $data->tgl_kegiatan = $tgl_kegiatan->format('Y-m-d');
            $data->tahun = $tgl_kegiatan->format('Y');

            if ( $r->hasFile('file') ) {
                $path = config('app.kegiatan-dosen').'/'.Sia::sessionDsn().'/'.$data->file;
                if ( file_exists($path) ) {
                    unlink($path);
                }

                $data->file = $this->uploadFile($r->file, $data->tahun, $r->kategori);
            }

            $data->save();

            Rmt::success('Berhasil menyimpan data');

        } catch(\Exception $e) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function viewDok($id, $id_dosen, $file)
    {
        $kegiatan = KegiatanDosen::where('id', $id)
                            ->where('id_dosen', $id_dosen)
                            ->firstOrFail();

        $path = config('app.kegiatan-dosen');
        $file = $path.'/'.$kegiatan->id_dosen.'/'.$kegiatan->file;
        
        if ( file_exists($file) ) {
            return Response::file($file);
        } else {
            echo "<center><h4>File tidak ditemukan</h4></center>";
        }

    }

    public function download(Request $r, $id_dosen)
    {
        $dosen = Dosen::findOrFail($id_dosen);

        try {

            $txt = config('app.kegiatan-dosen').'/petunjuk.txt';
            $files = config('app.kegiatan-dosen').'/'.$r->id_dosen;
            $fileTmp = storage_path('tmp').'/'.$dosen->nm_dosen.'.zip';
            Zipper::make($fileTmp)->folder($dosen->nm_dosen)->add($files)->add($txt)->close();
            
            return Response::download($fileTmp)->deleteFileAfterSend(true);

        } catch(\Exception $e){
            Rmt::error('Gagal mendownload data: '.$e->getMessage());
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        $kegiatan = KegiatanDosen::findOrFail($id);

        $path = config('app.kegiatan-dosen');
        $file = $path.'/'.$kegiatan->id_dosen.'/'.$kegiatan->file;

        if ( file_exists($file) ) {
            unlink($file);
        }

        $kegiatan->delete();

        Rmt::success('Berhasil menghapus data');

        return redirect()->back();

    }
}
