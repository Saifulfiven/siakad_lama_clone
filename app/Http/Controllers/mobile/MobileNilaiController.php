<?php
namespace App\Http\Controllers\mobile;

use Illuminate\Http\Request;
use DB, Rmt, Carbon, Response;
use App\Http\Controllers\api\Library;

trait MobileNilaiController
{
    use Library;

    public function nilai(Request $r)
    {
	    $data['r'] = $this->jadwalKuliah2($r->id_jdk);

        $data['jml_pertemuan'] = $data['r']->jenis == 1 ? 14 : 12;
    	$data['peserta'] = $this->pesertaKelas($data['r']->id);
        $data['min_kehadiran'] = round( ($data['jml_pertemuan'] * 0.50 ) + 1);

	    return view('mobile.nilai-s2.index', $data);
    }

    public function nilaiUpdates2(Request $r)
    {

        try {

            DB::transaction(function()use($r){

                foreach( $r->id_nilai as $key => $val )
                {

                    $nil = \App\Nilai::where('id', $r->id_nilai[$key])->first();

                    $grade = '';

                    if ( $r->dosen_ke == 1 ) {

                        if ( !empty($r->nilai_final_hide[$key]) ) {
                            
                            $tot_nilai = floatval($r->nilai_final_hide[$key]) + floatval($r->uts[$key]);
                            
                            $rata2 = 0;

                            if ( !empty($tot_nilai) ) {
                                $rata2 = $tot_nilai / 2;
                            }

                            $grade = $this->grade($r->id_prodi, $rata2);

                            $q_indeks = DB::table('skala_nilai')
                                        ->select('nilai_indeks')
                                        ->where('id_prodi', $r->id_prodi)
                                        ->where('nilai_huruf', $grade)
                                        ->first();
                            if ( !empty($q_indeks) ) {
                                $indeks = $q_indeks->nilai_indeks;
                            } else {
                                $indeks = 0.00;
                            }

                            $nil->nil_mid = $r->uts[$key];
                            $nil->nilai_angka = $rata2;
                            $nil->nilai_huruf = $grade;
                            $nil->nilai_indeks = $indeks;

                        } else {
                            $nil->nil_mid = $r->uts[$key];
                        }

                    } else {

                        $tot_nilai = floatval($r->nilai_mid_hide[$key]) + floatval($r->uas[$key]);
                        
                        $rata2 = 0;

                        if ( !empty($tot_nilai) ) {
                            $rata2 = $tot_nilai / 2;
                        }

                        $grade = $this->grade($r->id_prodi, $rata2);

                        $q_indeks = DB::table('skala_nilai')
                                        ->select('nilai_indeks')
                                        ->where('id_prodi', $r->id_prodi)
                                        ->where('nilai_huruf', $grade)
                                        ->first();
                        if ( !empty($q_indeks) ) {
                            $indeks = $q_indeks->nilai_indeks;
                        } else {
                            $indeks = 0.00;
                        }

                        $nil->nil_final = $r->uas[$key];
                        $nil->nilai_angka = $rata2;
                        $nil->nilai_huruf = $grade;
                        $nil->nilai_indeks = $indeks;

                    }
                    
                    $save = $nil->save();

                }
            });
            

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        Rmt::success('Berhasil menyimpan data');

        return Response::json(['error' => 0,'msg' => ''], 200);
    }

    public function nilaiUpdates2Single(Request $r)
    {

        try {

            DB::transaction(function()use($r){

                foreach( $r->id_nilai as $key => $val )
                {

                    $nil = \App\Nilai::where('id', $r->id_nilai[$key])->first();

                    $grade = '';

                    $rata2 = $r->uas[$key];

                    if ( !empty($tot_nilai) ) {
                        $rata2 = $tot_nilai / 2;
                    }

                    $grade = $this->grade($r->id_prodi, $rata2);

                    $q_indeks = DB::table('skala_nilai')
                                    ->select('nilai_indeks')
                                    ->where('id_prodi', $r->id_prodi)
                                    ->where('nilai_huruf', $grade)
                                    ->first();

                    if ( !empty($q_indeks) ) {
                        $indeks = $q_indeks->nilai_indeks;
                    } else {
                        $indeks = 0.00;
                    }

                    $nil->nil_mid = $r->uas[$key];
                    $nil->nil_final = $r->uas[$key];
                    $nil->nilai_angka = $rata2;
                    $nil->nilai_huruf = $grade;
                    $nil->nilai_indeks = $indeks;
                    
                    $save = $nil->save();

                }
            });
            

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        Rmt::success('Berhasil menyimpan data');

        return Response::json(['error' => 0,'msg' => ''], 200);
    }


    private function jadwalKuliah2($id_jdk)
    {
        $data = DB::table('jadwal_kuliah as jdk')
            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
            ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
            ->leftJoin('ruangan as r', 'r.id', 'jdk.ruangan')
            ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
            ->select('jdk.id','jdk.id_smt', 'jdk.jenis', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk', 'jdk.kode_kls','jdk.id_prodi', 'pr.jenjang', 'pr.nm_prodi', 'r.nm_ruangan','smt.nm_smt')
            ->where('jdk.id', $id_jdk)
            ->first();

        return $data;
    }

    private function grade($prodi, $nilai)
    {
        $query = $this->skalaNilai($prodi);

        $loop = 1;
        $grade = 'E';

        foreach( $query as $r ) {
            if ( $loop == 1 ) {
                if ( $nilai <= $r->range_atas || $nilai > $r->range_atas ) {
                    $grade = $r->nilai_huruf;
                }
            }

            if ( $nilai <= $r->range_atas ) {
                $grade = $r->nilai_huruf;
            }

            $loop++;
        }
         
         return $grade;
    }
}