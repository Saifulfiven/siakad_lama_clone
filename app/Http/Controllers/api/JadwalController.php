<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response;
use App\Mahasiswareg;
use DB, Carbon;

class JadwalController extends Controller
{
    use Library;

    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        // Jadwal kuliah = 1, sp = 2
        $jenis_jdk = 1;

        $user = Mahasiswareg::where('nim', $r->nim)->first();

        if ( empty($user) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada bisa ditampilkan']);
        }

        $periode = $this->semesterBerjalan($user->id);

        $jenis_ujian = $this->jenisUjian($periode['id']);

        $data_jdk = [];
        $data_jdu = [];

        /* Jadwal kuliah */
            $jdk = $this->jadwalKuliahMahasiswa($user->id, $jenis_jdk, $periode['id'])
                ->where('jdk.id_smt', $periode['id'])
                ->where('jdk.hari', '<>', 0)
                ->get();

            foreach( $jdk as $j ) {
                $jam    = substr($j->jam_masuk,0,5);
                $jam_out = substr($j->jam_keluar,0,5);
                $mtk    = trim($j->nm_mk);
                $data_jdk[] = [
                    'hari'      => ucfirst(strtolower(Rmt::hari($j->hari))),
                    'matakuliah'=> $mtk,
                    'dosen'     => $j->dosen,
                    'jam'       => $jam.'-'.$jam_out,
                    'ruangan'   => $j->nm_ruangan
                ];
            }

        /* Jadwal ujian */
            $jadwal_ujian = $this->jadwalUjianMhs($user->id,$jenis_jdk,$jenis_ujian,$periode['id'])->get();

            foreach( $jadwal_ujian as $jdu ) {
                $tgl = Carbon::parse($jdu->tgl_ujian)->format('d/m/Y');

                $data_jdu[] = [
                    'hari' => ucfirst(strtolower(Rmt::hari($jdu->hari))).' '.$tgl, 
                    'matakuliah' => trim($jdu->nm_mk), 
                    'dosen' => $jdu->dosen,
                    'jam' => substr($jdu->jam_masuk,0,5),
                    'rgn' => $jdu->nm_ruangan
                ];

            }


        $result_jdk = ['count' => count($jdk), 'data' => $data_jdk, 'tahun' => substr($periode['id'],0,4), 'sms' => $periode['ket'] == 1 ? 'GANJIL':'GENAP'];
        
        $result_jdu = ['count' => count($jadwal_ujian), 'data' => $data_jdu, 'jenis' => $jenis_ujian];
        
        $data = ['jadwal_kuliah' => $result_jdk, 'jadwal_ujian' => $result_jdu];
        
        $result = ['error' => 0, 'data' => $data];
       
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    private function jadwalUjianMhs($id_mhs_reg, $jenis_jdk = 1, $jenis_ujian,$id_smt)
    {
        $query = DB::table('peserta_ujian as pu')
                    ->leftJoin('jadwal_ujian as jdu', 'pu.id_jdu', 'jdu.id')
                    ->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
                    ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                    ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                    ->leftJoin('ruangan as r', 'jdu.id_ruangan','=','r.id')
                    ->leftJoin('prodi as pr', 'jdk.id_prodi', 'pr.id_prodi')
                    ->select('jdu.*','mk.nm_mk','mk.sks_mk','jdk.id_smt',
                            'jdk.kode_kls','r.nm_ruangan', 'pr.nm_prodi',
                            'pr.jenjang','mkur.smt',
                        DB::raw('
                        (SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen, ", ",dos.gelar_belakang SEPARATOR \'<br>\') from dosen_mengajar as dm
                        left join dosen as dos on dm.id_dosen=dos.id
                        where dm.id_jdk=jdk.id) as dosen'))
                    ->where('jdk.id_smt', $id_smt)
                    ->where('jdk.jenis', $jenis_jdk)
                    ->where('jdu.jenis_ujian', $jenis_ujian)
                    ->where('pu.id_mhs_reg', $id_mhs_reg)
                    ->orderBy('jdu.tgl_ujian','asc');
                    // ->orderBy('jdu.hari','asc')
                    // ->orderBy('jdu.jam_masuk','asc');

        return $query;
    }

}
