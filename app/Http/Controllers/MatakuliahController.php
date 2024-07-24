<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use App\Matakuliah;
use DB, Sia, Rmt, Excel, Response, Session;

class MatakuliahController extends Controller
{
    public function index(Request $r)
    {
        $query = Sia::matakuliah();
        
        $data['matakuliah'] = $query->paginate(10);
        return view('matakuliah.index', $data);
    }

    public function detail($id)
    {
        $data['mk'] = DB::table('matakuliah as mk')
                        ->leftJoin('prodi as pr', 'mk.id_prodi', '=', 'mk.id_prodi')
                        ->leftJoin('konsentrasi as kon', 'mk.id_konsentrasi','=', 'kon.id_konsentrasi')
                        ->select('mk.*','pr.nm_prodi','kon.nm_konsentrasi',
                                    DB::raw('(select count(id_mk) from '.Sia::prefix().'jadwal_kuliah
                                        where id_mk='.Sia::prefix().'mk.id) as terpakai'))
                        ->where('mk.id', $id)
                        ->first();

        if ( !empty($data['mk']->mk_terganti) ) {
            $mk_terganti = DB::table('matakuliah')
                            ->where('id', $data['mk']->mk_terganti)->first();
        }
        $data['mk_terganti'] = empty($mk_terganti) ? '' : $mk_terganti->nm_mk;

        return view('matakuliah.detail', $data);
    }

    public function cari(Request $r)
    {
        if ( !empty($r->q) ) {
            Session::put('mk_search',$r->q);
        } else {
            Session::pull('mk_search');
        }

        return redirect(route('matakuliah'));
    }

    public function filter(Request $r)
    {
        if ( $r->ajax() ) {
            Sia::filter($r->value,'mk_'.$r->modul);
        } else {
            Session::pull('mk_search_');
            Session::pull('mk_prodi');
            Session::pull('mk_jenis');
            Session::pull('mk_kelompok');
        }
        
        return redirect(route('matakuliah'));
    }

    public function eksporPrint(Request $r)
    {
        $query = Sia::matakuliah();

        $data['matakuliah'] = $query->get();
        return view('matakuliah.print', $data);
    }

    public function eksporExcel(Request $r)
    {
        $query = Sia::matakuliah();
        $data['matakuliah'] = $query->get();

        try {
            Excel::create('Matakuliah', function($excel)use($data) {

                $excel->sheet('New sheet', function($sheet)use($data) {

                    $sheet->loadView('matakuliah.excel', $data);

                });

            })->download('xlsx');;
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function add(Request $r)
    {
        return view('matakuliah.add');
    }

    public function matakuliah(Request $r)
    {
        $param = $r->input('query');
        if ( !empty($param) ) {
            $matakuliah = DB::table('mk_kurikulum as mkur')
                            ->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
                            ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
                            ->select('mk.id','mk.kode_mk','mk.nm_mk','mk.sks_mk','kur.nm_kurikulum','mkur.id as id_mkur','mkur.smt')
                            // ->where('kur.aktif',1)
                            ->where('kur.id_prodi', $r->prodi)
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
                            ->where('kur.id_prodi', $r->prodi)->get();
        }
        $data = [];
        foreach( $matakuliah as $r ) {
            $data[] = ['data' => $r->id, 'value' => $r->kode_mk.' - '.trim($r->nm_mk).' ('.$r->sks_mk.' sks) - '.$r->nm_kurikulum.' - smstr '.$r->smt, 'id_mkur' => $r->id_mkur];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'kode_matakuliah' => 'required',
            'nama_matakuliah'   => 'required',
            'kelompok_mk'   => 'required',
            'prodi' => 'required',
            'jenis_mk' => 'required',
            'sks_tm' => 'numeric',
            'sks_prak' => 'numeric',
            'sks_prak_lap' => 'numeric',
            'sks_sim' => 'numeric',
        ]);

        // Cari kode matakuliah yang sama
        $count = Matakuliah::where('kode_mk', $r->kode_matakuliah)->count();

        if ( $count > 0 ) {
            return Response::json(['error' => 1, 'msg' => 'Kode matakuliah ini telah ada'],200);
        }

        // Cek apakah total sks tidak kosong
        $tot_sks = (int)$r->sks_tm + (int)$r->sks_prak + (int)$r->sks_prak_lap + (int)$r->sks_sim;
        if ( $tot_sks < 1 ) {
            return Response::json(['error' => 1, 'msg' => 'Jumlah total sks masih 0']);
        }

        try {

            DB::transaction(function()use($r){
                $mk = new Matakuliah;
                $mk->id = Rmt::uuid();
                $mk->mk_terganti = $r->mk_terganti;
                $mk->id_prodi = $r->prodi;
                $mk->kode_mk = $r->kode_matakuliah;
                $mk->nm_mk = $r->nama_matakuliah;
                $mk->jenis_mk = $r->jenis_mk;
                $mk->ujian_akhir = $r->ujian_akhir;
                $mk->id_jenis_bayar = $r->jenis_bayar;
                $mk->id_konsentrasi = empty($r->konsentrasi) ? NULL : $r->konsentrasi; 
                $mk->kelompok_mk = empty($r->kelompok_mk) ? NULL : $r->kelompok_mk; 
                $mk->sks_mk = (int)$r->sks_tm + (int)$r->sks_prak + (int)$r->sks_prak_lap + (int)$r->sks_sim; 
                $mk->sks_tm = empty($r->sks_tm) ? 0 : (int)$r->sks_tm; 
                $mk->sks_prak = empty($r->sks_prak) ? 0 : (int)$r->sks_prak; 
                $mk->sks_prak_lap = empty($r->sks_prak_lap) ? 0 : (int)$r->sks_prak_lap; 
                $mk->sks_sim = empty($r->sks_sim) ? 0 : (int)$r->sks_sim; 
                $mk->a_sap = $r->a_sap; 
                $mk->a_silabus = $r->a_silabus; 
                $mk->a_bahan_ajar = $r->a_bahan_ajar; 
                $mk->acara_praktek = $r->acara_praktek; 
                $mk->a_diktat = $r->a_diktat; 
                $mk->tgl_mulai_efektif = empty($r->tgl_mulai_efektif) ? NULL : Rmt::formatTgl($r->tgl_mulai_efektif, 'Y-m-d'); 
                $mk->tgl_akhir_efektif = empty($r->tgl_akhir_efektif) ? NULL : Rmt::formatTgl($r->tgl_akhir_efektif, 'Y-m-d'); 
                $mk->save();
            });

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        Rmt::success('Berhasil menyimpan data');

        return Response::json(['error' => 0,'msg' => 'sukses'], 200);
    }

    public function edit($id)
    {
        $data['mk'] = Matakuliah::find($id);
        if ( !empty($data['mk']->mk_terganti) ) {
            $mk_terganti = DB::table('matakuliah')->where('id', $data['mk']->mk_terganti)->first();
        }
        $data['mk_terganti'] = empty($mk_terganti) ? '' : $mk_terganti->nm_mk;

        $rule = DB::table('jadwal_kuliah')->where('id_mk', $id)->count();

        // if ( !Sia::admin() ) {

        //     if ( $rule > 0 ) {
        //         echo "<center><h3>Matakuliah tidak bisa diubah jika telah terpakai di jadwal kuliah</h3></center>";
        //         exit; 
        //     }

        // }

        return view('matakuliah.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'kode_matakuliah' => 'required',
            'nama_matakuliah'   => 'required',
            'kelompok_mk'   => 'required',
            'prodi' => 'required',
            'jenis_mk' => 'required',
            'sks_tm' => 'numeric',
            'sks_prak' => 'numeric',
            'sks_prak_lap' => 'numeric',
            'sks_sim' => 'numeric',
        ]);

        // Cari kode matakuliah yang sama
        $count = Matakuliah::where('kode_mk', $r->kode_matakuliah)
                    ->where('id','<>',$r->id)->count();
        if ( $count > 0 ) {
            return Response::json(['error' => 1, 'msg' => 'Kode matakuliah ini telah ada'],200);
        }

        // Cek apakah total sks tidak kosong
        $tot_sks = (int)$r->sks_tm + (int)$r->sks_prak + (int)$r->sks_prak_lap + (int)$r->sks_sim;
        if ( $tot_sks < 1 ) {
            return Response::json(['error' => 1, 'msg' => 'Jumlah total sks masih 0']);
        }

        try {

            DB::transaction(function()use($r){
                $mk = Matakuliah::find($r->id);
                $mk->mk_terganti = $r->mk_terganti;
                $mk->id_prodi = $r->prodi;
                $mk->kode_mk = $r->kode_matakuliah;
                $mk->nm_mk = $r->nama_matakuliah;
                $mk->jenis_mk = $r->jenis_mk;
                $mk->ujian_akhir = $r->ujian_akhir;
                $mk->id_jenis_bayar = $r->jenis_bayar;
                $mk->kelompok_mk = empty($r->kelompok_mk) ? NULL : $r->kelompok_mk;
                $mk->id_konsentrasi = $r->konsentrasi;
                $mk->sks_mk = (int)$r->sks_tm + (int)$r->sks_prak + (int)$r->sks_prak_lap + (int)$r->sks_sim;
                $mk->sks_tm = empty($r->sks_tm) ? 0 : (int)$r->sks_tm;
                $mk->sks_prak = empty($r->sks_prak) ? 0 : (int)$r->sks_prak;
                $mk->sks_prak_lap = empty($r->sks_prak_lap) ? 0 : (int)$r->sks_prak_lap;
                $mk->sks_sim = empty($r->sks_sim) ? 0 : (int)$r->sks_sim;
                $mk->a_sap = $r->a_sap;
                $mk->a_silabus = $r->a_silabus;
                $mk->a_bahan_ajar = $r->a_bahan_ajar;
                $mk->acara_praktek = $r->acara_praktek;
                $mk->a_diktat = $r->a_diktat;
                $mk->tgl_mulai_efektif = empty($r->tgl_mulai_efektif) ? NULL : Rmt::formatTgl($r->tgl_mulai_efektif, 'Y-m-d');
                $mk->tgl_akhir_efektif = empty($r->tgl_akhir_efektif) ? NULL : Rmt::formatTgl($r->tgl_akhir_efektif, 'Y-m-d');
                $mk->save();
            });

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        Rmt::success('Berhasil mengubah data');
        return Response::json(['error' => 0,'msg' => 'sukses'], 200);
    }

    public function delete($id)
    {
        $count_on_kurikulum = DB::table('mk_kurikulum')->where('id_mk', $id)->count();
        if ( $count_on_kurikulum > 0 ) {
            Rmt::error('Gagal menghapus, matakuliah ini terpakai pada kurikulum');
            return redirect()->back();
        }

        $count_on_jadwal = DB::table('jadwal_kuliah')->where('id_mk',$id)->count();
        if ( $count_on_jadwal > 0 ) {
            Rmt::error('Gagal menghapus, matakuliah ini terpakai pada jadwal kuliah');
            return redirect()->back();
        }

        Matakuliah::find($id)->delete();
        Rmt::success('Berhasil Menghapus data');
        return redirect()->back();
    }
}
