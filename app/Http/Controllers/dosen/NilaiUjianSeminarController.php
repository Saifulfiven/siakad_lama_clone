<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bimbinganmhs, App\Bimbingandetail, App\Ujianakhir, App\Mahasiswareg, App\Dosen;
use Sia, Rmt, Session, Carbon, Response, DB;

class NilaiUjianSeminarController extends Controller
{
	protected $view;

	public function __construct()
	{
		$this->view = 'dsn.nilai-seminar';
	}

	private function filterAwal()
	{
		if ( !Session::has('seminar.smt') ) {
    		Session::put('seminar.smt', Sia::sessionPeriode());
    	}
	}

    public function index(Request $r)
    {
        $this->filterAwal();

    	$id_smt = Session::get('seminar.smt');

    	$seminar = Rmt::seminarDosen($id_smt, Sia::sessionDsn());

        if ( Session::has('seminar.cari') ) {

            $cari = Session::get('seminar.cari');
            $seminar->where(function($q)use($cari){
                $q->where('m1.nim', 'LIKE', '%'.$cari.'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.$cari.'%');
            });
        }

        $data['seminar'] = $seminar->paginate(20);

    	return view($this->view.'.index', $data);
    }

    public function detail(Request $r, $id_mhs_reg, $id_smt)
    {
        if ( !Session::has('seminar.smt') ) {
            Session::put('seminar.smt', $id_smt);
        }

        $data['id_smt'] = $id_smt;

        $data['mhs'] = Mahasiswareg::findOrFail($id_mhs_reg);

        $menguji = DB::table('penguji as p')
            ->leftJoin('ujian_akhir as ua', function($join){
                $join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
                $join->on('ua.id_smt', '=', 'p.id_smt');
                $join->on('ua.jenis', '=', 'p.jenis');
            })
            ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
            ->select('p.*')
            ->whereNotNull('ua.pukul')
            ->where('p.id_mhs_reg', $id_mhs_reg)
            ->where('p.id_dosen', Sia::sessionDsn())
            ->where('p.id_smt', $id_smt)
            ->groupBy('p.jenis')
            ->get()
            ->toArray();

        $data['menguji'] = [];

        if ( !empty($menguji) ) {

            foreach( $menguji as $m ) {
                $data['menguji'][$m->jenis] = $m->nilai;
            }

            if ( $r->jenis ) {
                Session::put('seminar.jenis', $r->jenis);
            } else {
                Session::put('seminar.jenis', $menguji[0]->jenis);
            }

        }

        return view($this->view.'.detail', $data);
    }

    public function nilai(Request $r)
    {
        try {
            $data['mhs'] = Mahasiswareg::findOrFail($r->id_mhs_reg);

            $data['nilai'] = DB::table('nilai_seminar as ns')
                    ->leftJoin('penguji as p', 'ns.id_penguji', 'p.id')
                    ->select('ns.*')
                    ->where('p.id_dosen', Sia::sessionDsn())
                    ->where('p.jenis', $r->jenis)
                    ->where('p.id_mhs_reg', $r->id_mhs_reg)
                    ->orderBy('ns.kriteria_penilaian')
                    ->get();

            $data['jenis'] = $r->jenis;
            $data['id_smt'] = $r->id_smt;

            return view($this->view.'.form-nilai', $data);

        } catch(\Exception $e ) {
            echo 'Error: '.$e->getMessage();
            echo '<br><br>';
        }
    }

    public function store(Request $r)
    {
        // validasi input
        foreach( $r->nilai as $key => $val ) {

            if ( !is_numeric($val) ) {
                return Response::json(['Nilai hanya boleh angka.'], 422);
            }
            if ( $val > 100 ) {
                return Response::json(['Maksimal nilai pada masing-masing kriteria tidak boleh lebih dari 100. Mohon periksa kembali inputan anda.'], 422);
            }
        }

        try {

            DB::beginTransaction();

            $total_nilai = 0;

            if ( $r->aksi == 'insert' ) {

                $penguji = DB::table('penguji')
                            ->where('id_dosen', Sia::sessionDsn())
                            ->where('jenis', $r->jenis)
                            ->where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('id_smt', $r->id_smt)
                            ->first();

                foreach( $r->nilai as $key => $val ) {

                    $total_nilai += $val;

                    $data = [
                        'id_penguji' => $penguji->id,
                        'kriteria_penilaian' => $key,
                        'nilai' => $val
                    ];

                    DB::table('nilai_seminar')->insert($data);
                }


            } elseif ( $r->aksi == 'update' ) {

                foreach( $r->nilai as $key => $val ) {

                    $total_nilai += $val;

                    $data = [ 'nilai' => $val ];

                    DB::table('nilai_seminar')
                        ->where('id', $key)
                        ->update($data);
                }

            } else {
                return Response::json(['Aksi tidak diketahui'], 422);
            }

            $rata2 = number_format($total_nilai / count($r->nilai), 2);

            // Update nilai di table penguji
            DB::table('penguji')
                ->where('id_mhs_reg', $r->id_mhs_reg)
                ->where('id_dosen', Sia::sessionDsn())
                ->where('id_smt', $r->id_smt)
                ->where('jenis', $r->jenis)
                ->update(['nilai' => $rata2]);

            // Update nilai di KRS jika ada
            $this->storeNilaiInKrs($r, $rata2);

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

                $nilai_huruf = Sia::grade($r->id_prodi, $nilai_akhir);
            

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
    }

    private function nilaiStoreSkripsi($r)
    {
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
            return false;
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

            $nilai_huruf = Sia::grade($r->id_prodi, $rekap_nilai);

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



    }

    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('seminar.cari',$r->cari);
        } else {
            Session::pull('seminar.cari');
        }

        return redirect(route('dsn_seminar'));
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('seminar.'.$r->modul);
            } else {
                Session::put('seminar.'.$r->modul,$r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull('seminar');
        }

        if ( $r->go ) {
            return redirect()->back();
        }

        return redirect(route('dsn_seminar'));
    }

