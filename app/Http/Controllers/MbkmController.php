<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, Carbon, Excel;
use App\AktivitasKm, App\AnggotaKm, App\PembimbingKm, App\Dosen, App\NilaiMbkm;
use Illuminate\Support\Facades\Auth;

class MbkmController extends Controller
{
    public function index(Request $r)
    {
        $aktivitas = DB::table('km_aktivitas as ka')
                        ->leftJoin('km_jenis_aktivitas as ja', 'ka.id_jenis_aktivitas', 'ja.id')
                        ->leftJoin('prodi as pr', 'pr.id_prodi', 'ka.id_prodi')
                        ->leftJoin('km_anggota_aktivitas as aak', 'ka.id', 'aak.id_aktivitas')
                        ->leftJoin('mahasiswa_reg as m1', 'm1.id', 'aak.id_mhs_reg')
                        ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                        ->leftJoin('km_pembimbing_aktivitas as pa', 'pa.id_aktivitas', 'ka.id')
                        ->leftJoin('dosen as d', 'd.id', 'pa.id_dosen')
                        ->select('ka.*', 'pr.nm_prodi', 'pr.jenjang','ja.nm_aktivitas');
        
        $this->filtering($aktivitas);

        $data['aktivitas'] = $aktivitas->groupBy('ka.id')->paginate(15);

        return view('aktivitas-mbkm.index', $data);
    }

    public function filtering($query)
    {
        if ( Session::has('mbkm_ta') ) {
            $query->whereIn('ka.id_smt', Session::get('mbkm_ta'));
        }

        if ( Session::has('mbkm_prodi') ) {
            $query->whereIn('ka.id_prodi', Session::get('mbkm_prodi'));
        }

        if ( Session::has('mbkm_jenis') ) {
            $query->whereIn('ka.id_jenis_aktivitas', Session::get('mbkm_jenis'));
        }

        if ( Session::has('mbkm_search') ) {
            $query->where(function($q){
                $q->where('ka.judul_aktivitas', 'LIKE', '%'.Session::get('mbkm_search').'%')
                    ->orWhere('ka.keterangan', 'LIKE', '%'.Session::get('mbkm_search').'%')
                    ->orWhere('m1.nim', 'LIKE', '%'.Session::get('mbkm_search').'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.Session::get('mbkm_search').'%')
                    ->orWhere('d.nm_dosen', 'LIKE', '%'.Session::get('mbkm_search').'%');
            });
        }
    }

    public function filter(Request $r)
    {
        if ( $r->ajax() ) {
            Sia::filter($r->value,'mbkm_'.$r->modul);
        } else {
            Session::pull('mbkm_ta');
            Session::pull('mbkm_prodi');
            Session::pull('mbkm_jenis');
        }
        
        return redirect(route('mbkm'));
    }

    public function cari(Request $r)
    {
        if ( !empty($r->q) ) {
            Session::put('mbkm_search',$r->q);
        } else {
            Session::pull('mbkm_search');
        }

        return redirect(route('mbkm'));
    }

    public function add(Request $r)
    {
        $data['jenis_aktivitas'] = DB::table('km_jenis_aktivitas')->get();

        return view('aktivitas-mbkm.add', $data);
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'prodi' => 'required',
            'jenis_aktivitas' => 'required',
            'judul' => 'required'
        ]);

