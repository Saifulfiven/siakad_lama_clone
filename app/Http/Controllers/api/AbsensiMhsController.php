<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response;
use App\Mahasiswareg, App\AbsenMhs;
use DB, Carbon;

class AbsensiMhsController extends Controller
{
    use Library;

    public function index(Request $r)
    {

        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $periode = Rmt::semesterBerjalan();

        $ta_berjalan = $periode->id_smt;

        $data_jdk = [];

        $jdk = $this->jadwalKuliahMahasiswa($mhs->id, 1, $ta_berjalan)
                ->where('jdk.hari', '<>', 0)
                ->get();

        foreach( $jdk as $j ) {

            $absen = AbsenMhs::where('id_jdk', $j->id)
                        ->where('updated_at', '>', Carbon::now())
                        ->count();

            $data_jdk[] = [
                'id' => $j->id,
                'hari'      => ucfirst(strtolower(Rmt::hari($j->hari))),
                'matakuliah'=> trim($j->nm_mk),
                'dosen'     => $j->dosen,
                'has_absen' => $absen > 0 ? true : false
            ];
        }

        $data = [
            'jadwal' => $data_jdk
        ];

        return Response::json($data, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function detail(Request $r, $id_jdk)
    {

        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $absen = DB::table('absen_mhs as am')
                    ->join('jadwal_kuliah as jdk', 'am.id_jdk', 'jdk.id')
                    ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
                    ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                    ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                    ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                    ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
                    ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                    ->select('jdk.kode_kls','jdk.id_smt','mk.kode_mk','mk.nm_mk','mk.sks_tm','mk.sks_mk',
                            'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.ket','jk.jam_masuk',
                            'jk.jam_keluar','am.id_jdk','am.pertemuan_ke','am.waktu','am.updated_at as end_time',
                            'n.a_1','n.a_2','n.a_3','n.a_4','n.a_5','n.a_6','n.a_7','n.a_8','n.a_9','n.a_10',
                            'n.a_11','n.a_12','n.a_13','n.a_14',
                        DB::raw('
                            (SELECT group_concat(distinct dm.dosen_ke,". ",dos.gelar_depan," ", dos.nm_dosen,", ", dos.gelar_belakang SEPARATOR \'<br>\') from dosen_mengajar as dm
                            left join dosen as dos on dm.id_dosen=dos.id
                            where dm.id_jdk=jdk.id order by dm.dosen_ke asc) as dosen'))
                    ->where('am.updated_at', '>', Carbon::now())
                    ->where('n.id_mhs_reg', $mhs->id)
                    ->where('am.id_jdk', $id_jdk)
                    ->first();

        $absen_ = DB::table('nilai')
                            ->where('id_mhs_reg', $mhs->id)
                            ->where('id_jdk', $id_jdk)
                            ->first();
        if ( empty($absen_) ) {
            return Response::json('Data tidak eksis', 422);
        }

        $absened = [
            '1' => $absen_->a_1,
            '2' => $absen_->a_2,
            '3' => $absen_->a_3,
            '4' => $absen_->a_4,
            '5' => $absen_->a_5,
            '6' => $absen_->a_6,
            '7' => $absen_->a_7,
            '8' => $absen_->a_8,
            '9' => $absen_->a_9,
            '10' => $absen_->a_10,
            '11' => $absen_->a_11,
            '12' => $absen_->a_12,
            '13' => $absen_->a_13,
            '14' => $absen_->a_14
        ];

        $result = [
            'absen' => $absen,
            'absened' => $absened
        ];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function store(Request $r)
    {

        try {

            $data = json_decode($r->data);

            $mhs = Mahasiswareg::where('nim', $data->nim)->first();

            DB::table('nilai')->where('id_mhs_reg', $mhs->id)
                ->where('id_jdk', $data->id_jdk)
                ->update(['a_'.$data->pertemuan => 1]);

        } catch( \Exception $e ) {

            return Response::json([$e->getMessage()], 422);

        }
    }
    
}