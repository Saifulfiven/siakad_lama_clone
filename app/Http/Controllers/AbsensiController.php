<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Sia, Session;
use App\Mahasiswareg;

class AbsensiController extends Controller
{
    public function index(Request $r)
    { 

        // Set Filter
        if ( !empty($r->prodi) ) {
            Session::set('abs_prodi', $r->prodi);
        }

        if ( !empty($r->angkatan) ) {
            Session::set('abs_angkatan', $r->angkatan);
        }

        if ( !empty($r->smt) ) {
            Session::set('abs_smt', $r->smt);
        }

        if ( !empty($r->absen) ) {
            
            if ( $r->absen == 'all' ) {
                Session::pull('abs_absen');
            } else {
                Session::set('abs_absen', $r->absen);
            }
        }

        if ( !Session::has('abs_prodi') ) {
            $this->setSessionAbsen();
        }

        $mhs = DB::table('v_mhs_krsan as v')
                ->select('v.*',
                    DB::raw('(SELECT 
                        SUM(a_1)+
                        SUM(a_2)+
                        SUM(a_3)+
                        SUM(a_4)+
                        SUM(a_5)+
                        SUM(a_6)+
                        SUM(a_7)+
                        SUM(a_8)+
                        SUM(a_9)+
                        SUM(a_10)+
                        SUM(a_11)+
                        SUM(a_12)+
                        SUM(a_13)+
                        SUM(a_14)

                        FROM nilai as nil
                        left join mahasiswa_reg as m1 on nil.id_mhs_reg = m1.id
                        left join jadwal_kuliah as jdk on nil.id_jdk = jdk.id
                        where jdk.id_smt = v.id_smt
                        and nil.id_mhs_reg = v.id_mhs_reg ) as absen'),

                        DB::raw('(SELECT count(*) as jml FROM nilai as nil
                            left join jadwal_kuliah as jdk on nil.id_jdk = jdk.id
                            where jdk.id_smt = v.id_smt
                            and nil.id_mhs_reg = v.id_mhs_reg ) as mk'))

                ->where('id_smt', Session::get('abs_smt'))
                ->where('id_prodi', Session::get('abs_prodi'));

        // Filter angkatan
        if ( Session::get('abs_angkatan') != 'all' ) {
            $mhs->whereRaw("left(nim,4)='".Session::get('abs_angkatan')."'");
        }

        if ( !empty(Session::get('abs_absen')) ) {
            $mhs->havingRaw("absen ".Session::get('abs_absen'));
        }

        $data['mahasiswa'] = $mhs->get();

        $data['semester'] = Sia::listSemester();

        return view('absensi.index', $data);
    }

    private function setSessionAbsen()
    {
        Session::set('abs_smt', Sia::sessionPeriode());
        $prodi = Sia::getProdiUser();
        Session::set('abs_prodi', $prodi[0]);
        Session::set('abs_angkatan', 'all');
    }

    public function cetakPerJadwal(Request $r)
    {
        $data['mhs'] = DB::table('mahasiswa_reg as m2')
                        ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                        ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                        ->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
                        ->select('m.id','m.nm_mhs', 'm2.id as id_reg_pd','m2.nim', 'm2.id_prodi', 'm2.semester_mulai','p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
                        ->where('m2.id',$r->id_mhs_reg)->first();

        $data['jadwal'] = $this->jadwalMhs($r->id_mhs_reg)
                    ->where('jdk.id_smt', $smt)
                    ->where('jdk.hari', '<>', 0)->get();

        $ta = DB::table('semester')
                ->where('id_smt', Session::set('abs_smt'))
                ->first();

        $data['ta'] = $ta->nm_smt;
        
        return view('absensi.cetak-per-jadwal', $data);
    }

    public function absen(Request $r)
    {

        $smt = empty($r->smt) ? Session::get('abs_smt') : $r->smt;

        if ( !Session::has('abs_smt') ) {
            $smt = Sia::sessionPeriode();
        }

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
                        ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                        ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                        ->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
                        ->select('m.id','m.nm_mhs', 'm2.id as id_reg_pd','m2.nim', 'm2.id_prodi', 'm2.semester_mulai','p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
                        ->where('m2.id',$r->id_mhs_reg)->first();

        $data['jadwal'] = $this->jadwalMhs($r->id_mhs_reg)
                    ->where('jdk.id_smt', $smt)
                    ->where('jdk.hari', '<>', 0)->get();

        $data['semester'] = DB::table('semester')
                        ->whereBetween('id_smt', [$data['mhs']->semester_mulai, Sia::semesterBerjalan()['id']])
                        ->orderBy('id_smt','desc')->get();

        return view('absensi.absensi',$data);
    }

    private function jadwalMhs($id_mhs_reg)
    {
        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
                ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
                ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
                ->select('jdk.*','mk.kode_mk','mk.nm_mk','mk.sks_mk',
                        'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
                        'jk.jam_keluar','smt.nm_smt','mkur.smt','smt.nm_smt',
                    DB::raw('
                        (SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen, ", ",dos.gelar_belakang SEPARATOR \'<br>\') from dosen_mengajar as dm
                        left join dosen as dos on dm.id_dosen=dos.id
                        where dm.id_jdk=jdk.id) as dosen'))
                ->where('jdk.jenis', 1)
                ->where('n.id_mhs_reg', $id_mhs_reg)
                ->orderBy('jdk.hari','asc')
                ->orderBy('jk.jam_masuk','asc');

        return $data;
    }

    public function absenMhs(Request $r)
    {
        $mhs = Mahasiswareg::find($r->id);
        $nilai = DB::table('nilai')->where('id', $r->id_nilai)->first();

        $kehadiran = [
            $nilai->a_1,
            $nilai->a_2,
            $nilai->a_3,
            $nilai->a_4,
            $nilai->a_5,
            $nilai->a_6,
            $nilai->a_7,
            $nilai->a_8,
            $nilai->a_9,
            $nilai->a_10,
            $nilai->a_11,
            $nilai->a_12,
            $nilai->a_13,
            $nilai->a_14
        ];

        for( $i = 0; $i < count($kehadiran); $i++ ) { ?>
            <tr>
                <td align="center"><?= $i+1 ?></td>
                <td align="center"><?= $kehadiran[$i] == 1 ? '<i class="fa fa-check" style="color: green"></i> Hadir</i>': '<i class="fa fa-ban" style="color: red"></i> Tidak Hadir</i>' ?></td>
            </tr>
        <?php }
    }

    public function cetakPerMk(Request $r)
    {
        $mhs = Mahasiswareg::find($r->id);
        $nilai = DB::table('nilai')->where('id', $r->id_nilai)->first();

        $kehadiran = [
            $nilai->a_1,
            $nilai->a_2,
            $nilai->a_3,
            $nilai->a_4,
            $nilai->a_5,
            $nilai->a_6,
            $nilai->a_7,
            $nilai->a_8,
            $nilai->a_9,
            $nilai->a_10,
            $nilai->a_11,
            $nilai->a_12,
            $nilai->a_13,
            $nilai->a_14
        ];

        $data = [];

        for( $i = 0; $i < count($kehadiran); $i++ ) {
            $absen = $kehadiran[$i] == 1 ? '<i class="fa fa-check" style="color: green"></i> Hadir</i>': '<i class="fa fa-ban" style="color: red"></i> Tidak Hadir</i>';
            $data[] = ['no' => $i+1, 'absen' => $absen];
        }

        return view('absensi.cetak-per-mk',$data);
    }
}