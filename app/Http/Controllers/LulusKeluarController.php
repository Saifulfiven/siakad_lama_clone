<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, Carbon, Excel;
use App\Mahasiswareg;

class LulusKeluarController extends Controller
{
    public function index(Request $r)
    {
        $query = Sia::mahasiswa()
            ->where('m2.id_jenis_keluar','<>', 0)
            ->select('m2.id as id_mhs_reg','m1.nm_mhs','m1.gelar_depan','m1.gelar_belakang','m1.jenkel','a.nm_agama','m1.tgl_lahir','p.nm_prodi','m2.pin',
                    'p.jenjang','m2.jam_kuliah','m2.semester_mulai','m2.nim','jk.ket_keluar',
                    'm2.semester_keluar','m2.tgl_keluar')
            ->orderBy('m2.seri_ijazah', 'asc');

        // Filter
        Sia::lulusKeluarFilter($query);

        $data['mahasiswa'] = $query->paginate(15);

        return view('lulus-keluar.index', $data);
    }

    public function filter(Request $r)
    {
        if ( $r->ajax() ) {
            Sia::filter($r->value,'lk_'.$r->modul);
        } else {
            Session::pull('lk_ta');
            Session::pull('lk_angkatan');
            Session::pull('lk_prodi');
            Session::pull('lk_status');
            Session::pull('lk_search');
            Session::pull('lk_jenkel');
            Session::pull('lk_jns_daftar');
            Session::pull('lk_ta_masuk');
            Session::pull('lk_pin');
        }
        
        return redirect(route('lk'));
    }

    public function cari(Request $r)
    {
        if ( !empty($r->q) ) {
            Session::put('lk_search',$r->q);
        } else {
            Session::pull('lk_search');
        }

        return redirect(route('lk'));
    }

    public function detail($id)
    {
        $data['mhs'] = DB::table('mahasiswa_reg as m2')
                ->rightJoin('mahasiswa as m1','m1.id', '=', 'm2.id_mhs')
                ->leftJoin('agama as a', 'm1.id_agama','=','a.id_agama')
                ->leftJoin('prodi as p', 'm2.id_prodi','=','p.id_prodi')
                ->leftJoin('jenis_keluar as jk', 'm2.id_jenis_keluar','=','jk.id_jns_keluar')
                ->leftJoin('semester as smt','smt.id_smt','m2.semester_keluar')
                ->where('m2.id_jenis_keluar','<>', 0)
                ->where('m2.id',$id)
                ->select('m2.id as id_mhs_reg','m2.ket_keluar as ket','m2.judul_skripsi','m1.nm_mhs','m2.nim','m2.id_jenis_keluar', 'm2.pin',
                            'm2.semester_keluar','m2.tgl_keluar', 'm2.sk_yudisium', 'm2.tgl_sk_yudisium','m2.ipk','m2.seri_ijazah','jk.ket_keluar','smt.nm_smt'
                        )
                ->first();

        return view('lulus-keluar.detail', $data);
    }

    public function add(Request $r)
    {
        if ( !empty($r->id_mhs_reg) && empty($r->judul) ) {
            $ipk = Sia::ipkLulus($r->id_mhs_reg);
            return Response::json(['error' => 0, 'msg' => $ipk]);
            exit;
        }

        // Get judul skripsi order jenis desc u/ antisipasi tidak ada data
        if ( !empty($r->judul) && !empty($r->judul) ) {
            $judul = DB::table('ujian_akhir')
                        ->select('judul_tmp')
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->orderBy('jenis','desc')
                        ->take(1)->first();

            return Response::json(['error' => 0, 'msg' => !empty($judul) ? strtoupper($judul->judul_tmp) :'' ]);
            exit;
        }

        return view('lulus-keluar.add');
    }

    public function mhs(Request $r )
    {
        $param = $r->input('query');
        if ( !empty($param) ) {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('id_prodi', Sia::getProdiUser())
                            ->where(function($q)use($param){
                                $q->where('m1.nim', 'like', '%'.$param.'%')
                                    ->orWhere('m2.nm_mhs', 'like', '%'.$param.'%');
                            })
                            ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
        } else {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('id_prodi', Sia::getProdiUser())
                            ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
        }

        $data = [];
        foreach( $mahasiswa as $r ) {
            $data[] = ['data' => $r->id, 'value' => $r->nim.' - '.$r->nm_mhs];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'mahasiswa' => 'required',
            'jenis_keluar' => 'required',
            'tgl_keluar' => 'required',
            'semester' => 'required'
        ]);

