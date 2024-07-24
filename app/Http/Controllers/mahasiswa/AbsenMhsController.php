<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AbsenMhs;
use Sia, DB, Response, Rmt, Carbon, Session;

class AbsenMhsController extends Controller
{
    public function index(Request $r)
    {

        $id_reg_pd = Sia::sessionMhs('id_mhs_reg');

        if ( !empty($r->ubah_jenis) ) {
            Session::put('jeniskrs_in_jdk', $r->ubah_jenis);
        } else {
            Session::put('jeniskrs_in_jdk', 1);
        }

        $query = Sia::jadwalKuliahMahasiswa($id_reg_pd, Session::get('jeniskrs_in_jdk'));
        $data['jadwal'] = $query->where('jdk.id_smt', Sia::sessionPeriode())
                                ->where('jdk.hari', '<>', 0)
                                ->get();

        return view('mahasiswa-member.absen-mhs.index', $data);
    }

    public function detail(Request $r)
    {

        $data['absen'] = DB::table('absen_mhs as am')
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
                    ->where('n.id_mhs_reg', Sia::sessionMhs())
                    ->where('am.id_jdk', $r->id_jdk)
                    ->first();

        return view('mahasiswa-member.absen-mhs.detail', $data);
    }

    public function store(Request $r)
    {

        try {

            DB::table('nilai')->where('id_mhs_reg', Sia::sessionMhs())
                ->where('id_jdk', $r->id_jdk)
                ->update(['a_'.$r->pertemuan => 1]);

        } catch( \Exception $e ) {

            return Response::json([$e->getMessage()], 422);

        }
    }
    
}