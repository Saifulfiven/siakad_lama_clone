<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Mahasiswareg;
use Rmt, DB, Response, Sia;

class KuesionerController extends Controller
{
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {

        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        if ( empty($mhs) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa ditampilkan']);
        }

        $periode = $this->getPeriode($mhs->id_prodi);

        $jadwal = $this->jadwalKuliahMahasiswa($mhs->id, 1)
                ->where('jdk.id_smt', $periode)
                ->where('jdk.hari', '<>', 0)
                ->get();

        $kues_aktif = DB::table('kues_jadwal')
                    ->where('aktif', 1)
                    ->where('id_prodi', $mhs->id_prodi)
                    ->first();

        $jadwalArr = [];

        foreach( $jadwal as $val )
        {
            $cek = 0;
            if ( !empty($kues_aktif->id) ) {
                $cek = DB::table('kues')
                        ->where('id_kues_jadwal', $kues_aktif->id)
                        ->where('id_mhs_reg', $mhs->id)
                        ->where('id_mk', $val->id_mk)
                        ->where('id_dosen', $val->id_dosen)
                        ->count();
            }

            $jadwalArr[] = [
                'id_mk' => $val->id_mk,
                'id_jdk' => $val->id,
                'nm_mk' => $val->nm_mk,
                'sks' => $val->sks_mk,
                'kelas' => $val->kode_kls,
                'ruangan' => $val->ruangan,
                'id_dosen' => $val->id_dosen,
                'dosen' => $this->namaDosen($val->gelar_depan, $val->nm_dosen, $val->gelar_belakang),
                'status' => $cek > 0 ? 'close' : 'open'
            ];
        }

        $data = ['kues' => $kues_aktif, 'jadwal' => $jadwalArr];
        $result = ['error' => 0, 'data' => $data];
        // dd($data);
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function add(Request $r)
    {
        $param = json_decode($r->data_kues);
        $param = $param[0];

        $mhs = Mahasiswareg::where('nim', $param->nim)->first();

        $dosen = DB::table('dosen')->where('id', $param->id_dosen)->first();

        $mk = DB::table('matakuliah')->where('id', $param->id_mk)->first();

        $komponen = DB::table('kues_komponen')
                            ->where('id_prodi', $mhs->id_prodi)
                            ->where('aktif', 1)
                            ->orderBy('urutan')
                            ->get();

        $kues = [];

        foreach( $komponen as $val ) {
            $isi = DB::table('kues_komponen_isi')
                ->where('id_komponen', $val->id)
                ->orderBy('urutan')
                ->get();
            foreach( $isi as $v2 ) {
                $kues[$val->id][] = [
                    'id' => $v2->id,
                    'pertanyaan' => $v2->pertanyaan,
                    'jenis' => $val->jenis
                ];
            }
        }

        $data = ['kues' => $kues, 'komponen' => $komponen, 'mk' => $mk, 'dosen' => $dosen];
        // dd($data);
        $result = ['error' => 0, 'data' => $data ];
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function store(Request $r)
    {

        $kuesioner = json_decode($r->kuesioner);
        $data_kues = json_decode($r->data_kues);
        
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        if ( empty($mhs) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa ditampilkan']);
        }

        try {

            // Simpan kuesioner
            $data_kues = [
                'id_mhs_reg' => $mhs->id,
                'id_jdk' => $data_kues->id_jdk,
                'id_mk' => $data_kues->id_mk,
                'kode_kls' => $data_kues->kelas,
                'ruangan' => $data_kues->ruangan,
                'id_dosen' => $data_kues->id_dosen,
                'id_kues_jadwal' => $data_kues->kues
            ];

            $id_kues = DB::table('kues')->insertGetId($data_kues);

            if ( !empty(count($kuesioner)) )
            {
                foreach( $kuesioner as $val )
                {
                    if ( $val->jenis == 'pg' ) {
                        $data = [
                            'id_kues' => $id_kues,
                            'id_komponen_isi' => $val->id,
                            'penilaian' => $val->penilaian,
                        ];
                    } else {
                        $data = [
                            'id_kues' => $id_kues,
                            'id_komponen_isi' => $val->id,
                            'penilaian_text' => $val->penilaian_text,
                        ];
                    }

                    DB::table('kues_hasil')->insert($data);
                }
            }

        } catch( \Exception $e ) {
            return Response::json(['Terjadi kesalahan, cek koneksi internet anda dan coba menyimpan kembali.'], 422);
        }

        return Response::json(['error' => 0, 'msg' => 'Sukses']);
    }

    private function getPeriode($id_prodi)
    {
        $data = DB::table('jadwal_kuliah')
                ->where('id_prodi', $id_prodi)
                ->where('jenis', 1)
                ->orderBy('id_smt', 'desc')
                ->first();

        return $data->id_smt;
    }

    private function jadwalKuliahMahasiswa($id_mhs_reg, $jenis = 1)
    {
        $data = DB::table('jadwal_kuliah as jdk')
                ->join('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
                ->join('dosen as dos', 'dm.id_dosen', 'dos.id')
                ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
                ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                ->select('jdk.*','mkur.id_mk','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'pr.jenjang','pr.nm_prodi','mkur.smt', 'dos.gelar_depan','dos.nm_dosen','dos.gelar_belakang', 'dm.id_dosen')
                ->where('jdk.jenis', $jenis)
                ->where('n.id_mhs_reg', $id_mhs_reg)
                ->orderBy('mk.nm_mk','asc');

        return $data;
    }

    private function namaDosen($depan,$tengah,$belakang)
    {
        $nama_dosen = '';
        if ( !empty($depan) ) {
            $nama_dosen .= $depan.' ';
        }
        if ( !empty($tengah) ) {
            $nama_dosen .= $tengah;
        }
        if ( !empty($belakang) ) {
            $nama_dosen .= ', '.$belakang;
        }
        return $nama_dosen;
    }
}
