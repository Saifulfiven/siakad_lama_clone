<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, Carbon, Auth;

class NilaiController extends Controller
{
    public function index(Request $r)
    {

    	if ( !empty($r->smt) ) {
    		Session::set('nil_semester', $r->smt);
    	}

    	if ( !empty($r->prodi) ) {
    		Session::set('nil_prodi', $r->prodi);
    	}

        if ( !empty($r->jenis) ) {
            // KULIAH = 1, SP = 2
            Session::set('nil_jenis', $r->jenis);
        }

    	if ( !Session::get('nil_semester') ) {
    		$this->setSessionFilter();
    	}

    	$query = Sia::nilaiPerkuliahan(Session::get('nil_jenis'))
    				->where('jdk.id_prodi', Session::get('nil_prodi'))
    				->where('jdk.id_smt', Session::get('nil_semester'));

        $fakultas_user = Sia::getFakultasUser();

        // Jika 61201 atau 62201 (s1)
        if ( $fakultas_user == 1 && !Sia::ketua1() ) {
            // $query->where(function($q){
            //     $q->where('jdk.hari', 0)
            //         ->orWhereNull('jdk.hari');
            // });
            // $query->where('mk.ujian_akhir','');
        }

        if ( $fakultas_user == 1 && Sia::jurusan() ) {
            $query->where(function($q){
                $q->where('jdk.hari', 0)
                    ->orWhereNull('jdk.hari');
            });
            $query->whereIn('mk.ujian_akhir',['','1','2','3']);
        }

    	if ( !empty($r->cari) ) {
    		$query->where(function($q)use($r){
    			$q->where('mk.kode_mk', 'like', '%'.$r->cari.'%')
    				->orWhere('mk.nm_mk', 'like', '%'.$r->cari.'%');
    		});
    	}

    	$data['jadwal'] = $query->paginate(15);
        
    	return view('nilai.index', $data);
    }

    private function setSessionFilter()
    {
        Session::set('nil_semester', Sia::sessionPeriode());
    	Session::set('nil_jenis', 1);
    	$prodi_user = Sia::getProdiUser();
    	Session::set('nil_prodi', @$prodi_user[0]);
    }

    public function cetak($id)
    {
        $jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : '';
        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id)->first();

        $data['peserta'] = Sia::pesertaKelas($data['r']->id);

