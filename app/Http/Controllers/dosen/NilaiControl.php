<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;

use DB, Sia, Rmt, Response, Session, Auth, Excel;

trait NilaiControl
{

    public function nilai(Request $r, $id)
    {
    	$jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : $r->jenis;
    	$query = Sia::jadwalKuliah('x', $jenis);

	    $data['r'] = $query->where('jdk.id',$id)->first();
        $data['jml_pertemuan'] = $r->jenis == 1 ? 14 : 10;
    	$data['peserta'] = Sia::pesertaKelas($data['r']->id);
        $data['min_kehadiran'] = round( ($data['jml_pertemuan'] * 0.50 ) + 1);
        $data['jenis_jadwal'] = $jenis;

	    return view('dsn.nilai.index', $data);
    }

    public function cekNilaiAkhir(Request $r)
    {
        if ( !$r->ajax() ) {
            return Response::json(['error' => 1, 'msg' => 'Access Denied']);
        }

        $hadir = Sia::persenNilai('hadir');
        $tugas = Sia::persenNilai('tugas');
        $uts = Sia::persenNilai('uts');
        $uas = Sia::persenNilai('uas');

        $n_hadir = $r->hadir * $hadir;
        $n_tugas = $r->tugas * $tugas;
        $n_uts = $r->uts * $uts;
        $n_uas = $r->uas * $uas;

        $total = $n_hadir + $n_tugas + $n_uts + $n_uas;
        $total = round($total,2);

        $grade = Sia::grade($r->prodi, $total, 1, 'T');

        $html = $total.' = '.$grade;
        return Response::json(['nilai' => $total, 'grade' => $grade, 'html' => $html]);
    }

