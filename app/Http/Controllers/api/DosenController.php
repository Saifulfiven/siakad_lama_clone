<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response, Carbon, DB;
use App\Dosen, App\AbsenMhs;

class DosenController extends Controller
{
    use Library;
    use LmsDsnController;

    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {

        $data = [];

        foreach( $this->dosen($r->limit, $r->skip, $r->cari) as $res ) {

            if ( empty($res->foto) ) {

                $foto = url('resources')."/assets/img/avatar.jpg";

            } else {
                $foto = config('app.url-foto-dosen').'/'.$res->foto;
            }

            $nama = !empty($res->gelar_depan) ? $res->gelar_depan.' ' : '';
            $nama .= $res->nm_dosen;
            $nama .= !empty($res->gelar_belakang) ? ', '.$res->gelar_belakang : '';

            $data[] = [
                'id' => $res->id, 
                'nama' => $nama, 
                'foto' => $foto
            ];
        }

        $result = ['error' => 0, 'count' => count($data), 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function profil($id)
    {
        $dosen = Dosen::findOrFail($id);

        if ( empty($dosen->foto) ) {

            $foto = url('resources')."/assets/img/avatar.jpg";

        } else {
            $foto = config('app.url-foto-dosen').'/'.$dosen->foto;
        }

        $nama = !empty($dosen->gelar_depan) ? $dosen->gelar_depan.' ' : '';
        $nama .= $dosen->nm_dosen;
        $nama .= !empty($dosen->gelar_belakang) ? ', '.$dosen->gelar_belakang : '';

        $data[] = [
            'nama' => $nama, 
            'hp' => $dosen->hp, 
            'foto' => $foto,
            'nidn' => $dosen->nidn,
            'email' => $dosen->email,
            'jenis' => $this->jenisDosen($dosen->jenis_dosen),
            'status' => $dosen->aktif == 1 ? 'Aktif' : 'Non Aktif'
        ];

        $result = ['error' => 0, 'count' => count($data), 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    private function jenisDosen($get = null)
    {
        if ( empty($get) ) {
            return ['DTY','DPK','DLB'];
        } else {
            switch ( $get ) {
                case 'DTY':
                        $jenis = 'Dosen Tetap Yayasan';
                    break;
                case 'DPK':
                    $jenis = 'Dosen Pembantu Kopertis';
                    break;
                case 'DLB':
                    $jenis = 'Dosen Luar Biasa';
                    break;
                default:
                    $jenis = '-';
                    break;
            }
            return $jenis;
        }
    }

    public function penilaian(Request $r)
    {

        $periode = Rmt::semesterBerjalan();

        $ta_berjalan = $periode->id_smt;

        if ( empty($r->ta) ) {

            $ta_aktif = $periode->id_smt;

        } else {

            $ta_aktif = $r->ta;
        }

        $jenis = empty($r->jenis) ? 1 : $r->jenis;

        $jadwal = $this->getJadwalMengajar()
                    ->where('dm.id_dosen', $r->id)
                    ->whereNotNull('dm.id_dosen')
                    ->where('jdk.id_smt', $ta_aktif)
                    // ->where('jdk.jenis', $jenis)
                    // ->where('jdk.id_prodi','<>','61101')
                    ->get();

        // Aktifkan jika membuka penginputan nilai
        // $result_ta = $this->listSemester($r->id, $ta_aktif, $ta_berjalan);

        // Aktifkan jika mendisabled penginputan nilai mundur
        $result_ta = DB::table('semester')
                ->where('id_smt', $ta_aktif)
                ->select('id_smt','nm_smt', DB::raw(''.$ta_aktif.' as ta_aktif'))
                ->orderBy('id_smt','desc')
                ->get();

        $data = ['semester' => $result_ta, 'jadwal' => $jadwal];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function penilaianDetail(Request $r)
    {

        if ( empty($r->id_jdk) ) {
            return Response::json(['error' => 1, 'msg' => 'tidak ada data bisa ditampilkan']);
        }

        $peserta_kelas = $this->pesertaKelas($r->id_jdk);

        $skala_nilai = $this->skalaNilai($r->id_prodi);

        $data = ['peserta_kelas' => $peserta_kelas, 'skala_nilai' => $skala_nilai];

        return Response::json(['error' => 0, 'data' => $data]);
    }

    public function penilaianHitung(Request $r)
    {
        $data = json_decode($r->nilai);

        $hadir = $this->persenNilai('hadir');
        $tugas = $this->persenNilai('tugas');
        $uts = $this->persenNilai('uts');
        $uas = $this->persenNilai('uas');

        $n_hadir = is_array(@$data->kehadiran) ? 0 : @$data->kehadiran * $hadir;
        $n_tugas = is_array(@$data->tugas) ? 0 : @$data->tugas * $tugas;
        $n_uts = is_array(@$data->mid) ? 0 : @$data->mid * $uts;
        $n_uas = is_array(@$data->final) ? 0 : @$data->final * $uas;

        $total = $n_hadir + $n_tugas + $n_uts + $n_uas;
        $total = round($total,2);

        $grade = $this->grade($r->prodi, $total);

        $html = $total.' = '.$grade;
        return Response::json(['nilai' => $total, 'grade' => $grade, 'html' => $html]);

    }

    private function persenNilai($jenis)
    {
        $persen = 0;

        switch ($jenis) {
            case 'hadir':
                $persen = 0;
            break;
            case 'tugas':
                $persen = 0.4;
            break;
            case 'uts':
                $persen = 0.3;
            break;
            case 'uas':
                $persen = 0.3;
            break;
            default:
                # code...
                break;
        }

        return $persen;
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

    public function penilaianStore(Request $r)
    {
        $data = json_decode($r->nilai);

        // BLOK INPUT NILAI UNTUK SEMENTARA
        // return Response::json(['error' => 1, 'msg' => 'Penginputan nilai telah berakhir']);

        try {

            DB::transaction(function()use($r,$data,&$nilai_angka){

                foreach( $data as $val ) {

                    $nilai_huruf = $val->nilai_huruf;
                    $nilai_angka = 0;

                    if ( empty($val->nilai_huruf) ) {

                        if ( empty($val->nilai_hadir) &&
                             empty($val->nilai_tugas) &&
                             empty($val->nilai_mid) &&
                             empty($val->nilai_final) )
                        {
                            $nilai_indeks = 0.00;
                        } else {
                            $nil = $this->hitungNilai($val->nilai_hadir, $val->nilai_tugas, $val->nilai_mid, $val->nilai_final, $r->prodi);
                            $nilai_huruf = $nil[1];
                            $nilai_angka = $nil[0];
                            $nilai_indeks = $this->nilaiIndeks($r->prodi, $nilai_huruf);
                        }
                    } else {
                        $nilai_indeks = $this->nilaiIndeks($r->prodi, $nilai_huruf);
                        $nil = $this->hitungNilai($val->nilai_hadir, $val->nilai_tugas, $val->nilai_mid, $val->nilai_final);
                        $nilai_angka = $nil[1];
                    }

                    DB::table('nilai')
                        ->where('id', $val->id_nilai)
                        ->update([
                            'nil_kehadiran' => $val->nilai_hadir,
                            'nil_tugas' => $val->nilai_tugas,
                            'nil_mid' => $val->nilai_mid,
                            'nil_final' => $val->nilai_final,
                            'nilai_angka' => $nilai_angka,
                            'nilai_huruf' => $nilai_huruf,
                            'nilai_indeks' => $nilai_indeks
                        ]);
                }

            });

        } catch( \Exception $e ) {
            return Response::json(['error' => 1, 'msg' => 'Terjadi kesalahan, ulangi lagi '.$e->getMessage()]);
        }

        return Response::json(['error' => 0, 'msg' => 'Sukses']);
    }

    private function hitungNilai($hadir,$tugas,$mid,$final, $prodi = '')
    {
        $p_hadir = $this->persenNilai('hadir');
        $p_tugas = $this->persenNilai('tugas');
        $p_uts = $this->persenNilai('uts');
        $p_uas = $this->persenNilai('uas');

        $n_hadir = $hadir * $p_hadir;
        $n_tugas = $tugas * $p_tugas;
        $n_uts = $mid * $p_uts;
        $n_uas = $final * $p_uas;

        $total = $n_hadir + $n_tugas + $n_uts + $n_uas;
        $total = round($total,2);
        
        if ( empty( $prodi ) ) {
            return ['',$total];
        } else {
            $grade = $this->grade($prodi, $total);

            return [$total, $grade];
        }

    }

    private function nilaiIndeks($prodi, $huruf)
    {
        $nil_indeks = DB::table('skala_nilai')
                        ->where('nilai_huruf', $huruf)
                        ->where('id_prodi', $prodi)
                        ->first();
        return !empty($nil_indeks) ? $nil_indeks->nilai_indeks : 0;
    }

    public function absensi(Request $r)
    {

        $periode = Rmt::semesterBerjalan();

        $ta_berjalan = $periode->id_smt;

        if ( empty($r->ta) ) {

            $ta_aktif = $periode->id_smt;

        } else {

            $ta_aktif = $r->ta;
        }

        $jenis = empty($r->jenis) ? 1 : $r->jenis;

        $jadwal = $this->getJadwalMengajar()
                    ->where('dm.id_dosen', $r->id)
                    ->whereNotNull('dm.id_dosen')
                    ->where('jdk.id_smt', $ta_aktif)
                    // ->where('jdk.jenis', $jenis)
                    // ->where('jdk.id_prodi','<>','61101')
                    ->get();

        $result_ta = $this->listSemester($r->id, $ta_aktif, $ta_berjalan);

        $data = ['semester' => $result_ta, 'jadwal' => $jadwal];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function absensiDetail(Request $r)
    {
        if ( empty($r->id_jdk) || empty($r->id_dosen) ) {
            return Response::json(['error' => 1, 'msg' => 'tidak ada data bisa ditampilkan']);
        }

        $this->insertAbsenDosen($r->id_jdk, $r->id_dosen);

        if ( empty($r->pertemuan) ) {

            $pert_aktif = DB::table('absen_dosen')
                            ->where('id_jdk', $r->id_jdk)
                            ->where('id_dosen', $r->id_dosen)
                            ->where('masuk', 0)
                            ->min('pertemuan');

            if ( empty($pert_aktif) ) {
                $pert_aktif = 'a_1';
            } else {
                $pert_aktif = 'a_'.$pert_aktif;
            }
        } else {
            $pert_aktif = $r->pertemuan;
        }

        $peserta_kelas = $this->pesertaKelasAbsen($r->id_jdk,$pert_aktif);

        $absen_dosen = $this->absenDosen($r->id_jdk, $r->id_dosen, $pert_aktif);

        $sekarang = Carbon::now();
        $absen_mhs = AbsenMhs::where('id_jdk', $r->id_jdk)
                        ->where('updated_at','>', $sekarang)->first();

        $absensi = [];

        if ( !empty($absen_mhs) ) {
            $absensi = [
                'waktu' => $absen_mhs->waktu,
                'pertemuan' => $absen_mhs->pertemuan_ke,
                'end_time' => $absen_mhs->updated_at,
            ];
        }

        $data = [
            'pertemuan_aktif' => $pert_aktif,
            'peserta_kelas' => $peserta_kelas,
            'absen_dosen' => $absen_dosen,
            'absensi' => $absensi
        ];

        return Response::json(['error' => 0, 'data' => $data]);
    }

    public function bukaAbsensi(Request $r)
    {

        // Cek apakah sudah ada pertemuan tersebut
        $cek = AbsenMhs::where('id_jdk', $r->id_jdk)
                ->where('pertemuan_ke', $r->pertemuan)
                ->count();

        if ( $cek > 0 ) {
            return Response::json(['Pertemuan ini telah dibuka sebelumnya'], 422);
        }

        // waktu < 5 menit
        if ( $r->waktu < 5 ) {
            return Response::json(['Waktu absen tidak boleh kurang dari 5 menit'], 422);
        }


        $now = Carbon::now();
        $end_time = Carbon::now()->addMinutes($r->waktu);

        $data = new AbsenMhs;
        $data->id_jdk = $r->id_jdk;
        $data->pertemuan_ke = $r->pertemuan;
        $data->waktu = $r->waktu;
        $data->created_at = $now;
        $data->updated_at = $end_time;
        $data->save();

        return Response::json('Berhasil menyimpan');
    }

    public function absensiStore(Request $r)
    {
        // return Response::json(['error' => 0, 'msg' => $r->])
        $data = json_decode($r->absensi);

        try {

            DB::transaction(function()use($r,$data){

                foreach( $data as $val ) {

                    DB::table('nilai')
                        ->where('id', $val->id_nilai)
                        ->update([
                            $r->pertemuan => $val->pertemuan ? 1 : NULL
                        ]);
                }

            });

        } catch( \Exception $e ) {
            return Response::json(['error' => 1, 'msg' => 'Terjadi kesalahan, ulangi lagi '.$e->getMessage()]);
        }

        return Response::json(['error' => 0, 'msg' => 'Sukses']);
    }

    public function absensiDosenStore(Request $r)
    {
        $data = json_decode($r->absensi);

        try {

            DB::transaction(function()use($r,$data){

                DB::table('absen_dosen')
                    ->where('id', $r->id)
                    ->update([
                        'masuk' => 1,
                        'tgl' => Carbon::parse($data->tanggal)->format('Y-m-d'),
                        'jam_masuk' => $data->jam_masuk,
                        'jam_keluar' => $data->jam_keluar,
                        'pokok_bahasan' => $data->pokok_bahasan,
                    ]);

            });

        } catch( \Exception $e ) {
            return Response::json(['error' => 1, 'msg' => 'Terjadi kesalahan, ulangi lagi '.$e->getMessage()]);
        }

        return Response::json(['error' => 0, 'msg' => 'Sukses']);
    }

    private function insertAbsenDosen($id_jdk, $id_dosen)
    {
        $cek = DB::table('absen_dosen')
                ->where('id_jdk', $id_jdk)
                ->where('id_dosen', $id_dosen)
                ->count();

        if ( $cek == 0 ) {

            $jdk = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', 'jk.id')
                ->select('jk.jam_masuk','jk.jam_keluar')
                ->where('jdk.id', $id_jdk)
                ->first();

            for ( $i = 1; $i <= 14; $i++ ) {
                $data = [
                    'id_dosen' => $id_dosen,
                    'id_jdk' => $id_jdk,
                    'pertemuan' => $i,
                    'jam_masuk' => $jdk->jam_masuk,
                    'jam_keluar' => $jdk->jam_keluar,
                ];
                DB::table('absen_dosen')->insert($data);
            }
        }
    }

    private function absenDosen($id_jdk, $id_dosen, $pert_aktif)
    {
        $pertemuan = substr($pert_aktif, 2,2);

        $data = DB::table('absen_dosen')
                ->where('id_dosen', $id_dosen)
                ->where('id_jdk', $id_jdk)
                ->where('pertemuan', $pertemuan)
                ->first();

        return $data;
    }

    private function pesertaKelasAbsen($id_jdk, $pertemuan)
    {
        $data = DB::table('nilai as n')
                ->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
                ->leftJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
                ->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
                ->select('n.id as id_nilai',$pertemuan.' as pertemuan','m2.nim','m1.nm_mhs')
                ->where('n.id_jdk', $id_jdk)
                ->orderBy('m2.nim')
                ->get();
        return $data;
    }

    private function dosen($perpage, $skip, $cari)
    {
        $perpage = empty($perpage) ? 20 : $perpage;
        $skip = empty($skip) ? 0 : $skip;

        $query = "SELECT * FROM dosen where aktif=1";

        if ( !empty($cari) ) {
            $query .= " and nm_dosen like '%$cari%'";
        }

        $query .= " order by nm_dosen asc
                    limit ".$skip.", ".$perpage;

        $data = DB::select($query);

        return $data;
    }

    private function getJadwalMengajar()
    {
        $query = DB::table('jadwal_kuliah as jdk')
                    ->leftJoin('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
                    ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                    ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                    ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                    ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
                    ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                    ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
                    ->select('jdk.id', 'pr.jenjang','jdk.hari','dm.dosen_ke',
                            DB::raw('upper(mk.nm_mk) as nm_mk'),
                            'pr.id_prodi','pr.nm_prodi','r.nm_ruangan',DB::raw('concat(left(jk.jam_masuk,5),\' - Kelas \', jdk.kode_kls) as jam_masuk'),
                            'smt.nm_smt','mkur.smt','jdk.jenis')
                    ->orderBy('jdk.id_prodi','asc')
                    ->orderBy('mkur.smt')
                    ->orderBy('jdk.hari','asc')
                    ->orderBy('jk.jam_masuk','asc');

        return $query;
    }

    private function listSemester($id_dosen, $periode, $periode_berjalan)
    {
        $smt = DB::table('dosen_mengajar as dm')
                    ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'dm.id_jdk')
                    ->selectRaw('min(jdk.id_smt) as smt_1')
                    ->where('dm.id_dosen', $id_dosen)
                    ->first();

        if ( !empty($smt) ) {
            $data = DB::table('semester')
                ->whereBetween('id_smt', [$smt->smt_1, $periode_berjalan])
                ->select('id_smt','nm_smt', DB::raw(''.$periode.' as ta_aktif'))
                ->orderBy('id_smt','desc')
                ->get();
        } else {
            $data = [];
        }

        return $data;
    }

}