    public function cetak(Request $r, $id_mhs_reg, $id_smt)
    {
        $data['mhs'] = Sia::mahasiswa()
                ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
                ->select('m2.nim','m1.nm_mhs','m2.id_prodi','p.nm_prodi','p.jenjang',
                        'm2.bebas_pembayaran','m2.jurnal_file','m2.jurnal_approved','m2.semester_mulai',
                        'k.nm_konsentrasi as konsentrasi')
                ->where('m2.id', $id_mhs_reg)
                ->first();

        $data['skripsi'] = DB::table('ujian_akhir')
                            ->where('id_mhs_reg', $id_mhs_reg)
                            ->where('jenis', $r->jenis)
                            ->where('id_smt', $id_smt)
                            ->first();

        $data['ketua'] = Sia::penguji($id_mhs_reg, $r->jenis, 'KETUA');
        $data['sekretaris'] = Sia::penguji($id_mhs_reg, $r->jenis, 'SEKRETARIS');

        $data['penguji'] = DB::table('penguji as p')
                        ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                        ->select(DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as penguji'),'p.id_smt', 'p.id_dosen', 'p.jabatan','p.id','p.nilai','d.ttd')
                        ->where('p.id_mhs_reg', $id_mhs_reg)
                        ->where('p.jenis', $r->jenis)
                        ->where('p.id_smt', $id_smt)
                        ->whereNotNull('p.id_dosen')
                        ->where('p.id_dosen','<>','')
                        ->orderBy('p.id')
                        ->take(4)
                        ->get();
        // dd($data['penguji']);

        $view_ = $r->prodi == 61101 ? 'cetak-s2' : 'cetak-s1';

        return view($this->view.'.'.$view_, $data);
    }
}
