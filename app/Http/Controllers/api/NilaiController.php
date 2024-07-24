<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response, Hash;
use App\Mahasiswareg;
use DB;

class NilaiController extends Controller
{
    use Library;

    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {

        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        if ( empty($mhs) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada bisa ditampilkan']);
        }

        $periode = $this->semesterBerjalan($mhs->id);
        $ta_aktif = empty($r->ta) || $r->ta == 'null' ? $periode['id'] : $r->ta;

        $jml_sks = 0;
        $tot_bobot = 0;
        $no = 1;
        $data_nilai = [];

        $kues_aktif = DB::table('kues_jadwal')
                        ->where('aktif', 1)
                        ->where('id_prodi', $mhs->id_prodi)
                        ->first();

        foreach( $this->nilai($mhs->id, $ta_aktif) as $n ) {
            $cek_kuesioner = 0;
                                                
            if ( !empty($kues_aktif->id) ) {
            
                $cek_kuesioner = DB::table('kues as k')
                        ->leftJoin('kues_jadwal as kj', 'k.id_kues_jadwal', 'kj.id')
                        ->where('k.id_mhs_reg', $mhs->id)
                        ->where('k.id_mk', $n->id_mk)
                        ->where('k.id_kues_jadwal', $kues_aktif->id)
                        ->count();
            }

            $has_kuesioner = true;

            if (
                !empty($kues_aktif) 
                && $ta_aktif == $kues_aktif->id_smt
                && $cek_kuesioner <= 0
                && !empty($n->id_jam)
            ) {
                $has_kuesioner = false;
            }

            $data_nilai[]   = [
                'no' => $no++,
                'id_nilai' => $n->id,
                'matakuliah' => $n->nm_mk, 
                'sks' => $n->sks_mk, 
                'nil_tugas' => $n->nil_tugas,
                'nil_uts' => $n->nil_mid,
                'nil_final' => $n->nil_final,
                'nilai' => $has_kuesioner ? $n->nilai_huruf : '-',
                'has_kuesioner' => $has_kuesioner
            ];

            $jml_sks += $n->sks_mk;
            $tot_bobot += $n->nilai_indeks * $n->sks_mk;
        }

        if ( empty($tot_bobot) ) {
            $ips = '0.00';
        } else {
            $ips = round($tot_bobot/$jml_sks,2);
        }

        $result_nilai = [
            'count' => count($data_nilai), 
            'total_sks' => $jml_sks, 
            'ips' => $ips,
            'ipk' => $this->ipk($mhs->id, $mhs->semester_mulai, $ta_aktif),
            'data' => $data_nilai
        ];

        $result_ta = [
            'data' => $this->semester($mhs->semester_mulai, $periode['id'], $ta_aktif)
        ];

        $data = ['ta' => $result_ta, 'nilai' => $result_nilai];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function transkrip(Request $r)
    {
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        if ( empty($mhs) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa ditampilkan']);
        }

        $transkrip = $this->getTranskrip($mhs->id);

        $transkrips = [];
        $total_sks = 0;
        $total_sks_diprogram = 0;
        $total_nilai = 0;
        $total_bobot = 0;
        $no = 1;
        
        foreach( $transkrip as $tr )
        {
            $kumulatif = $tr->sks_mk * $tr->nilai_indeks;

            $transkrips[] = [
                'no' => $no++,
                'nilai_huruf' => $tr->nilai_huruf,
                'nilai_indeks' => $tr->nilai_indeks,
                'kode_mk' => $tr->kode_mk,
                'nm_mk' => strtoupper($tr->nm_mk),
                'sks' => $tr->sks_mk,
                'smt' => $tr->smt
            ];

            if ( !empty($tr->nilai_indeks) ) {
                $total_sks += $tr->sks_mk;
            }
            $total_sks_diprogram += $tr->sks_mk;
            
            $total_nilai += $tr->nilai_indeks;
            $total_bobot += $kumulatif;

        }

        if ( !empty($total_bobot) && !empty($total_sks) ) {
            $ipk = number_format($total_bobot / $total_sks, 2);
        } else {
            $ipk = 0.00;
        }

        $total = [
            'total_sks' => $total_sks,
            'total_sks_diprogram' => $total_sks_diprogram,
            'total_bobot' => $total_bobot,
            'total_nilai' => $total_nilai,
            'ipk' => $ipk
        ];

        $result = ['error' => 0, 'transkrip' => $transkrips, 'total' => $total];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    private function getTranskrip($id_mhs_reg)
    {
        $data = DB::select("
                    SELECT id_mhs_reg, min(nilai_huruf) AS nilai_huruf, max(nilai_indeks) as nilai_indeks, kode_mk, nm_mk, sks_mk,smt
                    FROM (

                        SELECT id_mhs_reg,nilai_huruf, nilai_indeks, kode_mk, nm_mk, sks_mk,smt

                        FROM (
                            SELECT nt.id_mhs_reg, nt.nilai_huruf_diakui as nilai_huruf, nt.nilai_indeks, mk.kode_mk, mk.nm_mk, mk.sks_mk, mkur.smt
                            FROM nilai_transfer as nt
                            left join matakuliah as mk on nt.id_mk = mk.id
                            left join mk_kurikulum as mkur on mk.id = mkur.id_mk 
                            where nt.id_mhs_reg = '$id_mhs_reg'

                            union

                            SELECT nil.id_mhs_reg, nil.nilai_huruf, nil.nilai_indeks, mk.kode_mk, mk.nm_mk, mk.sks_mk, mkur.smt
                            FROM nilai as nil 
                            left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
                            left join mk_kurikulum as mkur on jdk.id_mkur = mkur.id 
                            left join matakuliah as mk on mk.id = mkur.id_mk
                            where nil.id_mhs_reg = '$id_mhs_reg'

                        ) as result

                    ) as result2
                    where nilai_huruf != ''
                    group by kode_mk
                    order by smt asc, kode_mk asc, nilai_indeks desc
                ");

        return $data;
    }

}
