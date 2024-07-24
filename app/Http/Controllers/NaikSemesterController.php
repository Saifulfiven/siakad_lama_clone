<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Rmt, Auth, Sia, Response, Session;

class NaikSemesterController extends Controller
{

    public function index(Request $r)
    {
        if ( Auth::user()->naik_smt != 1 ) {
            echo "<center><h3>Akses Ditolak</h3></center>";
            exit;
        }

        return view('naik-semester.index');
    }

    public function store(Request $r)
    {
        if ( Auth::user()->naik_smt != 1 ) {
            echo "<center><h3>Akses Ditolak</h3></center>";
            exit;
        }

        $smt = Sia::semesterBerjalan();
        $prodi = Sia::getProdiUser();
        $fakultas = Sia::getFakultasUser($prodi[0]);
        
        /* Validasi */
            // Jadwal belum ada
            $rule_1 = DB::table('jadwal_kuliah')->where('id_smt', $smt['id'])->count();
            if ( $rule_1 == 0 ) {
                return Response::json(['error' => 1, 'msg' => 'Anda belum bisa menaikkan semester, perkuliahan belum dimulai']);
            }

            // Mhs transfer tapi sks konversi 0
            // $rule_2 = DB::table('v_konversi_0')->whereIn('id_prodi', Sia::getProdiUser())->count();
            // if ( $rule_2 > 0 ) {
            //     return Response::json(['error' => 1, 'msg' => 'Anda belum bisa menaikkan semester, masih ada data mahasiswa konversi yang belum dimasukkan nilai konversinya. (Lihat pada halaman validasi)']);
            // }

            // Aktivitas belum diinput
            $mhs_aktif = DB::table('mahasiswa_reg as m1')
                ->whereIn('id_prodi', $prodi)
                        ->where('id_jenis_keluar', 0)
                        ->count();
            $rule_3 = DB::table('aktivitas_kuliah as akm')
                        ->leftJoin('mahasiswa_reg as m1', 'm1.id', 'akm.id_mhs_reg')
                        ->leftJoin('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
                        ->whereIn('m1.id_prodi', $prodi)
                        ->where('id_smt', $smt['id'])->count();

            if ( $rule_3 < $mhs_aktif ) {
                return Response::json(['error' => 1, 'msg' => 'Aktivitas perkuliahan belum lengkap. Masuk ke menu Perkuliahan -> Aktivitas Perkuliahan -> klik pada tombol Tambah Massal']);
            }
        /* end validasi */

        try{

            DB::transaction(function()use($r, $smt, $fakultas, $prodi){

                // Semester_aktif dan semester
                $newSmt = $this->setSemesterAktif($fakultas);

                $this->insertBiayaKuliah($newSmt);
                $this->setNilaiError($smt['id'], $prodi[0]);
                $this->updateJadwalAkademik();

                Session::pull('periode_aktif');

            });
        } catch( \Exception $e ) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }

        Rmt::success('Berhasil menyimpan data');
        return Response::json(['error' => 0]);
    }


    private function setSemesterAktif($fakultas)
    {
        $smt = DB::table('semester_aktif')->where('id_fakultas', $fakultas)->first();

        $ket = substr($smt->id_smt,4);

        if ( $ket == 1 ) {
            $new_smt = $smt->id_smt + 1;
        } else {
            $tahun = substr($smt->id_smt,0,4);
            $tahun += 1;
            $new_smt = $tahun.'1';
        }
        DB::table('semester_aktif')->where('id_fakultas', $fakultas)
                ->update(['id_smt' => $new_smt]);

        $semester = \App\Semester::firstOrNew([
            'id_smt' => $new_smt,
        ]);

        if ( !$semester->exists ) {
            $new_year = substr($new_smt,0,4);
            $ket_smt = substr($new_smt,4);
            $nm_smt = $ket_smt == 1 ? 'Ganjil' : 'Genap';
            $nm_smt = $new_year.'/'.($new_year+1).' '.$nm_smt;

            $data_smstr = new \App\Semester;
            $data_smstr->id_smt = $new_smt;
            $data_smstr->nm_smt = $nm_smt;
            $data_smstr->smt = $ket_smt;
            $data_smstr->save();
        }

        return $new_smt;

    }

    private function insertBiayaKuliah($smt)
    {
        $tahun = substr($smt, 0, 4);
        $ket_smt = substr($smt, 4);

        if ( $ket_smt == 1 ) {

            foreach( Sia::getProdiUser() as $prodi ) {

                $biaya = \App\Biaya::orderBy('tahun','desc')->first();

                $data = New \App\Biaya;
                $data->tahun = $tahun;
                $data->id_prodi = $prodi;
                $data->spp = $biaya->spp;
                $data->bpp = $biaya->bpp;
                $data->seragam = $biaya->seragam;
                $data->lainnya = $biaya->lainnya;
                $data->save();
            }
        }
    }

    private function setNilaiError($smt, $prodi)
    {
        $fakultas = Sia::getFakultasUser($prodi);
        $data = DB::table('nilai as n')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('prodi as pr', 'pr.id_prodi', 'jdk.id_prodi')
                ->leftJoin('fakultas as f', 'f.id', 'pr.id_fakultas')
                ->whereIn('nilai_huruf',['','T'])
                ->where('jdk.id_smt', $smt)
                ->where('jdk.jenis', 1)
                ->where('f.id', $fakultas)
                ->pluck('n.id');

        $res = DB::table('nilai')->whereIn('id', $data)
        ->update(['nilai_huruf' => 'E']);
    }

    private function updateJadwalAkademik()
    {
        $jdw = DB::table('jadwal_akademik')
                ->where('id_fakultas', Sia::sessionPeriode('fakultas'))
                ->first();

        DB::table('jadwal_akademik')
        ->where('id', $jdw->id)
        ->update([
            'awal_pembayaran' => $this->yearPlus_1($jdw->awal_pembayaran),
            'akhir_pembayaran' => $this->yearPlus_1($jdw->akhir_pembayaran),
            'awal_krs' => $this->yearPlus_1($jdw->awal_krs),
            'akhir_krs' => $this->yearPlus_1($jdw->akhir_krs),
            'awal_kuliah' => $this->yearPlus_1($jdw->awal_kuliah),
        ]);
        
    }

    private function yearPlus_1($tgl)
    {
        return date('Y-m-d',strtotime("$tgl +1 year"));
    }

}