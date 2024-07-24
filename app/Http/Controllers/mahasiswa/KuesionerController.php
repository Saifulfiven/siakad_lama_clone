<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sia, Rmt, DB, Response;

class KuesionerController extends Controller
{
    public function index(Request $r)
    {

        $data['jadwal'] = $this->jadwalKuliahMahasiswa(Sia::sessionMhs(), 1)
                            ->where('jdk.id_smt', Sia::sessionPeriode())
                            ->where('jdk.hari', '<>', 0)
                            ->get();

        $data['kues_aktif'] = DB::table('kues_jadwal')
                                ->where('aktif', 1)
                                ->where('id_prodi', Sia::sessionMhs('prodi'))
                                ->first();

        return view('mahasiswa-member.kuesioner.index', $data);
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

    public function add(Request $r)
    {
        $data['dosen'] = DB::table('dosen')->where('id', $r->dos)->first();
        $data['mk'] = DB::table('matakuliah')->where('id', $r->mk)->first();
        $data['komponen'] = DB::table('kues_komponen')
                            ->where('id_prodi', Sia::sessionMhs('prodi'))
                            ->where('aktif', 1)
                            ->orderBy('urutan')
                            ->get();

        if ( empty($data['dosen']->id) || empty($data['mk']->id) ) {
            return redirect(route('mhs_kues'));
        }

        return view('mahasiswa-member.kuesioner.add', $data);
    }

    public function store(Request $r)
    {
        if ( $r->pg && empty(count($r->penilaian)) ) {
            return Response::json(['Anda belum mengisi penilaian.'], 422);
        }

        if ( !$r->pg && $r->text && empty(count($r->penilaian_text))) {
            return Response::json(['Anda belum mengisi penilaian.'], 422);
        }

        try {

            // Simpan kuesioner
            $data_kues = [
                'id_mhs_reg' => Sia::sessionMhs(),
                'id_jdk' => $r->id_jdk,
                'id_mk' => $r->id_mk,
                'kode_kls' => $r->kode_kls,
                'ruangan' => $r->ruangan,
                'id_dosen' => $r->id_dosen,
                'id_kues_jadwal' => $r->id_kues_jadwal
            ];

            $id_kues = DB::table('kues')->insertGetId($data_kues);

            if ( !empty(count($r->penilaian)) )
            {
                foreach( $r->penilaian as $key => $val )
                {
                    $data = [
                        'id_kues' => $id_kues,
                        'id_komponen_isi' => $key,
                        'penilaian' => $val,
                    ];

                    DB::table('kues_hasil')->insert($data);
                }
            }

            if ( !empty(count($r->penilaian_text)) )
            {
                foreach( $r->penilaian_text as $key => $val )
                {
                    $data = [
                        'id_kues' => $id_kues,
                        'id_komponen_isi' => $key,
                        'penilaian_text' => $val,
                    ];

                    DB::table('kues_hasil')->insert($data);
                }
            }

            Rmt::success(true);

        } catch( \Exception $e ) {
            return Response::json(['Terjadi kesalahan. Silahkan coba menyimpan kembali'.$e->getMessage()], 422);
        }
    }

}
