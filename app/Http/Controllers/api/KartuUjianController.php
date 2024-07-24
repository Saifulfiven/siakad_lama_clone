<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response, DB, Carbon;
use App\Mahasiswareg, App\Mahasiswa;

class KartuUjianController extends Controller
{
    use Library;

    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $mhs2 = Mahasiswa::find($mhs->id_mhs);

        if ( empty($mhs) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa ditampilkan']);
        }
        
        if ( empty($mhs2->foto_mahasiswa) ) {
            $foto = $mhs2->jenkel == 'P' ? 'user-women.png' : 'user-man.png';
        } else {
            $foto = $mhs2->foto_mahasiswa;
        }

        if ( $mhs->id_prodi == 61101 ) {

            $periode = $this->semesterBerjalanS2($mhs->id);
            $jenis = $this->jenisUjianPasca($periode->id_smt);

            if ( !empty($periode) ) {
                $periode_id = $periode->id_smt;
                $periode_nama = $periode->nm_smt;
            } else {
                $periode = $this->semesterBerjalan($mhs->id);
                $periode_id = $periode['id'];
                $periode_nama = $periode['nama'];
            }
            
        } else {

            $periode = $this->semesterBerjalan($mhs->id);
            $periode_id = $periode['id'];
            $jenis = $this->jenisUjian($periode_id);
            $periode_nama = $periode['nama'];
        }

        $kartu = DB::table('kartu_ujian')
                ->where('id_mhs_reg', $mhs->id)
                ->where('id_smt', $periode_id)
                ->where('jenis', $jenis)
                ->count();
        $data_kartu = DB::table('kartu_ujian')
                ->where('id_mhs_reg', $mhs->id)
                ->where('id_smt', $periode_id)
                ->where('jenis', $jenis)
                ->first();

        $data = [ 'kartu' => $kartu, 'data_kartu' => $data_kartu, 'jenis' => $jenis, 'nim' => $mhs->nim, 'nama' => $mhs2->nm_mhs, 'foto' => $foto, 'smt' => $periode_nama ];

        $result = ['error' => 0, 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function statusKartu(Request $r)
    {
        $smt = DB::table('semester_aktif')
                ->where('id_fakultas', 1)
                ->first();

        $ta_aktif = empty($r->ta) || $r->ta == 'null' ? $smt->id_smt : $r->ta;

        $jenis = $this->jenisUjian($ta_aktif);

        $query = $this->jadwalUjian()
                        ->where('jdk.id_smt', $ta_aktif)
                        ->where('jdu.jenis_ujian', $jenis);

        if ( !empty($r->cari) ) {
            $query->where(function($q)use($r){
                $q->where('mk.kode_mk', 'like', '%'.$r->cari.'%')
                    ->orWhere('mk.nm_mk', 'like', '%'.$r->cari.'%')
                    ->orWhere('p.nama', 'like', '%'.$r->cari.'%')
                    ->orWhere('r.nm_ruangan', 'like', '%'.$r->cari.'%');
            });
        }

        $jadwal = $query->take(60)
                        ->orderBy('jdu.tgl_ujian')
                        ->orderBy('jdu.jam_masuk')
                        ->orderBy('mk.nm_mk')
                        ->orderBy('jdu.id','desc')->get();

        $jadwalArr = [];

        foreach( $jadwal as $jdk )
        {
            $jadwalArr[] = [
                'id' => $jdk->id,
                'hari' => ucfirst(Rmt::hari($jdk->hari)),
                'jam' => substr($jdk->jam_masuk,0,5),
                'mk' => strtoupper($jdk->nm_mk),
                'dosen' => $jdk->dosen,
                'prodi' => $jdk->nm_prodi.' - '. $jdk->jenjang,
                'ruangan' => $jdk->nm_ruangan,
                'pengawas' => $jdk->pengawas,
                'tgl' => Carbon::parse($jdk->tgl_ujian)->format('d/m/y')
            ];
        }

        $data = [
            'ta_aktif' => $ta_aktif,
            'jadwal' => $jadwalArr,
            'smt' => $this->listSmt(),
            'jenis' => $jenis
        ];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function statusKartudetail(Request $r)
    {
        $id = $r->id;

        $jdu = DB::table('jadwal_ujian as jdu')
                ->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
                ->leftJoin('ruangan as r', 'jdu.id_ruangan', 'r.id')
                ->select('jdu.*', 'p.nama', 'r.nm_ruangan')
                ->where('jdu.id', $id)->first();

        $peserta_ujian = $this->pesertaUjian($id);

        $jadwal = [
            'hari' => Rmt::hari($jdu->hari),
            'ruangan' => $jdu->nm_ruangan,
            'jam' => substr($jdu->jam_masuk,0,5),
            'tgl_ujian' => Carbon::parse($jdu->tgl_ujian)->format('d-m-Y'),
            'jml_peserta' => $jdu->jml_peserta,
            'pengawas' => $jdu->nama
        ];

        $peserta = [];

        foreach( $peserta_ujian as $res ) {
            $kartu = DB::table('kartu_ujian')
                    ->where('id_smt', '20181')
                    ->where('jenis', 'UAS')
                    ->where('id_mhs_reg', $res->id)
                    ->count();

            $peserta[] = [
                'nim' => $res->nim,
                'nm_mhs' => $res->nm_mhs,
                'status' => $kartu > 0 ? true : false
            ];
        }

        $data = [
            'jadwal' => $jadwal,
            'peserta' => $peserta
        ];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    private function jadwalUjian()
    {
        $query = DB::table('jadwal_ujian as jdu')
                    ->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
                    ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                    ->leftJoin('mk_kurikulum as mkur','mkur.id_mk','=','mk.id')
                    ->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
                    ->leftJoin('ruangan as r', 'jdu.id_ruangan','=','r.id')
                    ->leftJoin('prodi as pr', 'jdk.id_prodi', 'pr.id_prodi')
                    ->select('jdu.*','mk.nm_mk','mk.sks_mk','mk.kode_mk','jdk.id_smt',
                            'jdk.kode_kls','r.nm_ruangan', 'p.nama as pengawas',
                            'pr.nm_prodi','pr.jenjang','mkur.smt',
                            DB::raw('(select group_concat(distinct d.gelar_depan," ", d.nm_dosen,", ", d.gelar_belakang SEPARATOR \'<br>\') as dosen from dosen_mengajar as dm
                                            left join dosen as d on dm.id_dosen = d.id
                                            where dm.id_jdk=jdk.id) as dosen'))
                    ->orderBy('jdu.hari','asc')->orderBy('jdu.id_jdk')->orderBy('jdu.jam_masuk','asc');

        return $query;
    }

    private function pesertaUjian($id_jdu)
    {
        $data = DB::table('peserta_ujian as p')
                    ->leftJoin('mahasiswa_reg as m1', 'p.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('m1.id','m1.nim','m2.nm_mhs')
                    ->where('p.id_jdu', $id_jdu)
                    ->orderBy('m1.nim')
                    ->get();

        return $data;
    }
}