<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Mahasiswareg, App\Tugas, App\JawabanTugas;
use Rmt, DB, Response, Sia, Carbon;

class LmsMhsController extends Controller
{
	use Library;

    public function __construct(Request $r)
    {
    	if ( $r->route()->getName() != 'view_materi' ) {
	        Rmt::auth(config('app.token'), $r->token);
	    }
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
        $ta_aktif = empty($r->ta) || $r->ta == 'null' ? $periode['id'] : $r->ta;

        $data_jdk = [];

        /* Jadwal kuliah */
            $jdk = $this->jadwalKuliahMahasiswa($user->id, $jenis_jdk, $ta_aktif)
                ->where('jdk.hari', '<>', 0)
                ->get();

            $jdk_sp = $this->jadwalKuliahMahasiswa($user->id, 2, $ta_aktif)
                ->where('jdk.hari', '<>', 0)
                ->get();

            foreach( $jdk as $j ) {
                $jam    = substr($j->jam_masuk,0,5);
                $jam_out = substr($j->jam_keluar,0,5);
                $mtk    = ucwords(strtolower(trim($j->nm_mk)));
                $data_jdk[] = [
                	'id' 		=> $j->id,
                    'hari'      => ucfirst(strtolower(Rmt::hari($j->hari))),
                    'matakuliah'=> $mtk,
                    'dosen'     => $j->dosen,
                    'jam'       => $jam.'-'.$jam_out,
                    'ruangan'   => $j->nm_ruangan
                ];
            }

            foreach( $jdk_sp as $j ) {
                $jam    = substr($j->jam_masuk,0,5);
                $jam_out = substr($j->jam_keluar,0,5);
                $mtk    = ucwords(strtolower(trim($j->nm_mk)));
                $data_jdk[] = [
                    'id'        => $j->id,
                    'hari'      => ucfirst(strtolower(Rmt::hari($j->hari))),
                    'matakuliah'=> $mtk,
                    'dosen'     => $j->dosen,
                    'jam'       => $jam.'-'.$jam_out,
                    'ruangan'   => $j->nm_ruangan
                ];
            }


        $result_jdk = ['count' => count($data_jdk), 'data' => $data_jdk];

        $result_ta = [
            'data' => $this->semester($user->semester_mulai, $periode['id'], $ta_aktif)
        ];

        $data = ['ta' => $result_ta, 'jadwal_kuliah' => $result_jdk];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function detail(Request $r)
    {

		$jadwal = DB::table('jadwal_kuliah as jdk')
            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
            ->select('jdk.id','mk.nm_mk', 'jdk.kode_kls','jdk.id_prodi')
            ->where('jdk.id', $r->id)
            ->first();

        $jml_pertemuan = DB::table('dosen_mengajar')
							->where('id_jdk', $r->id)
							->sum('jml_tm');

        // $jml_pertemuan = $jml_pertemuan == 14 ? $jml_pertemuan + 2 : $jml_pertemuan;
        $jml_dosen = DB::table('dosen_mengajar')->where('id_jdk', $r->id)->count();
        
        $jml_pertemuan = $jml_pertemuan + 2;
        if ( $jml_dosen == 1 && $jadwal->id_prodi == 61101 ) {
            $jml_pertemuan = $jml_pertemuan + 6;
        }

        $data_dosen = DB::table('dosen_mengajar')->where('id_jdk', $r->id)->get();

		$resources = [];

		for( $i = 0; $i <= $jml_pertemuan; $i++ ) {

            // Make intro
            if ( $i == 0 ) {
                        
                foreach( $data_dosen as $dsn ) {

                    $konten = $this->makeIntro($r->id, $dsn->id_dosen, $r->nm_mk);

                    $resources[0][] = [
                        'id' => '',
                        'id_resource' => '',
                        'judul' => '',
                        'deskripsi' => $konten,
                        'ekstensi' => 'catatan',
                        'jenis' => 'catatan',
                        'jenis2' => '-',
                        'link' => ''
                    ];

                }

            }

			$resource = $this->getResources($i, $r->id);

			if ( count($resource) > 0 ) {

				foreach( $resource as $res ) {

					if ( $res->jenis == 'materi' ) {
						$link = url("/api/lms/materi/view/$res->id_resource/$res->id_dosen/$res->file");
						$eks = $this->get_file_extension($res->file);

					} elseif ( $res->jenis == 'tugas' ) {
						$link = '';
						$eks = 'tugas';
					} elseif ( $res->jenis == 'catatan' ) {
						$eks = 'catatan';
						$link = '';
					} elseif ( $res->jenis == 'kuis' ) {
                        $eks = 'kuis';
                        $link = '';
                    } elseif ( $res->jenis == 'video' ) {
                        $eks = 'video';
                        $link = '';
                    } else {
						$link = '';
						$eks = '';
					}

					$resources[$i][] = [
						'id' => $res->id,
						'id_resource' => $res->id_resource,
                        'judul' => $res->judul,
						'deskripsi' => empty($res->deskripsi) ? '':$res->deskripsi,
						'ekstensi' => $eks,
                        'jenis' => $res->jenis,
						'jenis2' => $res->jenis2,
						'link' => $link
					];

				}


			} else {
                
                // Generate intro
                if ( $i == 0 && count($resources) == 0 ) {

                    foreach( $data_dosen as $dsn ) {

                        $konten = $this->makeIntro($r->id, $dsn->id_dosen, $r->nm_mk);

                        $resources[0][] = [
                            'id' => '',
                            'id_resource' => '',
                            'judul' => '',
                            'deskripsi' => $konten,
                            'ekstensi' => 'catatan',
                            'jenis' => 'catatan',
                            'jenis2' => '-',
                            'link' => ''
                        ];

                    }

                } elseif ( $i == 0 && count($resources) > 0 ) {

                } else {
				    $resources[$i] = [];
                }

			}

		} // end for

		$data = [
			'jml_pertemuan' => $jml_pertemuan,
			'jadwal' => $jadwal,
			'resources' => $resources
		];
		
		$result = [
			'error' => 0,
			'data' => $data
		];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
        
    }

    private function makeIntro($id_jadwal, $id_dosen, $mk, $jenis = 'catatan')
    {
        try {

            $dsn = DB::table('dosen as d')
                        ->leftJoin('users as u', 'd.id_user', 'u.id')
                        ->select('d.*', 'u.email')
                        ->where('d.id', $id_dosen)
                        ->first();

            if ( empty($dsn->foto) ) {
                $foto = url('resources/assets/img/avatar.png');
            } else {
                $foto = config('app.url-foto-dosen').'/'.$dsn->foto;
            }

            $konten = '
                <p style="text-align:center"><strong>'.$this->namaDosen($dsn->gelar_depan, $dsn->nm_dosen, $dsn->gelar_belakang).'</strong></p>
                <p style="text-align:center;margin-bottom: 20px"><strong>'.$mk.'</strong></p><br>
                <p style="text-align:center;padding-bottom: 20px">
                    <img style="max-width:150px" alt="" src="'.$foto.'" /></p>
                <p style="text-align:center">No HP : '.$dsn->hp.'</p>
                <p style="text-align:center">Email : '.$dsn->email.'</p>';

            return $konten;

        } catch(\Exception $e) { dd($e->getMessage()); }
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

    public function materiView(Request $r, $id_materi, $id_dosen, $file)
    {

        $materi = DB::table('lms_materi as m')
                ->leftJoin('lms_bank_materi as bm', 'm.id_bank_materi', 'bm.id')
                ->where('m.id', $id_materi)
                ->where('m.id_dosen', $id_dosen)
                ->select('m.*','bm.file')
                ->first();

        if ( !empty($materi) ) {

            $file = config('app.lms-materi').'/'.$id_dosen.'/'.$materi->file;

            return Response::file($file);

        } else {
            echo 'Tidak ada data';
        }
    }

    public function tugasDetail(Request $r)
    {
		$mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $data['jadwal'] = DB::table('jadwal_kuliah as jdk')
            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
            ->select('jdk.id','mk.nm_mk', 'jdk.kode_kls')
            ->where('jdk.id', $r->id_jdk)
            ->first();

        $tugas = Tugas::findOrFail($r->id);
		
		$jwb = JawabanTugas::where('id_tugas', $r->id)
                ->where('id_peserta', $mhs->id)
                ->first();

        $status 	= !empty($jwb) ? true : false;
        $nilai 		= !empty($jwb) && $jwb->nilai != null ? $jwb->nilai : '';
        $komentar	= !empty($jwb) && !empty($jwb->comment) ? $jwb->comment : '';
        $jawaban 	= !empty($jwb) ? $jwb->jawaban : '';
        $file 		= !empty($jwb) ? $jwb->file : '';
        $remaining_attempt = !empty($jwb) && !empty($tugas->max_attempt) ? $tugas->max_attempt - $jwb->attempt : $tugas->max_attempt;
        $attempt 	= empty($tugas->max_attempt) ? 'Tidak Dibatasi' : 'Hanya '.$tugas->max_attempt.' kali percobaan';
        $updated_at = !empty($jwb) ? Rmt::tgl_indo($jwb->updated_at) : '';
        $mulai_berlaku	= !empty($tugas->mulai_berlaku) ? Rmt::tgl_indo($tugas->mulai_berlaku).' '.substr($tugas->mulai_berlaku, 11, 5) : '';
        $tgl_berakhir	= !empty($tugas->tgl_berakhir) ? Rmt::tgl_indo($tugas->tgl_berakhir).' '.substr($tugas->tgl_berakhir, 11, 5) : '';
        $tgl_tutup	= !empty($tugas->tgl_tutup) ? Rmt::tgl_indo($tugas->tgl_tutup).' '.substr($tugas->tgl_tutup, 11, 5) : '';

        $file = false;
        $text = false;
        if ( $tugas->jenis_pengiriman == 'all' ) {
        	$file = true;
        	$text = false;
        } elseif ( $tugas->jenis_pengiriman == 'text' ) {
        	$text = true;
        } elseif ( $tugas->jenis_pengiriman == 'file' ) {
        	$file = true;
        }

        $tertutup = false;

        if ( !empty($tugas->tgl_tutup) && Carbon::now() >= $tugas->tgl_tutup ) {
        	$tertutup = true;
        }

        $data['jawab'] = [
        	'status' => $status,
			'nilai' => $nilai,
			'komentar' => $komentar,
			'jawaban' => $jawaban,
			'file' => $file,
			'remaining_attempt' => $remaining_attempt,
			'attempt' => $attempt,
			'updated_at' => $updated_at,
			'mulai_berlaku' => $mulai_berlaku,
			'tgl_berakhir' => $tgl_berakhir,
			'tgl_tutup' => $tgl_tutup,
			'haveLampiran' => !empty($tugas->file) ? true : false,
			'tertutup' => $tertutup,
			'text' => $text,
			'file' => $file
        ];

        $data['tugas'] = $tugas;

		$result = [
			'error' => 0,
			'data' => $data
		];
		// dd($result);
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function kuisDetail(Request $r)
    {
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $data['jadwal'] = DB::table('jadwal_kuliah as jdk')
            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
            ->select('jdk.id','mk.nm_mk', 'jdk.kode_kls')
            ->where('jdk.id', $r->id_jdk)
            ->first();

        $tugas = Tugas::findOrFail($r->id);
        
        $jwb = JawabanTugas::where('id_tugas', $r->id)
                ->where('id_peserta', $mhs->id)
                ->first();

        $status     = !empty($jwb) ? true : false;
        $nilai      = !empty($jwb) && $jwb->nilai != null ? $jwb->nilai : '';
        $komentar   = !empty($jwb) && !empty($jwb->comment) ? $jwb->comment : '';
        $jawaban    = !empty($jwb) ? $jwb->jawaban : '';
        $file       = !empty($jwb) ? $jwb->file : '';
        $remaining_attempt = !empty($jwb) && !empty($tugas->max_attempt) ? $tugas->max_attempt - $jwb->attempt : $tugas->max_attempt;
        $attempt    = empty($tugas->max_attempt) ? 'Tidak Dibatasi' : 'Hanya '.$tugas->max_attempt.' kali percobaan';
        $updated_at = !empty($jwb) ? Rmt::tgl_indo($jwb->updated_at) : '';
        $mulai_berlaku  = !empty($tugas->mulai_berlaku) ? Rmt::tgl_indo($tugas->mulai_berlaku).' '.substr($tugas->mulai_berlaku, 11, 5) : '';
        $tgl_berakhir   = !empty($tugas->tgl_berakhir) ? Rmt::tgl_indo($tugas->tgl_berakhir).' '.substr($tugas->tgl_berakhir, 11, 5) : '';
        $tgl_tutup  = !empty($tugas->tgl_tutup) ? Rmt::tgl_indo($tugas->tgl_tutup).' '.substr($tugas->tgl_tutup, 11, 5) : '';

        $file = false;
        $text = false;
        if ( $tugas->jenis_pengiriman == 'all' ) {
            $file = true;
            $text = false;
        } elseif ( $tugas->jenis_pengiriman == 'text' ) {
            $text = true;
        } elseif ( $tugas->jenis_pengiriman == 'file' ) {
            $file = true;
        }

        $tertutup = false;

        if ( !empty($tugas->tgl_tutup) && Carbon::now() >= $tugas->tgl_tutup ) {
            $tertutup = true;
        }

        $data['jawab'] = [
            'status' => $status,
            'nilai' => $nilai,
            'komentar' => $komentar,
            'jawaban' => $jawaban,
            'file' => $file,
            'remaining_attempt' => $remaining_attempt,
            'attempt' => $attempt,
            'updated_at' => $updated_at,
            'mulai_berlaku' => $mulai_berlaku,
            'tgl_berakhir' => $tgl_berakhir,
            'tgl_tutup' => $tgl_tutup,
            'haveLampiran' => !empty($tugas->file) ? true : false,
            'tertutup' => $tertutup,
            'text' => $text,
            'file' => $file
        ];

        $data['tugas'] = $tugas;

        $result = [
            'error' => 0,
            'data' => $data
        ];
        // dd($result);
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function getResources($indeks, $id_jadwal)
    {

        $materi = DB::table('lms_resources as rs')
            ->leftJoin('lms_materi as m', 'rs.id_resource', 'm.id')
            ->leftJoin('lms_bank_materi as bm', 'bm.id', 'm.id_bank_materi')
            ->where('rs.jenis', 'materi')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->select('rs.*', 'm.judul', 'm.deskripsi', 'bm.file', 'm.id_dosen', DB::raw('\'- \' as jenis2'));

        $catatan = DB::table('lms_resources as rs')
            ->leftJoin('lms_catatan as ct', 'rs.id_resource', 'ct.id')
            ->where('rs.jenis', 'catatan')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->select('rs.*', DB::raw('\'catatan\' as judul'), 'ct.konten as deskripsi', DB::raw('\'catatan.png\' as file'), 'ct.id_dosen', DB::raw('\'- \' as jenis2'));

        $kuis = DB::table('lms_resources as rs')
            ->leftJoin('lmsk_kuis as k', 'rs.id_resource', 'k.id')
            ->where('rs.jenis', 'kuis')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->select('rs.*', 'k.judul', 'k.ket as deskripsi', DB::raw('\'kuis.png\' as file'), 'k.id_dosen', 'k.jenis as jenis2');

        $video = DB::table('lms_resources as rs')
            ->leftJoin('lms_video as v', 'rs.id_resource', 'v.id')
            ->where('rs.jenis', 'video')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->select('rs.*', 'v.judul', 'v.ket as deskripsi', DB::raw('\'video.png\' as file'), 'v.id_dosen', DB::raw('\'- \' as jenis2'));

        $tugas = DB::table('lms_resources as rs')
            ->leftJoin('lms_tugas as t', 'rs.id_resource', 't.id')
            ->where('rs.jenis', 'tugas')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->select('rs.*', 't.judul', 't.deskripsi', DB::raw('\'tugas.png\' as file'), 't.id_dosen', 't.jenis as jenis2')
            ->union($materi)
            ->union($kuis)
            ->union($video)
            ->union($catatan)
            ->orderBy('urutan')
            ->get();

        return $tugas;
    }

}
