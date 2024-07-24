<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session;

class MateriPascaController extends Controller
{
	
    public function index(Request $r)
    {
        $query = DB::table('materi_kuliah_pasca as mkp')
                    ->leftJoin('matakuliah as mk', 'mkp.kode_mk', 'mk.kode_mk')
                    ->select('mkp.*','mk.nm_mk','mk.sks_mk')
                    ->groupBy('mkp.kode_mk');

        if ( Session::has('materi_search') ) {
            $query->where(function($q) {
                $q->where('mk.nm_mk', 'like', '%'.Session::get('materi_search').'%')
                ->orWhere('mkp.kode_mk', 'like', '%'.Session::get('materi_search').'%');
            });
        }

        $data['matakuliah'] = $query->paginate(10);
        return view('materi-pasca.index', $data);
    }

    public function detail(Request $r)
    {
            $data['mk'] = DB::table('matakuliah as mk')
                            ->select('mk.*')
                            ->where('mk.kode_mk', $r->kode_mk)
                            ->first();

        return view('materi-pasca.detail', $data);
    }

    public function cari(Request $r)
    {
        if ( !empty($r->q) ) {
            Session::put('materi_search',$r->q);
        } else {
            Session::pull('materi_search');
        }

        return redirect(route('materi'));
    }

    public function add(Request $r)
    {
        return view('materi-pasca.add');
    }

    public function matakuliah(Request $r)
    {
        $param = $r->input('query');
        $prodi = '61101';

        if ( !empty($param) ) {
            $matakuliah = DB::table('mk_kurikulum as mkur')
                            ->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
                            ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
                            ->select('mk.id','mk.kode_mk','mk.nm_mk','mk.sks_mk','kur.nm_kurikulum','mkur.id as id_mkur','mkur.smt')
                            // ->where('kur.aktif',1)
                            ->where('kur.id_prodi', $prodi)
                            ->where(function($q)use($param){
                                $q->where('mk.nm_mk', 'like', '%'.$param.'%')
                                ->orWhere('mk.kode_mk', 'like', '%'.$param.'%');
                            })->orderBy('mk.nm_mk','asc')->get();
        } else {
            $matakuliah = DB::table('mk_kurikulum as mkur')
                            ->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
                            ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
                            ->select('mk.id','mk.kode_mk','mk.nm_mk','mk.sks_mk','kur.nm_kurikulum','mkur.id as id_mkur','mkur.smt')
                            // ->where('kur.aktif',1)
                            ->where('kur.id_prodi', $prodi)->get();
        }
        $data = [];
        foreach( $matakuliah as $r ) {
            $data[] = ['data' => $r->kode_mk, 'value' => $r->kode_mk.' - '.trim($r->nm_mk).' ('.$r->sks_mk.' sks)', 'id_mkur' => $r->id_mkur];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'judul'  => 'required',
            'matakuliah'  => 'required'
        ]);

        try {
            if ( $r->hasFile('file') ) {
                $name = $r->file->getClientOriginalName();

                $destinationPath = 'E:\G-drive\file-siakad\materi-pasca/'.$r->matakuliah;
                $r->file->move($destinationPath, $name);

                $data = [
                    'judul' => $r->judul,
                    'kode_mk' => $r->matakuliah,
                    'file_materi' => $name
                ];

                DB::table('materi_kuliah_pasca')
                ->insert($data);

                Rmt::success('Berhasil menyimpan data');
                return Response::json(['error' => 0, 'msg' => 'ok']);

            } else {
                return Response::json('Tidak ada file', 422);
            }
        } catch( \Exception $e ) {
            return Response::json($e->getMessage(), 422);
        }
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'judul'  => 'required'
        ]);

        try {
                $data = ['judul' => $r->judul];

                DB::table('materi_kuliah_pasca')
                ->where('id', $r->id)
                ->update($data);

                Rmt::success('Berhasil menyimpan data');
                return redirect()->back();

        } catch( \Exception $e ) {
            Rmt::error('Gagal menyimpan: '.$e->getMessage());
            return redirect()->back();
        }
    }

    public function delete(Request $r)
    {
        $materi = DB::table('materi_kuliah_pasca')
                    ->where('id', $r->id)
                    ->first();

        $file = 'E:\G-drive\file-siakad\materi-pasca/'.$materi->kode_mk.'/'.$materi->file_materi;
        if ( file_exists($file) ) {
            unlink($file);
        }

        DB::table('materi_kuliah_pasca')
                    ->where('id', $r->id)
                    ->delete();

        Rmt::success('Berhasil menghapus data');
        return redirect()->back();
    }

    public function materiPascaDownload(Request $r, $id, $file)
    {

        $materi = DB::table('materi_kuliah_pasca')
                ->where('id', $id)
                ->first();

        if ( !empty($materi) ) {
            
            $file_materi = !empty($materi->file_materi) ? $materi->file_materi : 'undefined';
            $path = config('app.materi-pasca');
            $file = $path.'/'.trim($materi->kode_mk).'/'.$file_materi;

            if ( file_exists($file) ) {
                return Response::file($file);
            } else {
                echo "<center><h4>File tidak ditemukan</h4></center>";
            }

        } else {
            echo 'Tidak ada data';
        }
    }
}