        return view('nilai.cetak', $data);
    }

    public function edit($id)
    {
    	$query = Sia::jadwalKuliah('x', Session::get('nil_jenis'));

	    $data['r'] = $query->where('jdk.id',$id)->first();

    	$data['peserta'] = Sia::pesertaKelas($data['r']->id);
        $prodi_users = Sia::getProdiUser();

        if ( Sia::akademik() ) {

            if ( in_array('61101', $prodi_users) && $data['r']->hari != 0 ) {
                echo "<center><h3>Akses ditolak</h3></center>";
                exit;
            }

        } elseif ( Sia::jurusan() ) {
            
            if ( !empty($data['r']->hari) ) {
                echo "<center><h3>Akses ditolak</h3></center>";
                exit;
            }
        }

        else {

            if ( $data['r']->hari != 0 && !Sia::ketua1() && !Sia::admin() ) {
                echo "<center><h3>Akses ditolak</h3></center>";
                exit;
            }
        }

	    return view('nilai.edit', $data);
    }

    public function detail($id)
    {
        $query = Sia::jadwalKuliah('x', Session::get('nil_jenis'));

        $data['r'] = $query->where('jdk.id',$id)->first();

        $data['peserta'] = Sia::pesertaKelas($data['r']->id);
        $prodi_users = Sia::getProdiUser();

        return view('nilai.detail', $data);
    }

    public function hitungNilaiS2(Request $r)
    {

        if ( $r->jml_dosen == 1 ) {
            $total = $r->uas;
        } else {
            $total = ( $r->uts + $r->uas ) / 2;
        }

        if ( !empty($r->nilai) ) {
        	$total = $r->nilai;
        }

        $total = round($total,2);

        $grade = Sia::grade($r->prodi, $total);

        $html = $total.' = '.$grade;
        return Response::json(['nilai' => $total, 'grade' => $grade, 'html' => $html]);
    }

    public function update(Request $r)
    {

    	try {

            DB::transaction(function()use($r){
                foreach( $r->nilai as $key => $val )
                {
                    if ( $val != '-' && $r->nil_huruf_priority[$key] == 2 ) {
                        $nilai = explode('-', $val);
                        $huruf = $nilai[1];
                        $lastChar = substr($val, -1);
                        if($lastChar === '-'){
                            $huruf .= $lastChar;
                        }
                        $indeks = $nilai[0];
                    } else {
                        if ( !empty($r->huruf_baru[$key]) ) {
                            $huruf = $r->huruf_baru[$key];
                            $q_indeks = DB::table('skala_nilai')
                                        ->select('nilai_indeks')
                                        ->where('id_prodi', $r->id_prodi)
                                        ->where('nilai_huruf', $huruf)
                                        ->first();
                            if ( !empty($q_indeks) ) {
                                $indeks = $q_indeks->nilai_indeks;
                            } else {
                                $indeks = 0.00;
                            }
                        } else {
                            $huruf = '';
                            $indeks = 0.00;
                        }
                    }
                    $nil = \App\Nilai::where('id', $r->id_nilai[$key])->first();
                    $nil->nil_kehadiran = $r->kehadiran[$key];
                    $nil->nil_tugas = $r->tugas[$key];
                    $nil->nil_mid = $r->uts[$key];
                    $nil->nil_final = $r->uas[$key];
                    $nil->nilai_angka = $r->nil_angka[$key];
                    $nil->nilai_huruf = $huruf;
                    $nil->nilai_indeks = $indeks;

                    $nil->save();

                    if ( $r->nil_lama[$key] != $huruf ) {

                        $log = new \App\LogNilai;
                        $log->matakuliah = $r->matakuliah;
                        $log->dosen = $r->dosen;
                        $log->id_smt = $r->id_smt;
                        $log->id_prodi = $r->id_prodi;
                        $log->id_user = Auth::user()->id;
                        $log->nm_pengubah = Auth::user()->nama;
                        $log->level = Auth::user()->level;
                        $log->nil_awal = $r->nil_lama[$key];
                        $log->nil_akhir = $huruf;
                        $log->ip = $r->ip();
                        $log->komputer = php_uname();
                        $log->save();
                    }
                }
            });
    		

    	} catch(\Exception $e) {
    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
    	}

    	Rmt::success('Berhasil menyimpan data');

    	return Response::json(['error' => 0,'msg' => ''], 200);
    }

    public function updateS2(Request $r)
    {

        try {

            DB::transaction(function()use($r){
                foreach( $r->id_nilai as $key => $val )
                {
                    $uts = $r->uts[$key];
                    $uas = $r->uas[$key];

                    $tot_nilai = '';

                    if ( $r->jml_dosen == 1 ) {
                        if ( $uas != '' ) {
                            $tot_nilai = $uas;
                        }
                    } else {
                        if ( $uas != '' || $uts != '' ) {
                            $tot_nilai = ($uas + $uts)/2;
                        }
                    }

                    if ( $tot_nilai !== '' ) {
                        $tot_nilai = round($tot_nilai, 2);
                        $huruf = Sia::grade($r->id_prodi, $tot_nilai);
                    }

                    if ( $tot_nilai === '' ) {
                        continue;
                    }
                    
                    $q_indeks = DB::table('skala_nilai')
                                        ->select('nilai_indeks')
                                        ->where('id_prodi', $r->id_prodi)
                                        ->where('nilai_huruf', $huruf)
                                        ->first();
                    if ( !empty($q_indeks) ) {
                        $indeks = $q_indeks->nilai_indeks;
                    } else {
                        $indeks = 0.00;
                    }

                    DB::table('nilai')->where('id', $val)
                        ->update([
                            'nil_mid' => $r->uts[$key],
                            'nil_final' => $r->uas[$key],
                            'nilai_angka' => $tot_nilai,
                            'nilai_huruf' => $huruf,
                            'nilai_indeks' => $indeks
                        ]);

                    if ( $r->nil_lama[$key] != $huruf ) {
                        $log = new \App\LogNilai;
                        $log->matakuliah = $r->matakuliah;
                        $log->dosen = $r->dosen;
                        $log->id_smt = $r->id_smt;
                        $log->id_prodi = $r->id_prodi;
                        $log->id_user = Auth::user()->id;
                        $log->nm_pengubah = Auth::user()->nama;
                        $log->level = Auth::user()->level;
                        $log->nil_awal = $r->nil_lama[$key];
                        $log->nil_akhir = $huruf;
                        $log->ip = $r->ip();
                        $log->komputer = php_uname();
                        $log->save();
                    }

                }
            });
            

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        Rmt::success('Berhasil menyimpan data');

        return Response::json(['error' => 0,'msg' => ''], 200);
    }

}