        // Cegah penginputan mahasiswa lulus jika akm belum terisi (khusus yg lulus)
        // yang mempunyai sks semester pada semester ini 
        // if ( $r->jenis_keluar == 1 ) {
        //     $rule = DB::table('aktivitas_kuliah')
        //             ->where('id_mhs_reg', $r->mahasiswa)
        //             ->where('id_smt', $r->semester)->count();
                    
        //     if ( $rule == 0 ) {
        //         return Response::json(['error' => 1, 'msg' => 'Aktivitas mahasiswa ini belum ada pada semester ini, buat aktivitas kulianya terlebih dahulu']);
        //     }
        // }

        try {
            // Dapatkan seri terakhir
            // Contoh : 1046/A.S1-STIE-NI/2018
            // rule : nomor urut minimal 4 digit
            $urut_seri = '';
            if ( $r->jenis_keluar == 1 ) {
                $last_seri = Mahasiswareg::whereIn('id_prodi', Sia::getProdiUser())
                                ->max('seri_ijazah');

                $urut_seri = explode('/',$last_seri);
                $urut_seri = $urut_seri[0] + 1;

                $jml_digit = strlen($urut_seri);

                if ( $jml_digit < 4 ) {
                    // Tambahkan 0 di depannya
                    $urut_seri = sprintf('%04d', $urut_seri);
                }
            }

            $mhs = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->where('m1.id', $r->mahasiswa)
                    ->select('nik')->first();
            
            if ( $r->jenis_keluar == 1 ) {
                if ( empty($mhs->nik) || strlen(trim($mhs->nik)) != 16 ) {
                    return Response::json(['error' => 1, 'msg' => 'NIK mahasiswa ini belum ada/belum lengkap']);
                }
            }


            DB::transaction(function()use($r, $urut_seri){

                $data = Mahasiswareg::find($r->mahasiswa);

                if ( $r->jenis_keluar == 1 ) {
                    $prodi = $data->prodi;

                    $seri_ijazah = $urut_seri;
                    $seri_ijazah .= "/".substr($prodi->nm_prodi,0,1);
                    $seri_ijazah .= ".".$prodi->jenjang."-ITB-NI/".date('Y');
                    
                    $data->seri_ijazah = $seri_ijazah;
                    $data->sk_yudisium = $r->sk_yudisium;
                    $data->tgl_sk_yudisium = Carbon::parse($r->tgl_sk_yudisium)->format('Y-m-d');
                    $data->ipk = str_replace(',', '.', $r->ipk);
                    $data->judul_skripsi = $r->judul_skripsi;
                    $data->jalur_skripsi = 1;
                } else {
                    $data->ket_keluar = $r->ket_keluar;
                }

                // Simpan
                $data->id_jenis_keluar = $r->jenis_keluar;
                $data->tgl_keluar = Carbon::parse($r->tgl_keluar)->format('Y-m-d');
                $data->semester_keluar = $r->semester;
                
                $data->save();
            });
         } 
         catch(\Exception $e)
         {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
         }