        try {

            $data = new AktivitasKm;
            $data->id_smt = Sia::sessionPeriode();
            $data->id_prodi = $r->prodi;
            $data->id_jenis_aktivitas = $r->jenis_aktivitas;
            $data->judul_aktivitas = $r->judul;
            $data->lokasi = $r->lokasi;
            $data->no_sk = $r->sk_tugas;
            
            if ( !empty($data->tgl_sk) ) {
                $data->tgl_sk = $r->tgl_sk;
            }

            if ( $r->jenis_aktivitas == 99 ) {
                $data->jenis_pertukaran = $r->jenis_pertukaran;
            }

            $data->jenis_anggota = $r->jenis_anggota;
            $data->keterangan = $r->keterangan;
            $data->save();

            Rmt::success('Berhasil menyimpan data');

         } 
         catch(\Exception $e)
         {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 422);
         }

         Rmt::success('Berhasil menyimpan data');
         return Response::json(['error' => 0, 'id' => $data->id], 200);
    }

    public function detail($id)
    {
        $data['mb'] = DB::table('km_aktivitas as ka')
                        ->leftJoin('km_jenis_aktivitas as ja', 'ka.id_jenis_aktivitas', 'ja.id')
                        ->leftJoin('prodi as pr', 'pr.id_prodi', 'ka.id_prodi')
                        ->leftJoin('semester as smt', 'ka.id_smt', 'smt.id_smt')
                        ->select('ka.*', 'pr.nm_prodi', 'pr.jenjang', 'smt.nm_smt','ja.nm_aktivitas')
                        ->where('ka.id', $id)
                        ->first();

        $data['peserta'] = AnggotaKm::where('id_aktivitas', $id)->get();

        $data['dosen'] = DB::table('km_pembimbing_aktivitas as pa')
                        ->leftJoin('km_jenis_pembimbing as jp', 'pa.id_jenis_pembimbing', 'jp.id')
                        ->leftJoin('dosen as d', 'd.id', 'pa.id_dosen')
                        ->select('pa.*', 'd.gelar_depan', 'd.gelar_belakang', 'd.nm_dosen', 'jp.nm_kategori')
                        ->where('pa.id_aktivitas', $id)
                        ->get();
        
        if ( !Session::has('tab') ) {
            Session::put('tab', 'peserta');
        }

        return view('aktivitas-mbkm.detail', $data);
    }

    public function mhs(Request $r )
    {
        $param = $r->input('query');
        if ( !empty($param) ) {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('m1.id_prodi', Sia::getProdiUser())
                            ->where(function($q)use($param){
                                $q->where('m1.nim', 'like', '%'.$param.'%')
                                    ->orWhere('m2.nm_mhs', 'like', '%'.$param.'%');
                            })
                            ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
        } else {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('m1.id_prodi', Sia::getProdiUser())
                            ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
        }

        $data = [];
        foreach( $mahasiswa as $r ) {
            $data[] = ['data' => $r->id, 'value' => $r->nim.' - '.$r->nm_mhs];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function storePeserta(Request $r)
    {
        try {

            $data = new AnggotaKm;
            $data->id_mhs_reg = $r->mahasiswa;
            $data->id_aktivitas = $r->id_aktivitas;
            $data->jenis_peran = $r->peran;
            $data->save();

            Session::put('tab', 'peserta');
            Rmt::success('Berhasil menyimpan data');

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function deletePeserta($id)
    {
        AnggotaKm::find($id)->delete();

        Session::put('tab', 'peserta');

        Rmt::success('Berhasil menghapus peserta');

        return redirect()->back();
    }

    public function dosen(Request $r)
    {
        $param = $r->input('query');
        if ( !empty($param) ) {
            $dosen = Dosen::where('aktif',1)
                            ->where(function($q)use($param){
                                $q->where('nm_dosen','like','%'.$param.'%')
                                ->orWhere('nidn','like','%'.$param.'%');
                            })->orderBy('nm_dosen','asc')->get();
        } else {
            $dosen = Dosen::where('aktif',1)->orderBy('nm_dosen','asc')->get();
        }
        $data = [];
        foreach( $dosen as $r ) {
            $data[] = ['data' => $r->id, 'value' => Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang)];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function storeDosen(Request $r)
    {
        $this->validate($r, [
            'dosen' => 'required',
            'pembimbing_ke' => 'required',
            'kegiatan' => 'required'
        ]);

        try {
            $data = new PembimbingKm;
            $data->id_aktivitas = $r->id_aktivitas;
            $data->id_dosen = $r->dosen;
            $data->id_jenis_pembimbing = $r->kegiatan;
            $data->save();

            Session::put('tab', 'dosen');
            Rmt::success('Berhasil menambahkan pembimbing');

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function deleteDosen($id)
    {
        PembimbingKm::findOrFail($id)->delete();

        Rmt::success('Berhasil menghapus pembimbing');
        return redirect()->back();
    }

    public function edit($id)
    {

        $data['mb'] = AktivitasKm::findOrFail($id);

        $data['jenis_aktivitas'] = DB::table('km_jenis_aktivitas')->get();

        return view('aktivitas-mbkm.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'prodi' => 'required',
            'jenis_aktivitas' => 'required',
            'judul' => 'required'
        ]);

        try {

            $data = AktivitasKm::findOrFail($r->id);
            $data->id_prodi = $r->prodi;
            $data->id_jenis_aktivitas = $r->jenis_aktivitas;
            $data->judul_aktivitas = $r->judul;
            $data->lokasi = $r->lokasi;
            $data->no_sk = $r->sk_tugas;
            
            if ( !empty($data->tgl_sk) ) {
                $data->tgl_sk = $r->tgl_sk;
            }

            if ( $r->jenis_aktivitas == 99 ) {
                $data->jenis_pertukaran = $r->jenis_pertukaran;
            }

            $data->jenis_anggota = $r->jenis_anggota;
            $data->keterangan = $r->keterangan;
            $data->save();

            Rmt::success('Berhasil menyimpan data');

         } 
         catch(\Exception $e)
         {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 422);
         }
    }

    public function excel()
    {
        $data['mahasiswa'] = Sia::akm()->orderBy('m1.nim','asc')->get();

        try {
            Excel::create('Aktivitas', function($excel)use($data) {

                $excel->sheet('New sheet', function($sheet)use($data) {

                    $sheet->loadView('aktivitas.excel', $data);

                });

            })->download('xlsx');
        } catch(\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function delete($id)
    {
        $cek_anggota = AnggotaKm::where('id_aktivitas', $id)->count();

        if ( $cek_anggota > 0 ) {
            Rmt::error('Masih ada peserta, hapus dahulu peserta aktivitas ini.');
            return redirect()->back();
        }

        $cek_pembimbing = PembimbingKm::where('id_aktivitas', $id)->count();

        if ( $cek_pembimbing > 0 ) {
            Rmt::error('Masih ada pembimbing, hapus dahulu pembimbing aktivitas ini.');
            return redirect()->back();
        }

        AktivitasKm::find($id)->delete();

        Rmt::success('Berhasil menghapus data');
        return redirect()->back();
    }

    public function conversiMbkm()
    {
    	$data = DB::table('km_aktivitas')
    	->select('km_aktivitas.id as ka_id', 'km_aktivitas.*', 'km_jenis_aktivitas.*', 'prodi.*', 'semester.*')
    	->join('prodi', 'prodi.id_prodi', '=', 'km_aktivitas.id_prodi')
    	->join('semester', 'semester.id_smt', '=', 'km_aktivitas.id_smt')
    	->join('km_jenis_aktivitas', 'km_jenis_aktivitas.id', '=', 'km_aktivitas.id_jenis_aktivitas')
    	->get();
        return view('mbkm.index', ['data' => $data]);
    }

    public function detailMbkm($id)
    {
    	$dataMbkm = DB::table('km_aktivitas')
    	->join('prodi', 'prodi.id_prodi', '=', 'km_aktivitas.id_prodi')
    	->join('semester', 'semester.id_smt', '=', 'km_aktivitas.id_smt')
    	->join('km_jenis_aktivitas', 'km_jenis_aktivitas.id', '=', 'km_aktivitas.id_jenis_aktivitas')
    	->where('km_aktivitas.id', $id)
    	->get();

    	$dataMhs = DB::table('km_anggota_aktivitas')
    	->select('mahasiswa_reg.nim', 'mahasiswa.nm_mhs', 'km_anggota_aktivitas.*')
    	->join('mahasiswa_reg', 'km_anggota_aktivitas.id_mhs_reg', '=', 'mahasiswa_reg.id')
    	->join('mahasiswa', 'mahasiswa_reg.id_mhs', '=', 'mahasiswa.id')
    	->where('km_anggota_aktivitas.id_aktivitas', $id)
    	->get();

    	return view('mbkm.nilai', ['data' => $dataMbkm, 'mhs' => $dataMhs, 'aktivitas' => $id]);
    }

    public function showNilai($id)
    {
    	$data['aktivitas'] = DB::table('km_anggota_aktivitas')
    	->join('km_aktivitas', 'km_aktivitas.id', '=', 'km_anggota_aktivitas.id_aktivitas')
    	->join('km_jenis_aktivitas', 'km_jenis_aktivitas.id', '=', 'km_aktivitas.id_jenis_aktivitas')
    	->join('semester', 'semester.id_smt', '=', 'km_aktivitas.id_smt')
    	->where('km_anggota_aktivitas.id_mhs_reg', $id)
    	->get();

    	$data['mhs'] = DB::table('mahasiswa_reg')
    	->select('mahasiswa.nm_mhs', 'mahasiswa_reg.*')
    	->join('mahasiswa', 'mahasiswa.id', '=', 'mahasiswa_reg.id_mhs')
    	->where('mahasiswa_reg.id', $id)
    	->get();

    	$data['nilai'] = DB::table('km_anggota_aktivitas')
        ->select('nilai_mbkm.*', 'matakuliah.nm_mk', 'matakuliah.sks_mk')
        ->join('km_aktivitas', 'km_aktivitas.id', '=', 'km_anggota_aktivitas.id_aktivitas')
        ->join('nilai_mbkm', 'nilai_mbkm.id_mhs_reg', '=', 'km_anggota_aktivitas.id_mhs_reg')
        ->join('matakuliah', 'matakuliah.id', '=', 'nilai_mbkm.id_mk')
        ->where('nilai_mbkm.id_mhs_reg', $id)
        ->groupby('nilai_mbkm.id')
    	->get();

        $prodi = $data['aktivitas'][0]->id_prodi;

        $data['mk'] = DB::select("SELECT * FROM matakuliah WHERE id_prodi = '$prodi' AND LEFT(nm_mk, 4) = 'MBKM'");

    	return view('mbkm.show-nilai', ['data' => $data]);
    }

    public function storeNilai(Request $request)
    {
        try {
            $nilaiMbkm = new NilaiMbkm();
            $nilaiMbkm->id_mhs_reg = $request->id_mhs_reg;
            $nilaiMbkm->id_mk = $request->nm_mk;
            $nilaiMbkm->id_smt = $request->id_smt;
            $nilaiMbkm->id_aktivitas = $request->id_aktivitas;

            $nilAngka = $request->nil_angka;

            if ($nilAngka >= 85 && $nilAngka <= 100) {
                $nilHuruf = 'A';
                $nilIndeks = 4;
            } elseif ($nilAngka >= 80 && $nilAngka <= 84) {
                $nilHuruf = 'A-';
                $nilIndeks = 3.75;
            } elseif ($nilAngka >= 75 && $nilAngka <= 79) {
                $nilHuruf = 'B+';
                $nilIndeks = 3.5;
            } elseif ($nilAngka >= 70 && $nilAngka <= 74) {
                $nilHuruf = 'B';
                $nilIndeks = 3;
            } elseif ($nilAngka >= 65 && $nilAngka <= 69) {
                $nilHuruf = 'B-';
                $nilIndeks = 2.75;
            } elseif ($nilAngka >= 60 && $nilAngka <= 65) {
                $nilHuruf = 'C';
                $nilIndeks = 2.5;
            } elseif ($nilAngka >= 55 && $nilAngka <= 59) {
                $nilHuruf = 'D';
                $nilIndeks = 1;
            } elseif ($nilAngka > 0 && $nilAngka <= 54) {
                $nilHuruf = 'E';
                $nilIndeks = 0;
            } elseif ($nilAngka >= 0) {
                $nilHuruf = 'T';
                $nilIndeks = 0;
            }

            $nilaiMbkm->nil_angka = $request->nil_angka;
            $nilaiMbkm->nil_huruf = $nilHuruf;
            $nilaiMbkm->nil_indeks = $nilIndeks;
            $nilaiMbkm->save();

            Rmt::success('Berhasil mengimput data');

            return redirect()->back();
        } catch (\Exception $e) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function destroyNilai($id)
    {
        $delNilaiMbkm = NilaiMbkm::find($id);
        $delNilaiMbkm->delete();

        return redirect()->back();
    }

}
