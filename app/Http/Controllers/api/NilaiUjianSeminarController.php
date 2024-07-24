<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bimbinganmhs, App\Bimbingandetail, App\Ujianakhir, App\Mahasiswareg, App\Dosen;
use Rmt, Carbon, Response, DB;

class NilaiUjianSeminarController extends Controller
{
    use Library;

	protected $view;

    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        $periode = Rmt::semesterBerjalan();

        $ta_berjalan = $periode->id_smt;

        if ( empty($r->ta) ) {

            $ta_aktif = $periode->id_smt;

        } else {

            $ta_aktif = $r->ta;
        }

    	$query = Rmt::seminarDosen($ta_aktif, $r->id);

        if ( !empty($r->cari) ) {

            $query->where(function($q)use($r){
                $q->where('m1.nim', 'LIKE', '%'.$r->cari.'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.$r->cari.'%');
            });
        }

        $mahasiswa = $query->get();

        $result_ta = $this->listSemester($r->id, $ta_aktif, $ta_berjalan);

        $data = ['semester' => $result_ta, 'mahasiswa' => $mahasiswa];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    private function listSemester($id_dosen, $periode, $periode_berjalan)
    {

        $smt = DB::table('penguji as p')
                ->where('p.id_dosen', $id_dosen)
                ->selectRaw('min(p.id_smt) as smt_1')
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

    public function detail(Request $r)
    {

        $mhs =  Mahasiswareg::where('nim', $r->nim)->first();

        $mahasiswa = [
            'id' => $mhs->id,
            'nama' => $mhs->mhs->nm_mhs,
            'nim' => $mhs->nim,
            'id_prodi' => $mhs->id_prodi
        ];

        $query_ujian = DB::table('penguji as p')
            ->leftJoin('ujian_akhir as ua', function($join){
                $join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
                $join->on('ua.id_smt', '=', 'p.id_smt');
                $join->on('ua.jenis', '=', 'p.jenis');
            })
            ->select('p.jenis')
            ->whereNotNull('ua.pukul')
            ->where('p.id_mhs_reg', $mhs->id)
            ->where('p.id_dosen', $r->id_dosen)
            ->where('p.id_smt', $r->id_smt)
            ->groupBy('p.jenis')
            ->get()
            ->toArray();

        $list_ujian = [];

        if ( !empty($query_ujian) ) {

            foreach( $query_ujian as $m ) {
                $list_ujian[] = [
                    'key' => $m->jenis,
                    'ket' => Rmt::jnsSeminar($m->jenis)
                ];
            }

            $penguji = DB::table('penguji as p')
                        ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                        ->select('d.id','p.jabatan', DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as nm_dosen'),'p.nilai')
                        ->where('p.id_mhs_reg', $mhs->id)
                        ->whereNotNull('d.id')
                        ->where('p.jenis', $r->jenis)
                        ->where('p.id_smt', $r->id_smt)
                        ->get();

        } else {

            $result = [
                'valid' => false,
                'ket' => 'Belum ada jadwal ujian',
                'jenis' => $r->jenis,
                'penguji' => [],
                'ta' => Rmt::namaTa($r->id_smt),
                'mahasiswa' => $mahasiswa
            ];

            return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
        }

        $result = [
            'valid' => true,
            'list_ujian' => $list_ujian,
            'jenis' => $r->jenis,
            'penguji' => $penguji,
            'ta' => Rmt::namaTa($r->id_smt),
            'mahasiswa' => $mahasiswa
        ];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function edit(Request $r)
    {
        try {
            $mhs =  Mahasiswareg::where('nim', $r->nim)->first();

            $mahasiswa = [
                'id' => $mhs->id,
                'nama' => $mhs->mhs->nm_mhs,
                'nim' => $mhs->nim,
                'id_prodi' => $mhs->id_prodi
            ];

            $ujian_name = $r->jenis == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL PENELITIAN';

            $ujian_name = $r->jenis == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian_name;

            // Cek Jadwal Ujian Seminar
                $query_ujian = DB::table('penguji as p')
                                ->leftJoin('ujian_akhir as ua', function($join){
                                    $join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
                                    $join->on('ua.id_smt', '=', 'p.id_smt');
                                    $join->on('ua.jenis', '=', 'p.jenis');
                                })
                                ->select('p.jenis')
                                ->whereNotNull('ua.pukul')
                                ->where('p.id_mhs_reg', $mhs->id)
                                ->where('p.id_dosen', $r->id_dosen)
                                ->where('p.id_smt', $r->id_smt)
                                ->groupBy('p.jenis')
                                ->get()
                                ->toArray();

                if ( empty($query_ujian) ) {
                    $result = [
                        'valid' => false,
                        'list_penilaian' => [],
                        'aksi' => '',
                        'jenis' => $r->jenis,
                        'id_smt' => $r->id_smt,
                        'mahasiswa' => $mahasiswa
                    ];

                    return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

                } else {

                    $list_ujian = [];

                    foreach( $query_ujian as $m ) {
                        $list_ujian[] = [
                            'key' => $m->jenis,
                            'ket' => Rmt::jnsSeminar($m->jenis)
                        ];
                    }
                }


            $nilai = DB::table('nilai_seminar as ns')
                    ->leftJoin('penguji as p', 'ns.id_penguji', 'p.id')
                    ->select('ns.*')
                    ->where('p.id_dosen', $r->id_dosen)
                    ->where('p.jenis', $r->jenis)
                    ->where('p.id_mhs_reg', $mhs->id)
                    ->orderBy('ns.kriteria_penilaian')
                    ->groupBy('ns.kriteria_penilaian')
                    ->get();

            $list_penilaian = [];

            $kriteria_penilaian = $this->kriteriaPenilaian($mhs->id_prodi);

            if ( count($nilai) == 0 ) {

                $aksi = 'insert';
                
                if ( $mhs->id_prodi == 61101 ) {
                    $ujian_name2 = $ujian_name == 'AKHIR TESIS / UJIAN TUTUP' ? 'Ujian '.ucwords(strtolower($ujian_name)) : ucwords(strtolower($ujian_name));
                    array_push($kriteria_penilaian, $ujian_name2);
                }

                foreach( $kriteria_penilaian as $key => $val ) {
                    $list_penilaian[] = [
                        'indeks' => $key,
                        'id' => '',
                        'kriteria' => $val,
                        'nilai' => ''
                    ];
                }

            } else {
                $aksi = 'update';

                $list_penilaian = [];

                foreach( $nilai as $n ) {

                    if ( isset($kriteria_penilaian[$n->kriteria_penilaian]) ) {
                        
                        $kriteria_name = $kriteria_penilaian[$n->kriteria_penilaian];

                    } else {
                        $kriteria_name = $ujian_name == 'AKHIR TESIS / UJIAN TUTUP' ? 'Ujian '.ucwords(strtolower($ujian_name)) : ucwords(strtolower($ujian_name));
                    }

                    $list_penilaian[] = [
                        'indeks' => '',
                        'id' => $n->id,
                        'kriteria' => $kriteria_name,
                        'nilai' => $n->nilai
                    ];
                }

            }

            $result = [
                'valid' => true,
                'aksi' => $aksi,
                'jenis' => $r->jenis,
                'id_smt' => $r->id_smt,
                'mahasiswa' => $mahasiswa,
                'list_penilaian' => $list_penilaian,
                'list_ujian' => $list_ujian
            ];


            return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

        } catch(\Exception $e ) {
            return Response::json($e->getMessage(), 422, [], JSON_UNESCAPED_SLASHES);
        }
    }

    private function kriteriaPenilaian($prodi, $key = null)
    {
        $s1 = [
                'Penampilan',
                'Penguasaan Materi Skripsi',
                'Penguasaan Materi Ilmu',
                'Kemampuan beragumentasi'
            ];
        $s2 = [
                'Orisinalitas',
                'Kedalaman dan ketajaman',
                'Keterkaitan antara judul, masalah/focus,hipotesis (jika ada), kajian pustaka, pembahasan, simpulan dan saran',
                'Kegunaan dan kemutakhiran',
                'Ketepatan metode, analisis dan hasil penelitian',
                'Penguasaan materi',
                'Kejujuran dan objektivitas'
            ];

        if ( empty($key) ) {
            return $prodi == '61101' ? $s2 : $s1;
        } else {
            return $prodi == '61101' ? $s2[$key] : $s1[$key];
        }
    }

    public function update(Request $r)
    {
        $nilai = json_decode($r->nilai);
        $data = json_decode($r->data);

        // validasi input
        foreach( $nilai as $key => $val ) {

            if ( empty($val->nilai) ) {
                return Response::json('Masih ada nilai yang kosong.', 422);
            }

            if ( !is_numeric($val->nilai) ) {
                return Response::json('Nilai hanya boleh angka.', 422);
            }
            if ( $val->nilai > 100 ) {
                return Response::json('Maksimal nilai pada masing-masing kriteria tidak boleh lebih dari 100. Mohon periksa kembali inputan anda.', 422);
            }
        }

        try {

            DB::beginTransaction();

            $total_nilai = 0;

            if ( $data->aksi == 'insert' ) {

                $penguji = DB::table('penguji')
                            ->where('id_dosen', $data->id_dosen)
                            ->where('jenis', $data->jenis)
                            ->where('id_mhs_reg', $data->id_mhs_reg)
                            ->where('id_smt', $data->id_smt)
                            ->first();

                // return Response::json($penguji->id, 422);

                foreach( $nilai as $key => $val ) {

                    $total_nilai += $val->nilai;

                    $insertData = [
                        'id_penguji' => $penguji->id,
                        'kriteria_penilaian' => $key,
                        'nilai' => $val->nilai
                    ];

                    DB::table('nilai_seminar')->insert($insertData);
                }


            } elseif ( $data->aksi == 'update' ) {

                foreach( $nilai as $key => $val ) {

                    $total_nilai += $val->nilai;

                    $insertData = [ 'nilai' => $val->nilai ];

                    DB::table('nilai_seminar')
                        ->where('id', $val->id)
                        ->update($insertData);
                }

            } else {
                return Response::json(['Aksi tidak diketahui'], 422);
            }

            $rata2 = number_format($total_nilai / count($nilai), 2);

            // Update nilai di table penguji
            DB::table('penguji')
                ->where('id_mhs_reg', $data->id_mhs_reg)
                ->where('id_dosen', $data->id_dosen)
                ->where('id_smt', $data->id_smt)
                ->where('jenis', $data->jenis)
                ->update(['nilai' => $rata2]);

            // Update nilai di KRS jika ada
            $this->storeNilaiInKrs($data);

            DB::commit();

            Rmt::success('Berhasil menyimpan data');

            return Response::json([]);

        } catch( \Exception $e ) {

            DB::rollback();
            return Response::json([$e->getMessage()], 422);
        }
    }

    // controller untuk store rekap nilai
    public function storeNilaiInKrs($r)
    {

        try {

            if ( $r->jenis == 'S' ) {
                
                // Nilai skripsi = (nil. proposal + hasil + skripsi)/3;
                $this->nilaiStoreSkripsi($r);
            
            } else {

                // Set pembagi nol karena mengambil semua penguji termasuk dosen yang menginput sekarang
                $pembagi = 0;
                $total_nilai = 0;

                $penguji = DB::table('penguji as p')
                    ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                    ->select('p.nilai')
                    ->where('p.id_mhs_reg', $r->id_mhs_reg)
                    ->whereNotNull('d.id')
                    ->where('p.jenis', $r->jenis)
                    ->where('p.id_smt', $r->id_smt)
                    ->get();

                // Kumpulkan nilai semua penguji
                foreach( $penguji as $val ) {
                    
                    // Jika masih ada nilai penguji yang belum masuk
                    // batalkan operasi
                    if ( empty($val->nilai) ) {
                        return false;
                    }

                    $total_nilai += (int)$val->nilai;

                    if ( !empty($val->nilai) ) {
                        $pembagi += 1;
                    }

                }


                if ( empty($total_nilai) ) {
                    
                    $nilai_akhir = 0;

                } else {

                    $nilai_akhir = number_format($total_nilai / $pembagi,2);
                }


                // Update nilai
                // Cek apakah ada krs-an untuk proposal/hasil
                $cek_krs = DB::table('nilai as n')
                            ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                            ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                            ->select('n.id')
                            ->where('mk.ujian_akhir', $r->jenis)
                            ->where('n.id_mhs_reg', $r->id_mhs_reg)
                            ->first();

                if ( !empty($cek_krs) ) {

                    $nilai_huruf = $this->grade($r->id_prodi, $nilai_akhir);
                

                    $skala_nil = DB::table('skala_nilai')
                        ->where('nilai_huruf', $nilai_huruf)
                        ->where('id_prodi', $r->id_prodi)
                        ->first();

                    if ( !empty($skala_nil) ) {
                        DB::table('nilai')
                                ->where('id', $cek_krs->id)
                                ->update(['nilai_huruf' => $nilai_huruf, 'nilai_indeks' => $skala_nil->nilai_indeks]);
                    }

                } else {

                    // Ambil krs skripsi
                    $in_krs = DB::table('nilai as n')
                            ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                            ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                            ->select('n.id')
                            ->where('mk.ujian_akhir', 'S')
                            ->where('n.id_mhs_reg', $r->id_mhs_reg)
                            ->where('jdk.id_smt', $r->id_smt)
                            ->first();
                            
                    if ( !empty($in_krs) ) {

                        if ( $r->jenis == 'P' ) {

                            // Jika proposal simpan nilai pada field nilai_mid
                            DB::table('nilai')
                                ->where('id', $in_krs->id)
                                ->update(['nil_mid' => $nilai_akhir]);
                        
                        } elseif ( $r->jenis == 'H' ) {

                            // Jika hasil simpan nilai pada field nilai_final
                            DB::table('nilai')
                                ->where('id', $in_krs->id)
                                ->update(['nil_final' => $nilai_akhir]);
                        }
                    }
                }

            }

        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }
    }

    private function nilaiStoreSkripsi($r)
    {
        try {
            // Cek apakah ada krs proposal atau skripsi
            $cek = DB::table('nilai as n')
                        ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                        ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                        ->select('n.id')
                        ->whereIn('mk.ujian_akhir', ['P','H'])
                        ->where('n.id_mhs_reg', $r->id_mhs_reg)
                        ->where('jdk.id_smt', $r->id_smt)
                        ->count();
            
            if ( $cek > 0 ) {
                $jenis = "('S','H','P')";
            } else {
                $jenis = "('S')";
            }

            $pembagi = 0;
            $total_nilai = 0;

            $penguji = DB::select("SELECT FORMAT(total_nilai/pembagi,2) as rekap_nilai from 
                        (select p.*,
                            (select count(id)
                                from penguji where jenis= p.jenis
                                    and id_mhs_reg = p.id_mhs_reg
                                    and nilai is not null
                                    and id_smt = p.id_smt) as pembagi,
                            (select sum(nilai)
                                from penguji where jenis= p.jenis
                                    and id_mhs_reg = p.id_mhs_reg
                                    and nilai is not null
                                    and id_smt = p.id_smt) as total_nilai
                            from penguji as p
                                where id_mhs_reg='$r->id_mhs_reg'
                                    and id_smt=$r->id_smt
                                    and nilai is not null
                                    and jenis in $jenis
                                group by jenis ) as res");

            foreach ( $penguji as $val ) {
                $pembagi += 1;
                $total_nilai += $val->rekap_nilai;
            }

            if ( $pembagi == 0 ) {
                abort(422, 'Pembagi 0');
            }

            if ( empty($total_nilai) ) {
                $rekap_nilai = 0;
            } else {
                $rekap_nilai = number_format($total_nilai / $pembagi,2);
            }


            $in_krs = DB::table('nilai as n')
                        ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                        ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                        ->select('n.id')
                        ->where('mk.ujian_akhir', 'S')
                        ->where('n.id_mhs_reg', $r->id_mhs_reg)
                        ->where('jdk.id_smt', $r->id_smt)
                        ->first();

            if ( !empty($in_krs) ) {

                $nilai_huruf = $this->grade($r->id_prodi, $rekap_nilai);

                $skala_nil = DB::table('skala_nilai')
                    ->where('nilai_huruf', $nilai_huruf)
                    ->where('id_prodi', $r->id_prodi)
                    ->first();

                if ( !empty($skala_nil) ) {
                    // Update nilai
                    DB::table('nilai')
                        ->where('id', $in_krs->id)
                        ->update(['nilai_huruf' => $nilai_huruf, 'nilai_indeks' => $skala_nil->nilai_indeks]);
                }
            }

        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }

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