         Rmt::success('Berhasil menyimpan data');
         return Response::json(['error' => 0, 'msg' => ''], 200);
    }

    public function edit($id)
    {
        $data['mhs'] = Sia::mahasiswa()
            ->where('m2.id_jenis_keluar','<>', 0)
            ->where('m2.id',$id)
            ->select('m2.id as id_mhs_reg','m2.ket_keluar','m1.nm_mhs','m2.nim','m2.id_jenis_keluar',
                        'm2.semester_keluar','m2.tgl_keluar', 'm2.sk_yudisium', 'm2.tgl_sk_yudisium', 'm2.ipk','m2.seri_ijazah','m2.judul_skripsi')
            ->first();

        return view('lulus-keluar.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'mahasiswa' => 'required',
            'tgl_keluar' => 'required',
            'semester' => 'required'
        ]);

        try {
            $data = Mahasiswareg::find($r->mahasiswa);

            $data->tgl_keluar = Carbon::parse($r->tgl_keluar)->format('Y-m-d');
            $data->semester_keluar = $r->semester;
            $data->id_jenis_keluar = $r->id_jenis_keluar;
            
            if ( $r->id_jenis_keluar == 1 ) {

                $data->sk_yudisium = $r->sk_yudisium;
                $data->tgl_sk_yudisium = Carbon::parse($r->tgl_sk_yudisium)->format('Y-m-d');
                $data->ipk = str_replace(',', '.', $r->ipk);
                $data->judul_skripsi = $r->judul_skripsi;

            } else {
                $data->ket_keluar = $r->ket_keluar;
            }

            $data->save();
         } 
         catch(\Exception $e)
         {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
         }

         Rmt::success('Berhasil menyimpan data');
         return Response::json(['error' => 0, 'msg' => ''], 200);
    }

    public function delete($id)
    {
        $data = Mahasiswareg::find($id);

        $data->id_jenis_keluar = 0;
        $data->semester_keluar = '';
        $data->tgl_keluar = '';
        $data->sk_yudisium = '';
        $data->tgl_sk_yudisium = '';
        $data->ipk = '';
        $data->seri_ijazah = '';
        $data->save();

        Rmt::success('Berhasil menghapus data');
        return redirect()->back();
    }

    public function beritaAcaraYudisium(Request $r)
    {
        if ( !empty($r->smt) ) {
            Session::set('bay_smt', $r->smt);
        }

        if ( !empty($r->prodi) ) {
            Session::set('bay_prodi', $r->prodi);
        }

        if ( !Session::get('bay_smt') ) {
            Session::set('bay_smt', Sia::sessionPeriode());
            $prodi_user = Sia::getProdiUser();
            Session::set('bay_prodi', @$prodi_user[0]);
        }

        $data['lulus'] = DB::table('mahasiswa_reg as m')
                            ->leftJoin('prodi as pr', 'm.id_prodi', 'pr.id_prodi')
                            ->select('m.tgl_sk_yudisium as tgl','m.id_prodi','m.semester_keluar','pr.jenjang','pr.nm_prodi')
                            ->where('m.id_jenis_keluar', 1)
                            ->where('m.id_prodi', Session::get('bay_prodi'))
                            ->where('m.semester_keluar', Session::get('bay_smt'))
                            ->whereNotNull('m.tgl_sk_yudisium')
                            ->orderBy('m.tgl_sk_yudisium')
                            ->groupBy('m.tgl_sk_yudisium')
                            ->get();

        return view('lulus-keluar.berita-acara-yudisium', $data);
    }

    public function beritaAcaraYudisiumCetak(Request $r)
    {
        $data['prodi'] = \App\Prodi::where('id_prodi', $r->prodi)->first();
        $data['mahasiswa'] = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                            ->select('m1.nim','m1.ipk','m2.nm_mhs','m1.id_prodi',
                                DB::raw('(select sum(nilai) from penguji
                                        where id_mhs_reg=m1.id and jenis=\'S\') as angka'))
                            ->where('m1.tgl_sk_yudisium', $r->tgl)
                            ->where('m1.id_prodi', $r->prodi)
                            ->orderBy('m1.seri_ijazah','asc')
                            ->get();

        return view('lulus-keluar.berita-acara-yudisium-cetak', $data);
    }

    public function cetak(Request $r)
    {
        $query = Sia::mahasiswa()
            ->where('m2.id_jenis_keluar','<>', 0)
            ->select('m2.id as id_mhs_reg','m1.nm_mhs','m1.jenkel','a.nm_agama','m1.tgl_lahir','p.nm_prodi',
                    'p.jenjang','m2.jam_kuliah','m2.semester_mulai','m2.nim','m2.pin', 'm2.kode_batch_pin','jk.ket_keluar',
                    'm2.semester_keluar','m2.tgl_keluar')
            ->orderBy('m2.nim', 'desc');

        // Filter
        Sia::lulusKeluarFilter($query);

        $data['mahasiswa'] = $query->get();

        return view('lulus-keluar.print', $data);
    }

    public function ekspor(Request $r)
    {
        $query = Sia::mahasiswa()
            ->leftJoin('konsentrasi as kon', 'm2.id_konsentrasi', 'kon.id_konsentrasi')
            ->where('m2.id_jenis_keluar','<>', 0)
            ->select('m2.id as id_mhs_reg','m1.nm_mhs','m1.jenkel','a.nm_agama','m1.tgl_lahir','p.nm_prodi','m1.gelar_depan','m1.gelar_belakang','p.singkatan_gelar',
                    'p.jenjang','m2.jam_kuliah','m2.semester_mulai','m2.nim','jk.ket_keluar',
                    'm1.nm_ibu','m1.nm_ayah','m1.alamat','m1.hp','m1.tempat_lahir','m2.judul_skripsi','m2.seri_ijazah','m2.pin','m2.kode_batch_pin',
                    'm2.semester_keluar','m2.tgl_keluar','m2.ipk','kon.nm_konsentrasi')
            ->orderBy('m2.seri_ijazah', 'asc');

        // Filter
        Sia::lulusKeluarFilter($query);

        $data['mahasiswa'] = $query->get();

        try {
            Excel::create('Mahasiswa Lulus-Keluar', function($excel)use($data) {

                $excel->sheet('Sheet', function($sheet)use($data) {

                    $sheet->loadView('lulus-keluar.ekspor', $data);

                });

            })->download('xlsx');;
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function html(Request $r)
    {
        if ( empty($r->tgl_mulai) ) {
            dd('Tanggal mulai belum diisi');
        }

        if ( empty($r->tgl_akhir) ) {
            $tgl_akhir = Carbon::today()->format('Y-m-d');
        } else {
            $tgl_akhir = Carbon::parse($r->tgl_akhir)->format('Y-m-d');
        }

        $tgl_mulai = Carbon::parse($r->tgl_mulai)->format('Y-m-d');

        if ( $tgl_akhir < $tgl_mulai ) {
            dd('Invalid input tanggal');
        }

        $query = Sia::mahasiswa()
            ->leftJoin('konsentrasi as kon', 'm2.id_konsentrasi', 'kon.id_konsentrasi')
            ->where('m2.id_jenis_keluar', '<>', 0)
            // ->whereNotIn('m2.nim', ['2007220411'])
            // ->where('m2.tgl_keluar','>', '2020-02-07')
            ->whereBetween('m2.tgl_keluar', [$tgl_mulai, $tgl_akhir])
            ->select('m1.nm_mhs','m1.gelar_depan','m1.gelar_belakang','m1.tgl_lahir','p.id_prodi','p.nm_prodi','p.singkatan_gelar',
                    'm2.nim','jk.ket_keluar','m2.seri_ijazah',
                    'm1.nm_ibu','m1.nm_ayah','m1.alamat','m1.hp',
                    'm1.tempat_lahir','m2.judul_skripsi',
                    'm2.ipk','kon.nm_konsentrasi')
            ->orderBy('m2.seri_ijazah', 'asc');

        // Filter
        Sia::lulusKeluarFilter($query);

        $data['mahasiswa'] = $query->get();

        return view('lulus-keluar.html', $data);
    }

    public function cetakSkLulus(Request $r, $id)
    {
        $data['mhs'] = Sia::mahasiswa()
            ->leftJoin('prodi as pr','pr.id_prodi','m2.id_prodi')
            ->leftJoin('konsentrasi as k', 'k.id_konsentrasi', 'm2.id_konsentrasi')
            ->where('m2.id', $id)
            ->select('m1.nm_mhs','m1.tempat_lahir','m1.tgl_lahir','m2.id_jenis_keluar','m2.nim','m2.id_prodi','m2.ipk','m2.tgl_keluar','m2.ipk','pr.nm_prodi', 'pr.jenjang','k.nm_konsentrasi')
            ->first();
        if ( $data['mhs']->id_jenis_keluar != 1 ) {
            dd('Mahasiswa ini tidak lulus');
        }

        // if ( $data['mhs']->id_prodi == 61101 ) {
        //     return view('lulus-keluar.cetak-sk-lulus-stie', $data);
        // } else {
            return view('lulus-keluar.cetak-sk-lulus', $data);
        // }
    }
}