    public function nilaiUpdate(Request $r)
    {


        if ( !Sia::canAction(Session::get('jdm.ta')) && $r->jenis_jadwal == 1 ) {
            return Response::json(['error' => 1, 'msg' => 'Masa penginputan nilai telah berakhir']);
        }

        if ( count($r->nilai) == 0 ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa disimpan']);
        }

    	try {
            // return Response::json(['error' => 1, 'msg' => 'Maintenance'.$r->id_nilai[2]]);

    		DB::transaction(function()use($r){
    			foreach( $r->nilai as $key => $val )
    			{

                    // Cek apakah kehadiran 75%
                    // $kehadiran = Session::get('kehadiran');
                    // if ( $kehadiran[$r->id_nilai[$key]] <= 10 ) {
                    //     continue;
                    // }

                    // Kehadiran tidak cukup
                    if ( $val == 'non' ) {
                        continue;
                    }

    				if ( $val != '-' && $r->nil_huruf_priority[$key] == 2 ) {
    					$nilai = explode('-', $val);
    					$huruf = $nilai[1];
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
    				// $nil->nil_kehadiran = $r->kehadiran[$key];
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
    					$log->dosen = Sia::sessionDsn('nama');
                        $log->id_smt = $r->id_smt;
    					$log->id_prodi = $r->id_prodi;
    					$log->id_user = Auth::user()->id;
    					$log->nm_pengubah = Sia::sessionDsn('nama');
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

    public function nilaiUpdateS2(Request $r)
    {

        if ( !Sia::canAction(Session::get('jdm.ta'), false, 2) && $r->jenis_jadwal == 1 ) {
            return Response::json(['error' => 1, 'msg' => 'Masa penginputan nilai telah berakhir']);
        }

        if ( is_array($r->nilai) && count($r->nilai) == 0 ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa disimpan']);
        }

        try {

            DB::transaction(function()use($r){

                foreach( $r->id_nilai as $key => $val )
                {

                    $nil = \App\Nilai::where('id', $r->id_nilai[$key])->first();

                    $grade = '';

                    if ( $r->dosen_ke == 1 ) {

                        if ( !empty($r->nilai_final_hide[$key]) ) {
                            
                            $tot_nilai = floatval($r->nilai_final_hide[$key]) + floatval($r->uts[$key]);
                            
                            $rata2 = 0;

                            if ( !empty($tot_nilai) ) {
                                $rata2 = $tot_nilai / 2;
                            }

                            $grade = Sia::grade($r->id_prodi, $rata2, 1, 'T');

                            $q_indeks = DB::table('skala_nilai')
                                        ->select('nilai_indeks')
                                        ->where('id_prodi', $r->id_prodi)
                                        ->where('nilai_huruf', $grade)
                                        ->first();
                            if ( !empty($q_indeks) ) {
                                $indeks = $q_indeks->nilai_indeks;
                            } else {
                                $indeks = 0.00;
                            }

                            $nil->nil_mid = $r->uts[$key];
                            $nil->nilai_angka = $rata2;
                            $nil->nilai_huruf = $grade;
                            $nil->nilai_indeks = $indeks;

                        } else {
                            $nil->nil_mid = $r->uts[$key];
                        }

                    } else {

                        $tot_nilai = floatval($r->nilai_mid_hide[$key]) + floatval($r->uas[$key]);
                        
                        $rata2 = 0;

                        if ( !empty($tot_nilai) ) {
                            $rata2 = $tot_nilai / 2;
                        }

                        $grade = Sia::grade($r->id_prodi, $rata2, 1, 'T');

                        $q_indeks = DB::table('skala_nilai')
                                        ->select('nilai_indeks')
                                        ->where('id_prodi', $r->id_prodi)
                                        ->where('nilai_huruf', $grade)
                                        ->first();
                        if ( !empty($q_indeks) ) {
                            $indeks = $q_indeks->nilai_indeks;
                        } else {
                            $indeks = 0.00;
                        }

                        $nil->nil_final = $r->uas[$key];
                        $nil->nilai_angka = $rata2;
                        $nil->nilai_huruf = $grade;
                        $nil->nilai_indeks = $indeks;

                    }
                    
                    $save = $nil->save();

                }
            });
            

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        Rmt::success('Berhasil menyimpan data');

        return Response::json(['error' => 0,'msg' => ''], 200);
    }

    public function nilaiUpdateS2Single(Request $r)
    {

        if ( !Sia::canAction(Session::get('jdm.ta'), false, 2) && $r->jenis_jadwal == 1 ) {
            return Response::json(['error' => 1, 'msg' => 'Masa penginputan nilai telah berakhir']);
        }

        if ( is_array($r->nilai) && count($r->nilai) == 0 ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada data yang bisa disimpan']);
        }

        try {

            DB::transaction(function()use($r){

                foreach( $r->id_nilai as $key => $val )
                {

                    $nil = \App\Nilai::where('id', $r->id_nilai[$key])->first();

                    $grade = '';


                    $rata2 = $r->uas[$key];

                    $grade = Sia::grade($r->id_prodi, $rata2, 1, 'T');

                    $q_indeks = DB::table('skala_nilai')
                                    ->select('nilai_indeks')
                                    ->where('id_prodi', $r->id_prodi)
                                    ->where('nilai_huruf', $grade)
                                    ->first();
                    if ( !empty($q_indeks) ) {
                        $indeks = $q_indeks->nilai_indeks;
                    } else {
                        $indeks = 0.00;
                    }

                    $nil->nil_mid = $r->uas[$key];
                    $nil->nil_final = $r->uas[$key];
                    $nil->nilai_angka = $rata2;
                    $nil->nilai_huruf = $grade;
                    $nil->nilai_indeks = $indeks;
                    
                    $nil->save();

                }
                

            });
            

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        Rmt::success('Berhasil menyimpan data');

        return Response::json(['error' => 0,'msg' => ''], 200);
    }

    public function nilaiCetak(Request $r, $id)
    {
    	$jenis = $r->jenis != 2 ? 1 : 2;
        
    	$query = Sia::jadwalKuliah('x', $jenis);

	    $data['r'] = $query->where('jdk.id',$id)->first();

    	$data['peserta'] = Sia::pesertaKelas($data['r']->id);

		$qr = 'NILAI KULIAH : '.Sia::sessionDsn('nama').','.$data['r']->nm_smt.' [STIE NOBEL INDONESIA]';
		\QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionDsn().'.svg');

	    return view('dsn.nilai.cetak', $data);
    }

    public function nilaiCetakS2(Request $r, $id)
    {
        $jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : '';
        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id)->first();

        $data['peserta'] = Sia::pesertaKelas($data['r']->id);

        $qr = 'NILAI KULIAH : '.Sia::sessionDsn('nama').','.$data['r']->nm_smt.' [STIE NOBEL INDONESIA]';
        \QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionDsn().'.svg');

        return view('dsn.nilai.cetak-s2', $data);
    }

    public function nilaiEkspor(Request $r, $id)
    {
        $jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : '';
        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id)->first();

        $data['peserta'] = Sia::pesertaKelas($data['r']->id);
        try {
            Excel::create('Nilai '.$data['r']->nm_mk, function($excel)use($data) {

                $excel->sheet('New sheet', function($sheet)use($data) {

                    $sheet->loadView('dsn.nilai.excel', $data);

                });

            })->download('xlsx');;
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }
}