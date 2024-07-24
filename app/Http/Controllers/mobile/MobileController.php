<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tugas, App\Mahasiswareg, App\JawabanTugas, App\Topik, App\Materi, App\Resources;
use App\BankMateri, App\Catatan, App\TopikJawaban, App\Kuis, App\TelahKuis, App\KuisHasil, App\Video, App\Mahasiswa;
use DB, Rmt, Response, Session, Carbon, Zipper, File;

class MobileController extends Controller
{
	use MobileKuisController;
	use MobileNilaiController;

	/* MAhasiswa */
	    public function tugas(Request $r)
	    {
	    	$data['mhs'] = Mahasiswareg::where('nim', $r->nim)->first();

			\LogAktivitas::addManual($r->nim, $r->nim, 'Tugas');

	        $data['r'] = DB::table('jadwal_kuliah as jdk')
	            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
	            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
	            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
	            ->select('jdk.id','mk.kode_mk', 'mk.nm_mk', 'jdk.kode_kls','jdk.id_smt')
	            ->where('jdk.id', $r->id_jdk)
	            ->first();

	        $data['tugas'] = Tugas::findOrFail($r->id);

	        if ( $data['mhs']->id_prodi == 61101 ) {
	        	$data['jenis_ujian'] = $this->jenisUjianPasca($data['r']->id_smt);
	        } else {
	        	$data['jenis_ujian'] = $this->jenisUjian($data['r']->id_smt);
	        }



	        return view('mobile.tugas.detail', $data);
	    }

	    public function tugasViewAttach(Request $r, $id_tugas, $id_dosen, $file )
	    {

	        $tugas = DB::table('lms_tugas as t')
	                ->where('t.id', $id_tugas)
	                ->where('t.id_dosen', $id_dosen)
	                ->select('t.*')
	                ->first();

	        if ( !empty($tugas) ) {

	            $file = config('app.lms-materi').'/'.$id_dosen.'/'.$file;

	            return Response::file($file);

	        } else {
	            echo 'Tidak ada data';
	        }
	    }

	    public function tugasStore(Request $r)
	    {
	    	\LogAktivitas::addManual($r->nim, $r->nim, 'Kumpul Tugas');

	        try {

	        	$mhs = DB::table('mahasiswa_reg as m1')
	        			->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
	        			->where('m1.nim', $r->nim)
	        			->select('m1.id', 'm1.nim', 'm2.nm_mhs')
	        			->first();

	            $tgs = Tugas::findOrFail($r->id_tugas);
	            $nim = $mhs->nim;
	            $nama = trim($mhs->nm_mhs);

	            $errors = [];

	            if ( !empty($tgs->tgl_tutup) && Carbon::now() >= $tgs->tgl_tutup ) {
	                $errors[] = 'Tugas ini telah tertutup.';
	            }

	            if ( empty($r->jawaban) && !$r->hasFile('file') ) {
	                $errors[] = 'Anda belum memberikan jawaban';
	            }

	            /* Validasi file */
	                if ( $r->hasFile('file') ) {

	                    $size = round($r->file->getSize()/1024);

	                    if ( $size > $tgs->max_file_upload ) {
	                        $errors[] = 'Maksimal ukuran file yang diperbolehkan adalah: '.$tgs->max_file_upload / 1024 .' MB';
	                    }

	                    $accept = ['pdf','doc','docx','xlsx','xls','pptx','zip','rar','jpg','png','gif','jpeg','txt'];
	                    $ekstensi = $r->file->getClientOriginalExtension();
	                    if ( !in_array(strtolower($ekstensi), $accept) ) {
	                        $errors[] = 'Tipe file yang diupload tidak dibolehkan. Hanya '.implode(',', $accept);
	                    }
	                }
	            /* End validasi */

	            /* Validasi jawaban */
	                if ( !empty($r->jawaban) ) {
	                    $jawaban = strip_tags($r->jawaban);
	                    $jml_karakter = strlen(strip_tags($jawaban));

	                    if ( !empty($tgs->min_teks) && $jml_karakter < $tgs->min_teks ) {
	                        $errors[] = 'Jumlah karakter minimum pada jawaban harus '.$tgs->min_teks.' karakter';
	                    }

	                    if ( !empty($tgs->max_teks) && $jml_karakter > $tgs->max_teks ) {
	                        $errors[] = 'Jumlah karakter maksimal pada jawaban adalah '.$tgs->min_teks.' karakter';
	                    }
	                }
	            /* End validasi */

	            if ( count($errors) > 0 ) {
	                return Response::json($errors, 422);
	            }


	            $data = new JawabanTugas;
	            $data->id_peserta = $mhs->id;
	            $data->id_tugas = $r->id_tugas;
	            $data->attempt = 1;

	            if ( !empty($r->jawaban) ) {
	                $data->jawaban = $r->jawaban;
	            }

	            if ( $r->hasFile('file') ) {

	                $name = $r->file->getClientOriginalName();
	                $path = config('app.lms-tugas').'/'.$r->id_tugas.'-'.$r->id_jadwal.'/'.$nim.'-'.$nama;
	                $r->file->move($path, $name);

	                $data->file = $name;
	            }

	            $data->save();
	            Rmt::success('Berhasil menyimpan jawaban');

	        } catch( \Exception $e ) {
	            return Response::json([$e->getMessage()], 422);
	        }
	    }

	    public function tugasUpdate(Request $r)
	    {
	        $tgs = Tugas::findOrFail($r->id_tugas);

	        $this->validate($r, [
	            'file' => 'max:'.$tgs->max_file_upload
	        ]);

	        try {

				$mhs = DB::table('mahasiswa_reg as m1')
	        			->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
	        			->where('m1.nim', $r->nim)
	        			->select('m1.id', 'm1.nim', 'm2.nm_mhs')
	        			->first();

	            $nim = $mhs->nim;
	            $nama = trim($mhs->nm_mhs);

	            $errors = [];

	            if ( !empty($tgs->tgl_tutup) && Carbon::now() >= $tgs->tgl_tutup ) {
	                $errors[] = 'Tugas ini telah tertutup.';
	            }

	            if ( empty($r->jawaban) && !$r->hasFile('file') ) {
	                $errors[] = 'Anda belum memberikan jawaban';
	            }

	            /* Validasi file */
	                if ( $r->hasFile('file') ) {

	                    $size = round($r->file->getSize()/1024);

	                    if ( $size > $tgs->max_file_upload ) {
	                        $errors[] = 'Maksimal ukuran file yang diperbolehkan adalah: '.$tgs->max_file_upload / 1024 .' MB';
	                    }

	                    $accept = ['pdf','doc','docx','xlsx','xls','pptx','zip','rar','jpg','png','gif','jpeg'];
	                    $ekstensi = $r->file->getClientOriginalExtension();
	                    if ( !in_array(strtolower($ekstensi), $accept) ) {
	                        $errors[] = 'Tipe file yang diupload tidak dibolehkan. Hanya '.implode(',', $accept);
	                    }
	                }
	            /* End validasi */

	            /* Validasi jawaban */
	                if ( !empty($r->jawaban) ) {
	                    $jawaban = strip_tags($r->jawaban);
	                    $jml_karakter = strlen(strip_tags($jawaban));

	                    if ( !empty($tgs->min_teks) && $jml_karakter < $tgs->min_teks ) {
	                        $errors[] = 'Jumlah karakter minimum pada jawaban harus '.$tgs->min_teks.' karakter';
	                    }

	                    if ( !empty($tgs->max_teks) && $jml_karakter > $tgs->max_teks ) {
	                        $errors[] = 'Jumlah karakter maksimal pada jawaban adalah '.$tgs->min_teks.' karakter';
	                    }
	                }
	            /* End validasi */

	            if ( count($errors) > 0 ) {
	                return Response::json($errors, 422);
	            }


	            $data = JawabanTugas::findOrFail($r->id);

	            $old_file = $data->file;

	            $data->id_peserta = $mhs->id;
	            $data->id_tugas = $r->id_tugas;

	            if ( !empty($r->remaining_attempt) ) {
	                $data->attempt = $data->attempt + 1;
	            }

	            if ( !empty($r->jawaban) ) {
	                $data->jawaban = $r->jawaban;
	            }

	            if ( $r->hasFile('file') ) {

	                $path = config('app.lms-tugas').'/'.$r->id_tugas.'-'.$r->id_jadwal.'/'.$nim.'-'.$nama;

	                $path_old_file = $path.'/'.$old_file;

	                if ( file_exists($path_old_file) ) {
	                    unlink($path_old_file);
	                }

	                $name = $r->file->getClientOriginalName();

	                $r->file->move($path, $name);

	                $data->file = $name;

	            }

	            $data->save();
	            Rmt::success('Berhasil menyimpan jawaban');

	        } catch( \Exception $e ) {
	            return Response::json([$e->getMessage()], 422);
	        }
	    }

	    public function tugasDownload(Request $r, $id_tugas, $file)
	    {
	    	$mhs = DB::table('mahasiswa_reg as m1')
	        			->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
	        			->where('m1.nim', $r->nim)
	        			->select('m1.id', 'm1.nim', 'm2.nm_mhs')
	        			->first();

	        $nim = $mhs->nim;
	        $nama = trim($mhs->nm_mhs);
	        $pathToFile = config('app.lms-tugas').'/'.$id_tugas.'-'.$r->jdk.'/'.$nim.'-'.$nama.'/'.$file;

	        return Response::file($pathToFile);
	    }


	    public function kuis(Request $r)
	    {
	    	$data['mhs'] = Mahasiswareg::where('nim', $r->nim)->first();

	        $data['r'] = DB::table('jadwal_kuliah as jdk')
	            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
	            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
	            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
	            ->select('jdk.id','mk.kode_mk', 'mk.nm_mk', 'jdk.kode_kls','jdk.id_smt')
	            ->where('jdk.id', $r->id_jdk)
	            ->first();

	        if ( $data['mhs']->id_prodi == 61101 ) {
	        	$data['jenis_ujian'] = $this->jenisUjianPasca($data['r']->id_smt);
	        } else {
	        	$data['jenis_ujian'] = $this->jenisUjian($data['r']->id_smt);
	        }

	        $data['kuis'] = Kuis::findOrFail($r->id);

	        // Jika kuis tertutup periksa apakah telah mengerjakan kuis
	        // Periksa di table lmsk_telah_kuis
	        if ( !empty($data['kuis']->tgl_tutup) && Carbon::now() >= $data['kuis']->tgl_tutup ) {
	            $cek_telah_kuis = DB::table('lmsk_telah_kuis')
	                    ->where('id_peserta', $data['mhs']->id)
	                    ->where('id_kuis', $r->id)
	                    ->where('sisa_waktu','<>',0)
	                    ->count();

	            // Jika dapat
	            if ( $cek_telah_kuis > 0 ) {
	                // Update tabel lmsk_telah_selesai
	                DB::table('lmsk_telah_kuis')
	                    ->where('id_peserta', $data['mhs']->id)
	                    ->where('id_kuis', $r->id)
	                    ->update(['sisa_waktu' => 0]);
	            }
	        }

	        return view('mobile.kuis.detail', $data);
	    }

	    public function kerjaKuis(Request $r)
	    {
	    	\LogAktivitas::addManual($r->nim, $r->nim, 'Kerja Tugas');

	    	$data['mhs'] = Mahasiswareg::where('nim', $r->nim)->first();

	        $data['r'] = DB::table('jadwal_kuliah as jdk')
	            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
	            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
	            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
	            ->select('jdk.id','mk.kode_mk', 'mk.nm_mk', 'jdk.kode_kls','jdk.id_smt')
	            ->where('jdk.id', $r->id_jdk)
	            ->first();

	        $data['kuis'] = Kuis::findOrFail($r->id_kuis);

	        if ( Carbon::now() < $data['kuis']->tgl_mulai ) {
	            dd('Waktu kuis belum saatnya');
	        }

	        if ( !empty($data['kuis']->tgl_tutup) && Carbon::now() >= $data['kuis']->tgl_tutup ) {
	            dd('Waktu kuis telah berakhir');
	        }

	        // Simpan telah kuis untuk mengambil sisa waktu
	        $telah_kuis = TelahKuis::where('id_kuis', $r->id_kuis)
	                    ->where('id_peserta', $data['mhs']->id)
	                    ->first();

	        if ( empty($telah_kuis) ) {

	        	$now = Carbon::now();
	            $sisa_waktu = Rmt::sisaMenit($now, $data['kuis']->tgl_tutup);
	            $waktu_kerja = $data['kuis']->waktu_kerja;

	            if ( $sisa_waktu < $waktu_kerja ) {
	                $waktu_kerja = $sisa_waktu;
	            }

	            $telah = new TelahKuis;
	            $telah->id_peserta = $data['mhs']->id;
	            $telah->id_kuis = $r->id_kuis;
	            $telah->sisa_waktu = $waktu_kerja;
	            $telah->save();
	            $data['telah_kuis'] = TelahKuis::find($telah->id);

	        } else {

	        	$now = Carbon::now();
	            $sisa_waktu = Rmt::sisaMenit($now, $data['kuis']->tgl_tutup);
	            $waktu_kerja = $data['kuis']->waktu_kerja;

	            if ( $sisa_waktu < $waktu_kerja ) {
	                $telah_kuis->sisa_waktu = $sisa_waktu;
	                $telah_kuis->save();
	                $data['telah_kuis'] = $telah_kuis;
	            } else {
	            	$data['telah_kuis'] = $telah_kuis;
	            }

	        }

	        if ( $data['telah_kuis']->sisa_waktu == 0 ) {
	            return redirect(route('kuis', ['id' => $r->id_kuis, 'id_jdk' => $r->id_jdk, 'nim' => $data['mhs']->nim]));
	        }


	        // Get soal
	        $soal = DB::table('lmsk_kuis_soal as ks')
	                ->leftJoin('lmsk_bank_soal as bs', 'bs.id', 'ks.id_bank_soal')
	                ->where('ks.id_kuis', $r->id_kuis)
	                ->select('bs.*','ks.id as id_kuis_soal');

	        if ( $data['kuis']->acak == '1' ) {
	            $soal = $soal->inRandomOrder();
	        } else {
	            $soal = $soal->orderBy('ks.id');
	        }

	        $data['soal'] = $soal->get();

	        $data['jumlah_soal'] = $this->jumlahSoal($r->id_kuis);

	        if ( $data['kuis']->tampilan == 'all' ) {
	            return view('mobile.kuis.kerja', $data);
	        } else {
	            return view('mobile.kuis.kerja-persoal', $data);
	        }
	    }

	    public function kuisUpdateWaktu(Request $r)
	    {
	        $data = TelahKuis::find($r->id);
	        $data->sisa_waktu = $r->waktu < 0 ? 0 : $r->waktu;
	        $data->save();
	    }

	    public function kuisStore(Request $r)
	    {
	        if ( count($r->jawaban) == 0 ) {
	            return Response::json(['Anda belum mengerjakan satupun'], 422);
	        }

	        $nilai_per_soal = 100 / $r->jumlah_soal;

	        try {

	            DB::transaction(function()use($r, $nilai_per_soal){

	                $id_mhs_reg = $r->id_mhs_reg;

	                foreach( $r->jawaban as $key => $val ) {

	                    $soal = DB::table('lmsk_kuis_soal as ks')
	                            ->leftJoin('lmsk_bank_soal as bs', 'ks.id_bank_soal', 'bs.id')
	                            ->select('bs.*')
	                            ->where('ks.id', $key)
	                            ->first();

	                    $nilai = 0;

	                    if ( $r->jenis[$key] == 'pg' ) {

	                        $nilai = $this->periksaPg($soal->jawaban_benar, $nilai_per_soal, $val);

	                        $data = KuisHasil::updateOrCreate(
	                        [
	                            'id_kuis_soal' => $key,
	                            'id_peserta' => $id_mhs_reg
	                        ],
	                        [
	                            'jawaban' => $val,
	                            'penilaian' => number_format($nilai, 2)
	                        ]);


	                    } else {

	                        $nilai = $this->periksaEssay($soal->keyword, $nilai_per_soal, $val);

	                        $data = KuisHasil::updateOrCreate(
	                        [
	                            'id_kuis_soal' => $key,
	                            'id_peserta' => $id_mhs_reg
	                        ],
	                        [
	                            'jawaban' => $val,
	                            'penilaian' => number_format($nilai, 2)
	                        ]);

	                    }
	                }

	                $telah = TelahKuis::find($r->id_telah_kuis);
	                $telah->sisa_waktu = 0;
	                $telah->save();

	            });

	            Rmt::success('Berhasil menyimpan kuis');
	            return Response::json(['Sukses']);

	        } catch( \Exception $e ) {
	            return Response::json([$e->getMessage().'. COBA ULANGI LAGI.'], 422);
	        }

	    }

	    public function kuisStoreSingle(Request $r)
	    {

	        $nilai_per_soal = 100 / $r->jml_soal;

	        try {

	            $id_mhs_reg = $r->id_mhs_reg;

	            $soal = DB::table('lmsk_kuis_soal as ks')
	                    ->leftJoin('lmsk_bank_soal as bs', 'ks.id_bank_soal', 'bs.id')
	                    ->select('bs.*')
	                    ->where('ks.id', $r->id_kuis_soal)
	                    ->first();

	            $nilai = 0;

	            if ( $r->jenis == 'pg' ) {

	                $nilai = $this->periksaPg($soal->jawaban_benar, $nilai_per_soal, $r->jawaban);

	                $data = KuisHasil::updateOrCreate(
	                [
	                    'id_kuis_soal' => $r->id_kuis_soal,
	                    'id_peserta' => $id_mhs_reg
	                ],
	                [
	                    'jawaban' => $r->jawaban,
	                    'penilaian' => number_format($nilai, 2)
	                ]);


	            } else {

	                $nilai = $this->periksaEssay($soal->keyword, $nilai_per_soal, $r->jawaban);

	                $data = KuisHasil::updateOrCreate(
	                [
	                    'id_kuis_soal' => $r->id_kuis_soal,
	                    'id_peserta' => $id_mhs_reg
	                ],
	                [
	                    'jawaban' => $r->jawaban,
	                    'penilaian' => number_format($nilai, 2)
	                ]);

	            }

	            return Response::json(['Sukses']);

	        } catch( \Exception $e ) {
	            return Response::json([$e->getMessage().'. COBA ULANGI LAGI.'], 422);
	        }

	    }

	    private function periksaPg($jawaban_benar, $nilai_soal, $jawaban_peserta)
	    {

	        if ( $jawaban_benar == $jawaban_peserta ) {
	            $val = $nilai_soal;
	        } else {
	            $val = 0;
	        }

	        return $val;
	    }

	    private function periksaEssay($keyword, $nilai_soal, $jawaban_peserta)
	    {
	        $nilai = 0;

	        if ( !empty($keyword) ) {
	            $keywords = explode(',', $keyword);
	            $nilai_per_keyword = $nilai_soal / count($keywords);

	            foreach( $keywords as $val ) {
	                if ( strpos($jawaban_peserta, trim($val)) !== false ){
	                    $nilai += $nilai_per_keyword;
	                }
	            }
	        }

	        return $nilai;
	    }

	    public static function jumlahSoal($id_kuis)
	    {
	    	$data = DB::table('lmsk_kuis_soal')
	    			->where('id_kuis', $id_kuis)
	    			->count();
	    	return $data;
		}

	    public function jenisUjian($id_smt)
	    {
	        $data = DB::table('jadwal_ujian as jdu')
	            ->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
	            ->where('jdk.id_smt', $id_smt)
	            ->where('jdu.jenis_ujian', 'UAS')
	            ->count();

	        return $data > 0 ? 'UAS':'UTS';
	    }

	    public function jenisUjianPasca($id_smt)
	    {
	        $data = DB::table('jadwal_ujian as jdu')
	            ->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
	            ->where('jdk.id_smt', $id_smt)
	            ->where('jdk.id_prodi', '61101')
	            ->where('jdu.jenis_ujian', 'UAS')
	            ->count();

	        return $data > 0 ? 'UAS':'UTS';
	    }

	    public function video(Request $r)
	    {

	        $data['mhs'] = Mahasiswareg::where('nim', $r->nim)->first();

	        $data['r'] = DB::table('jadwal_kuliah as jdk')
	            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
	            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
	            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
	            ->select('jdk.id','mk.kode_mk', 'mk.nm_mk', 'jdk.kode_kls','jdk.id_smt')
	            ->where('jdk.id', $r->id_jdk)
	            ->first();

	        $data['video'] = Video::findOrFail($r->id);

	        return view('mobile.video.index', $data);

	    }

	    public function videoUpdateKetersediaan(Request $r)
	    {
	        $video = Video::find($r->id_video);
	        $video->siap = 'y';
	        $video->save();
	    }

	    public function profilMhs(Request $r, $nim)
	    {
	        $mhs = Mahasiswareg::where('nim', $nim)->first();

	        $data['agama'] = DB::table('agama')->get();
	        $data['jenisTinggal'] = DB::table('jenis_tinggal')->get();
	        $data['alatTranspor'] = DB::table('alat_transpor')->get();
	        $data['alatTranspor'] = DB::table('alat_transpor')->get();
	        $data['jnsPendaftaran'] = DB::table('jenis_pendaftaran')->get();
	        $data['jalurMasuk'] = DB::table('jalur_masuk')->get();
	        $data['prodi'] = DB::table('prodi')->orderBy('jenjang')->get();
	        $data['penghasilan'] = DB::table('penghasilan')->get();
	        $data['pekerjaan'] = DB::table('pekerjaan')->get();
	        $data['pdk'] = DB::table('pendidikan')->get();
	        $data['infoNobel'] = DB::table('info_nobel')->get();
	        $data['mhs'] = Mahasiswa::where('id',$mhs->id_mhs)->first();

	        if ( empty($mhs) ) {
	            dd('Data tidak ditemukan');
	        }

	    	return view('mobile.profil-mhs.index', $data);
	    }

	    public function updateProfilMhs(Request $r)
	    {

	        $this->validate($r, [
	            'nik'   => 'required|unique:mahasiswa,id,'.$r->id.'|max:16',
	            'kelurahan' => 'required',
	            'kecamatan' => 'required',
	            'hp' => 'required',
	        ]);

	        try {
	            DB::transaction(function ()use($r,&$response) {
	                $mhs = Mahasiswa::find($r->id);
	                $mhs->nik = $r->nik;
	                $mhs->nisn = $r->nisn;
	                $mhs->npwp = $r->npwp;
	                $mhs->alamat = $r->alamat;
	                $mhs->dusun = $r->dusun;
	                $mhs->des_kel = $r->kelurahan;
	                $mhs->rt = $r->rt;
	                $mhs->rw = $r->rw;
	                $mhs->id_wil = $r->kecamatan;
	                $mhs->pos = $r->pos;
	                $mhs->hp = $r->hp;
	                $mhs->email = $r->email;
	                $mhs->nm_sekolah = $r->nm_sekolah;
	                $mhs->tahun_lulus_sekolah = $r->thn_lulus_sekolah;
	                $mhs->nik_ibu = $r->nik_ibu;
	                $mhs->tgl_lahir_ibu = empty($r->tgl_lahir_ibu) ? NULL : Rmt::formatTgl($r->tgl_lahir_ibu,'Y-m-d');
	                $mhs->id_pdk_ibu = empty($r->pdk_ibu) ? NULL : $r->pdk_ibu;
	                $mhs->id_pekerjaan_ibu = empty($r->pekerjaan_ibu) ? NULL : $r->pekerjaan_ibu;
	                $mhs->id_penghasilan_ibu = empty($r->penghasilan_ibu) ? NULL : $r->penghasilan_ibu;
	                $mhs->hp_ibu = $r->hp_ibu;
	                $mhs->nik_ayah = $r->nik_ayah;
	                $mhs->nm_ayah = $r->nama_ayah;
	                $mhs->tgl_lahir_ayah = empty($r->tgl_lahir_ayah) ? NULL : Rmt::formatTgl($r->tgl_lahir_ayah,'Y-m-d');
	                $mhs->id_pdk_ayah = empty($r->pdk_ayah) ? NULL : $r->pdk_ayah;
	                $mhs->id_pekerjaan_ayah = empty($r->pekerjaan_ayah) ? NULL : $r->pekerjaan_ayah;
	                $mhs->hp_ayah = $r->hp_ayah;
	                $mhs->id_penghasilan_ayah = empty($r->penghasilan_ayah) ? NULL : $r->penghasilan_ayah;
	                $mhs->nm_wali = $r->nama_wali;
	                $mhs->tgl_lahir_wali = empty($r->tgl_lahir_wali) ? NULL : Rmt::formatTgl($r->tgl_lahir_wali,'Y-m-d');
	                $mhs->id_pdk_wali = empty($r->pdk_wali) ? NULL : $r->pdk_wali;
	                $mhs->id_pekerjaan_wali = empty($r->pekerjaan_wali) ? NULL : $r->pekerjaan_wali;
	                $mhs->id_penghasilan_wali = empty($r->penghasilan_wali) ? NULL : $r->penghasilan_wali;
	                $mhs->hp_wali = $r->hp_wali;
	                $mhs->jenis_tinggal = empty($r->jenis_tinggal) ? NULL : $r->jenis_tinggal;
	                $mhs->alat_transpor = empty($r->alat_transpor) ? NULL : $r->alat_transpor;
	                $mhs->save();

	                Rmt::Success('Berhasil menyimpan data');
	                $response = ['error' => 0, 'msg' => 'sukses'];
	            });
	        } catch(\Exception $e) {
	            $response = ['error' => 1, 'msg' => $e->getMessage()];
	        }

	        Rmt::success('Berhasil menyimpan data');
	        return Response::json($response,200);
	    }

    /* Dosen */

	    public function lmsDsn(Request $r)
	    {

		    $data['r'] = $this->jadwal($r->id_jdk);

	        $data['id_dosen'] = $r->id_dosen;

	        $jml_pertemuan = Rmt::jmlPertemuan($r->id_jdk);

        	// $data['jml_pertemuan'] = $jml_pertemuan == 14 ? $jml_pertemuan + 2 : $jml_pertemuan;
        	$data['jml_pertemuan'] = $jml_pertemuan + 2;

	        $data['undangan'] = $this->pesertaUndangan('0', $r->id_jdk);

	        $data['peserta_undangan'] = $this->pesertaUndangan('1', $r->id_jdk);
            
		    return view('mobile.lms-dsn.index', $data);
	    }

	    private function jadwal($id_jdk)
	    {
			$data = DB::table('jadwal_kuliah as jdk')
	            ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
	            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
	            ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
	            ->select('jdk.id','jdk.jenis', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk', 'jdk.kode_kls','jdk.id_prodi')
	            ->where('jdk.id', $id_jdk)
	            ->first();

	        return $data;
	    }

	    private function pesertaUndangan($aktif, $id_jadwal)
	    {
	        $data = DB::table('lms_peserta_undangan as pu')
	                    ->join('mahasiswa_reg as m1', 'pu.id_peserta', 'm1.id')
	                    ->join('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
	                    ->join('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
	                    ->select('pu.id', 'pu.id_peserta', 'm1.nim', 'm2.nm_mhs', 'pr.nm_prodi', 'pr.jenjang')
	                    ->where('aktif', $aktif)
	                    ->where('pu.id_jadwal', $id_jadwal)
	                    ->get();

	        return $data;
	    }

	    public function lmsUploadMateri(Request $r)
	    {
	        $id_dosen = $r->id_dosen;

	        if ( empty($id_dosen) ) {
	            return Response::json(['Data Not Found'], 404);
	        }

	        try {

	            DB::transaction(function() use($r, $id_dosen, &$res, &$id_materi, &$bm) {

	                $bm = BankMateri::findOrFail($r->id);

	                $m = new Materi;
	                $m->id_jadwal = $r->id_jadwal;
	                $m->id_bank_materi = $bm->id;
	                $m->id_dosen = $id_dosen;
	                $m->judul = Rmt::removeExtensi($bm->file);
	                $m->save();
	                $id_materi = $m->id;

	                $urutan = Resources::where('id_jadwal', $r->id_jadwal)
	                            ->where('pertemuan_ke', $r->pertemuan)
	                            ->max('urutan');
	                $urutan = empty($urutan) ? 1 : $urutan + 1;

	                $res = new Resources;
	                $res->id_jadwal = $r->id_jadwal;
	                $res->id_resource = $id_materi;
	                $res->jenis = 'materi';
	                $res->pertemuan_ke = $r->pertemuan;
	                $res->urutan = $urutan;
	                $res->save();
	            });

	            return Response::json(['id' => $res->id, 'id_materi' => $id_materi, 'file' => $bm->file]);

	        } catch( \Exception $e ) {
	            return Response::json([$e->getMessage()], 422);
	        }
	    }

	    public function lmsMateriAdd(Request $r, $id)
	    {

	        $data['r'] = $this->jadwal($id);

	        return view('mobile.lms-dsn.materi-add', $data);
	    }

	    public function lmsMateriStore(Request $r)
	    {

	        $this->validate($r, [
	            'judul' => 'required|max:255',
	            'nama_file' => 'required'
	        ]);


	        try {

	            DB::transaction(function() use($r) {
	                $id_dosen = $r->id_dosen;

	                $id_bank_materi = $r->id_bank_materi;

	                if ( $r->sumber_materi == 'upload' ) {

	                    $cek = BankMateri::where('file', $r->nama_file)->count();

	                    if ( $cek == 0 ) {
	                        $bm = new BankMateri;
	                        $bm->id_dosen = $id_dosen;
	                        $bm->file = $r->nama_file;
	                        $bm->save();

	                        $id_bank_materi = $bm->id;

	                        $path = config('app.lms-materi').'/' . $id_dosen;
	                        if ( !file_exists($path) ) {
	                            File::makeDirectory($path, $mode = 0777, true, true);
	                        }

	                        $lokasi_tmp = config('app.lms-tmp').'/'.$r->nama_file;
	                        $destinasi = config('app.lms-materi').'/'.$id_dosen.'/'.$r->nama_file;
	                        File::move($lokasi_tmp, $destinasi);

	                    } else {
	                        $tmp = config('app.lms-tmp').'/' . $r->nama_file;
	                        if ( !file_exists($tmp) ) {
	                            unlink($tmp);
	                        }
	                    }
	                }


	                $m = new Materi;
	                $m->id_jadwal = $r->id_jadwal;
	                $m->id_bank_materi = $id_bank_materi;
	                $m->id_dosen = $id_dosen;
	                $m->judul = $r->judul;
	                $m->deskripsi = $r->deskripsi;
	                $m->save();
	                $id_materi = $m->id;

	                $urutan = Resources::where('id_jadwal', $r->id_jadwal)
	                            ->where('pertemuan_ke', $r->pertemuan)
	                            ->max('urutan');
	                $urutan = empty($urutan) ? 1 : $urutan + 1;

	                $res = new Resources;
	                $res->id_jadwal = $r->id_jadwal;
	                $res->id_resource = $id_materi;
	                $res->jenis = 'materi';
	                $res->pertemuan_ke = $r->pertemuan;
	                $res->urutan = $urutan;
	                $res->save();
	            });

	        } catch(\Exception $e){
	            return Response::json([$e->getMessage()], 422);
	        }

	        Rmt::success('Berhasil menyimpan data');
	    }

	    public function lmsUploadTmp(Request $r)
	    {

	        try {
	            if ( $r->hasFile('file') ) {

	                $name = $r->file->getClientOriginalName();
	                $destinationPath = config('app.lms-tmp').'';
	                $r->file->move($destinationPath, $name);

	            } else {
	                return Response::json('Tidak ada file', 422);
	            }
	        } catch( \Exception $e ) {
	            return Response::json($e->getMessage(), 422);
	        }
	    }

	    public function lmsMateriEdit(Request $r, $id)
	    {
	        $data['r'] = $this->jadwal($id);

	        $data['materi'] = DB::table('lms_materi as m')
	                        ->leftJoin('lms_bank_materi as bm', 'm.id_bank_materi', 'bm.id')
	                        ->where('m.id', $r->id_materi)
	                        ->select('m.*','bm.file', 'bm.id as id_bm')
	                        ->first();

	        return view('mobile.lms-dsn.materi-edit', $data);
	    }

	    public function lmsMateriUpdate(Request $r)
	    {

	        $this->validate($r, [
	            'judul' => 'required|max:255',
	            'nama_file' => 'required'
	        ]);


	        try {

	            DB::transaction(function() use($r) {
	                $id_dosen = $r->id_dosen;

	                $id_bank_materi = $r->id_bank_materi;

	                if ( $r->sumber_materi == 'upload' ) {

	                    $cek = BankMateri::where('file', $r->nama_file)->count();

	                    if ( $cek == 0 ) {
	                        $bm = new BankMateri;
	                        $bm->id_dosen = $id_dosen;
	                        $bm->file = $r->nama_file;
	                        $bm->save();

	                        $id_bank_materi = $bm->id;

	                        $path = config('app.lms-materi').'/' . $id_dosen;
	                        if ( !file_exists($path) ) {
	                            File::makeDirectory($path, $mode = 0777, true, true);
	                        }

	                        $lokasi_tmp = config('app.lms-tmp').'/'.$r->nama_file;
	                        $destinasi = config('app.lms-materi').'/'.$id_dosen.'/'.$r->nama_file;
	                        File::move($lokasi_tmp, $destinasi);

	                    } else {
	                        $tmp = config('app.lms-tmp').'/' . $r->nama_file;
	                        if ( !file_exists($tmp) ) {
	                            unlink($tmp);
	                        }
	                    }

	                } else {
	                    $bm = BankMateri::find($id_bank_materi);
	                    $bm->file = $r->nama_file;
	                    $bm->save();
	                }


	                $m = Materi::find($r->id_materi);
	                $m->id_bank_materi = $id_bank_materi;
	                $m->judul = $r->judul;
	                $m->deskripsi = $r->deskripsi;
	                $m->save();

	            });

	        } catch(\Exception $e){
	            return Response::json([$e->getMessage()], 422);
	        }

	        Rmt::success('Berhasil menyimpan data');
	    }

	    public function lmsMateriView(Request $r, $id_materi, $id_dosen)
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

	    /* Tugas */
	        public function lmsTugasAdd(Request $r, $id)
	        {

	            $data['r'] = $this->jadwal($id);

	            return view('mobile.lms-dsn.tugas-add', $data);
	        }

	        public function lmsTugasStore(Request $r)
	        {
	            $this->validate($r, [
	                'judul' => 'required|max:255'
	            ]);

	            $tgl_mulai = !empty($r->mulai_berlaku) ? Carbon::parse($r->mulai_berlaku) : '';
	            $tgl_berakhir = !empty($r->tgl_berakhir) ? Carbon::parse($r->tgl_berakhir) : '';
	            $tgl_tutup = !empty($r->tgl_tutup) ? Carbon::parse($r->tgl_tutup) : '';

	            $errors = [];

	            if ( !empty($tgl_mulai) && !empty($tgl_berakhir) ) {
	                if ( $tgl_mulai->greaterThanOrEqualTo($tgl_berakhir) ) {
	                    $errors[] = 'Tanggal mulai pengiriman harus lebih kecil dari tgl jatuh tempo.';
	                }
	            }

	            if ( !empty($tgl_tutup) && !empty($tgl_berakhir) ) {
	                if ( $tgl_berakhir->greaterThan($tgl_tutup) ) {
	                    $errors[] = 'Tanggal jatuh tempo harus lebih kecil atau sama dengan tgl batas akhir upload.';
	                }
	            }

	            if ( !empty($r->minimal_kata) && !empty($r->maksimal_kata) ) {
	                if ( $r->minimal_kata > $r->maksimal_kata ) {
	                    $errors[] = 'Jumlah minimal kata harus lebih kecil/sama dengan jumlah maksimal kata.';
	                }
	            }

	            if ( count($errors) > 0 ) {
	                return Response::json($errors, 422);
	            }

	            try {

	                DB::transaction(function() use($r) {
	                    $id_dosen = $r->id_dosen;

	                    $nm_file = $r->nama_file;

	                    if ( $r->sumber_file == 'upload' ) {

	                        $cek = BankMateri::where('file', $r->nama_file)->count();

	                        if ( $cek == 0 ) {
	                            $bm = new BankMateri;
	                            $bm->id_dosen = $id_dosen;
	                            $bm->file = $r->nama_file;
	                            $bm->save();

	                            $path = config('app.lms-materi').'/' . $id_dosen;
	                            if ( !file_exists($path) ) {
	                                File::makeDirectory($path, $mode = 0777, true, true);
	                            }

	                            $lokasi_tmp = config('app.lms-tmp').'/'.$r->nama_file;
	                            $destinasi = config('app.lms-materi').'/'.$id_dosen.'/'.$r->nama_file;
	                            File::move($lokasi_tmp, $destinasi);

	                        } else {
	                            $tmp = config('app.lms-tmp').'/' . $r->nama_file;
	                            if ( !file_exists($tmp) ) {
	                                unlink($tmp);
	                            }

	                        }

	                    }

	                    $tgl_berakhir = $r->tgl_berakhir;

	                    if ( !empty($r->tgl_tutup) && empty($r->tgl_berakhir) ) {
	                        $tgl_berakhir = $r->tgl_tutup;
	                    }

	                    $data = new Tugas;
	                    $data->id_jadwal = $r->id_jadwal;
	                    $data->id_dosen = $id_dosen;
	                    $data->judul = $r->judul;
	                    $data->jenis = $r->jenis;

	                    if ( !empty($nm_file) ) {
	                        $data->file = $nm_file;
	                    }

	                    $data->deskripsi = $r->deskripsi;
	                    $data->mulai_berlaku = empty($r->mulai_berlaku) ? null : Carbon::parse($r->mulai_berlaku)->format('Y-m-d h:i');
	                    $data->tgl_berakhir = empty($tgl_berakhir) ? null : Carbon::parse($tgl_berakhir)->format('Y-m-d h:i');
	                    $data->tgl_tutup = empty($r->tgl_tutup) ? null : Carbon::parse($r->tgl_tutup)->format('Y-m-d h:i');
	                    $data->jenis_pengiriman = $r->jenis_pengiriman;
	                    $data->min_teks = $r->minimal_kata;
	                    $data->max_teks = $r->maksimal_kata;
	                    $data->max_file_upload = $r->max_file_upload;
	                    $data->max_attempt = $r->max_attempt;
	                    $data->save();
	                    $id_tugas = $data->id;

	                    $urutan = Resources::where('id_jadwal', $r->id_jadwal)
	                                ->where('pertemuan_ke', $r->pertemuan)
	                                ->max('urutan');
	                    $urutan = empty($urutan) ? 1 : $urutan + 1;

	                    $res = new Resources;
	                    $res->id_jadwal = $r->id_jadwal;
	                    $res->id_resource = $id_tugas;
	                    $res->jenis = 'tugas';
	                    $res->pertemuan_ke = $r->pertemuan;
	                    $res->urutan = $urutan;
	                    $res->save();

	                });

	            } catch(\Exception $e){
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	        public function lmsTugasEdit(Request $r, $id)
	        {

	            $data['r'] = $this->jadwal($id);

	            $data['tugas'] = Tugas::findOrFail($r->id_tugas);

	            return view('mobile.lms-dsn.tugas-edit', $data);
	        }

	        public function lmsTugasUpdate(Request $r)
	        {

	            $this->validate($r, [
	                'judul' => 'required|max:255'
	            ]);

	            $errors = [];

	            $tgl_mulai = !empty($r->mulai_berlaku) ? Carbon::parse($r->mulai_berlaku) : '';
	            $tgl_berakhir = !empty($r->tgl_berakhir) ? Carbon::parse($r->tgl_berakhir) : '';
	            $tgl_tutup = !empty($r->tgl_tutup) ? Carbon::parse($r->tgl_tutup) : '';

	            $errors = [];

	            if ( !empty($tgl_mulai) && !empty($tgl_berakhir) ) {
	                if ( $tgl_mulai->greaterThanOrEqualTo($tgl_berakhir) ) {
	                    $errors[] = 'Tanggal mulai pengiriman harus lebih kecil dari tgl jatuh tempo.';
	                }
	            }

	            if ( !empty($tgl_tutup) && !empty($tgl_berakhir) ) {
	                if ( $tgl_berakhir->greaterThan($tgl_tutup) ) {
	                    $errors[] = 'Tanggal jatuh tempo harus lebih kecil atau sama dengan tgl batas akhir upload.';
	                }
	            }

	            if ( !empty($r->minimal_kata) && !empty($r->maksimal_kata) ) {
	                if ( $r->minimal_kata > $r->maksimal_kata ) {
	                    $errors[] = 'Jumlah minimal kata harus lebih kecil/sama dengan jumlah maksimal kata.';
	                }
	            }

	            if ( count($errors) > 0 ) {
	                return Response::json($errors, 422);
	            }


	            try {

	                DB::transaction(function() use($r) {
	            		$id_dosen = $r->id_dosen;

	                    if ( $r->sumber_file == 'upload' ) {

	                        $cek = BankMateri::where('file', $r->nama_file)->count();

	                        if ( $cek == 0 ) {
	                            $bm = new BankMateri;
	                            $bm->id_dosen = $id_dosen;
	                            $bm->file = $r->nama_file;
	                            $bm->save();

	                            $path = config('app.lms-materi').'/' . $id_dosen;
	                            if ( !file_exists($path) ) {
	                                File::makeDirectory($path, $mode = 0777, true, true);
	                            }

	                            $lokasi_tmp = config('app.lms-tmp').'/'.$r->nama_file;
	                            $destinasi = config('app.lms-materi').'/'.$id_dosen.'/'.$r->nama_file;
	                            File::move($lokasi_tmp, $destinasi);

	                        } else {
	                            $tmp = config('app.lms-tmp').'/' . $r->nama_file;
	                            if ( !file_exists($tmp) ) {
	                                unlink($tmp);
	                            }

	                        }

	                    }

	                    $tgl_berakhir = $r->tgl_berakhir;

	                    if ( !empty($r->tgl_tutup) && empty($r->tgl_berakhir) ) {
	                        $tgl_berakhir = $r->tgl_tutup;
	                    }

	                    $data = Tugas::findOrFail($r->id);
	                    $data->judul = $r->judul;
	                    $data->jenis = $r->jenis;
	                    $data->file = $r->nama_file;
	                    $data->deskripsi = $r->deskripsi;
	                    $data->mulai_berlaku = empty($r->mulai_berlaku) ? null : Carbon::parse($r->mulai_berlaku)->format('Y-m-d h:i');
	                    $data->tgl_berakhir = empty($tgl_berakhir) ? null : Carbon::parse($tgl_berakhir)->format('Y-m-d h:i');
	                    $data->tgl_tutup = empty($r->tgl_tutup) ? null : Carbon::parse($r->tgl_tutup)->format('Y-m-d h:i');
	                    $data->jenis_pengiriman = $r->jenis_pengiriman;
	                    $data->min_teks = $r->minimal_kata;
	                    $data->max_teks = $r->maksimal_kata;
	                    $data->max_file_upload = $r->max_file_upload;
	                    $data->max_attempt = $r->max_attempt;
	                    $data->save();

	                });

	                Rmt::success('Berhasil mengubah data');

	            } catch(\Exception $e){
	                return Response::json([$e->getMessage()], 422);
	            }

	        }

	        public function lmsTugasDetail(Request $r, $id_jadwal, $id)
	        {

	            $data['r'] = $this->jadwal($id_jadwal);

	            $data['tugas'] = Tugas::findOrFail($id);

	            $peserta_undangan = DB::table('lms_peserta_undangan as pu')
	                    ->leftJoin('mahasiswa_reg as m1', 'pu.id_peserta', 'm1.id')
	                    ->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
	                    ->select('m1.id as id_mhs_reg', 'm1.nim', 'm2.nm_mhs')
	                    ->where('pu.id_jadwal', $id_jadwal)
	                    ->where('aktif', '1');

	            $data['peserta_kelas'] = DB::table('nilai as n')
	                    ->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
	                    ->leftJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
	                    ->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
	                    ->select('n.id_mhs_reg', 'm2.nim', 'm1.nm_mhs')
	                    ->where('n.id_jdk', $id_jadwal)
	                    ->union($peserta_undangan)
	                    ->orderBy('nim')
	                    ->get();

	            if ( $r->act == 'grade' ) {
	                return view('mobile.lms-dsn.tugas-grade', $data);
	            } else {
	                return view('mobile.lms-dsn.tugas-detail', $data);
	            }
	        }


	        public function lmsTugasGrade(Request $r)
	        {

	            try {

	                $jawab = DB::table('lms_jawaban_tugas')
	                        ->where('id_peserta', $r->pk)
	                        ->where('id_tugas', $r->id_tugas)
	                        ->first();

	                if ( empty($jawab) ) {

	                    if ( $r->name == 'nilai' ) {

	                        if ( ( (int)$r->value < 1 || (int)$r->value > 100 ) && $r->value != '' ) {
	                            return Response::json(['Penilaian hanya boleh pada rentang 1 - 100.'], 422);
	                        }

	                        $data = [
	                            'id_peserta' => $r->pk,
	                            'id_tugas' => $r->id_tugas,
	                            'nilai' => (int)$r->value,
	                            'tgl_kumpul' => null
	                        ];

	                        DB::table('lms_jawaban_tugas')->insert($data);

	                    } else {
	                        $data = [
	                            'id_peserta' => $r->pk,
	                            'id_tugas' => $r->id_tugas,
	                            'comment' => $r->value,
	                            'tgl_kumpul' => null
	                        ];

	                        DB::table('lms_jawaban_tugas')->insert($data);
	                    }

	                } else {

	                    if ( $r->name == 'nilai' ) {
	                        if ( ((int)$r->value < 1 || (int)$r->value > 100) && $r->value != '' ) {
	                            return Response::json(['Penilaian hanya boleh pada rentang 1 - 100.'], 422);
	                        }

	                        $data = ['nilai' => (int)$r->value];
	                    } else {
	                        $data = ['comment' => $r->value];
	                    }

	                    DB::table('lms_jawaban_tugas')
	                        ->where('id', $jawab->id)
	                        ->update($data);
	                }

	            } catch(\Exception $e) {
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	        public function lmsTugasJawaban(Request $r)
	        {
	            try {

	                $data = DB::table('lms_jawaban_tugas')
	                        ->where('id', $r->id_jawaban)
	                        ->first();

	                echo !empty($data) && !empty($data->jawaban) ? $data->jawaban : 'Tidak dijawab/Belum dijawab oleh peserta';
	            } catch( \Exception $e ) {
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	        public function lmsTugasDownload(Request $r)
	        {
	            try {

	                $files = config('app.lms-tugas').'/'.$r->id_tugas.'-'.$r->id_jadwal;
	                $fileTmp = config('app.lms-tmp').'/'.$r->judul.'.zip';
	                Zipper::make($fileTmp)->add($files)->close();

	                return Response::download($fileTmp)->deleteFileAfterSend(true);

	            } catch(\Exception $e){
	                Rmt::error('Gagal mendownload data: '.$e->getMessage());
	                return redirect()->back();
	            }
	        }

	        public function lmsTugasDownloadSingle(Request $r, $id)
	        {

	            $file = config('app.lms-tugas').'/'.$id.'-'.$r->id_jadwal.'/'.$r->nim.'-'.trim($r->nama).'/'.$r->file;

	            return Response::download($file);
	        }

	        public function lmsTugasViewAttach(Request $r, $id_tugas, $id_dosen)
	        {

	            $tugas = DB::table('lms_tugas as t')
	                    ->where('t.id', $id_tugas)
	                    ->where('t.id_dosen', $id_dosen)
	                    ->select('t.*')
	                    ->first();

	            if ( !empty($tugas) ) {

	                $file = config('app.lms-materi').'/'.$id_dosen.'/'.$r->file;

	                return Response::download($file);

	            } else {
	                echo 'Tidak ada data';
	            }
	        }

	    /* end tugas */

	    /* Catatan */

	        public function lmsCatatanAdd(Request $r, $id)
	        {
	            $data['r'] = $this->jadwal($id);

	            return view('mobile.lms-dsn.catatan-add', $data);
	        }

	        public function lmsCatatanStore(Request $r)
	        {
	            $this->validate($r, [
	                'konten' => 'required'
	            ]);

	            try {

	                $data = new Catatan;
	                $data->konten = $r->konten;
	                $data->id_dosen = $r->id_dosen;
	                $data->id_jadwal = $r->id_jadwal;
	                $data->save();
	                $id = $data->id;

	                $urutan = Resources::where('id_jadwal', $r->id_jadwal)
	                            ->where('pertemuan_ke', $r->pertemuan)
	                            ->max('urutan');
	                $urutan = empty($urutan) ? 1 : $urutan + 1;

	                $res = new Resources;
	                $res->id_jadwal = $r->id_jadwal;
	                $res->id_resource = $id;
	                $res->jenis = 'catatan';
	                $res->pertemuan_ke = $r->pertemuan;
	                $res->urutan = $urutan;
	                $res->save();

	            } catch(\Exception $e) {
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	        public function lmsCatatanEdit(Request $r, $id)
	        {

	            $data['r'] = $this->jadwal($id);

	            $data['note'] = Catatan::findOrFail($r->id_catatan);

	            return view('mobile.lms-dsn.catatan-edit', $data);
	        }

	        public function lmsCatatanUpdate(Request $r, $id)
	        {
	            $this->validate($r, [
	                'konten' => 'required'
	            ]);

	            try {

	                $data = Catatan::findOrFail($id);
	                $data->konten = $r->konten;
	                $data->save();

	            } catch(\Exception $e) {
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	    /* end catatan */

	    /* Topik / forum */
	        public function lmsTopik(Request $r)
	        {
	            $id_dosen = $r->id_dosen;

	            $topik = Topik::where('id_jadwal', $r->id_jadwal)
	                            ->where('id_dosen', $id_dosen)
	                            ->orderBy('created_at', 'desc')
	                            ->get();

	            foreach( $topik as $val ) { ?>

	                <div class="thread-card" style="<?= $val->is_closed == 1 ? 'border-color: #0aa699':'' ?>">
	                    <a href="<?= route('dsnm_lms_topik_detail', ['id' => $val->id, 'jdw' => $r->id_jadwal, 'id_dosen' => $id_dosen]) ?>" class="btn-loadings">
	                        <h4 class="font-bold text-gray-900">
	                            <?= $val->judul ?>
	                            <span class="pull-right">
	                                <?php $jawaban =  $val->jawaban()->count(); ?>
	                                <?php if ($jawaban > 0 ) { ?>
	                                    <i class="fa fa-comment text-gray-600"></i>
	                                    <?= $jawaban ?>
	                                <?php } ?>
	                            </span>
	                        </h4>
	                        <p class="text-gray-600">
	                            <?= str_limit($val->konten, 150) ?>
	                        </p>
	                    </a>

	                    <div class="thread-info-avatar">
	                        <img src="<?= url('resources') ?>/assets/img/avatar.png" class="img-circle w-6 rounded-full mr-3">
	                    </div>
	                    <div class="text-gray-600">
	                        <a class="text-green-darker mr-2">
	                            <?php if ( $val->creator == $id_dosen ) { ?>
	                                Anda
	                            <?php } else {
	                                $mhs = DB::table('mahasiswa_reg as m1')
	                                        ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
	                                        ->select('m1.nim', 'm2.nm_mhs')
	                                        ->where('m1.id', $val->creator)
	                                        ->first();
	                                echo !empty($mhs) ? $mhs->nm_mhs.' - '.$mhs->nim : '-'; ?>
	                            <?php } ?>
	                        </a>
	                        <?= Rmt::WaktuLalu($val->created_at) ?>

	                        <div class="pull-right">
	                            <?php if ( $val->is_closed == 1 ) { ?>
	                                <a style="color: #0aa699"><i class="fa fa-check"></i> Topik ini telah selesai</a> &nbsp;
	                            <?php } else { ?>
	                                <a href='javascript:;' onclick="ubahTopik('<?= $val->id ?>')" class='btn btn-default btn-xs'><i class='fa fa-pencil'></i> Ubah</i></a>
	                            <?php } ?>
	                            <a href="<?= route('dsnm_lms_topik_delete', ['id' => $val->id, 'id_dosen' => $id_dosen]) ?>" onclick="return confirm('Anda ingin menghapus topik ini.?')" class='btn btn-danger btn-xs'><i class='fa fa-times'></i> Hapus</i></a>
	                        </div>
	                    </div>
	                </div>
	                <script type="text/javascript">
	                	$('.btn-loadings a, a.btn-loadings').click(function(){
				            $('#caplet-overlay').show();
				        });
	                </script>

	            <?php }

	            if ( count($topik) == 0 ) { ?>
	                <div class="alert alert-info" style="margin-bottom: 0">
	                    Belum ada data
	                </div>
	            <?php }
	        }

	        public function lmsTopikStore(Request $r)
	        {

	            $this->validate($r, [
	                'judul' => 'required',
	                'konten' => 'required'
	            ]);

	            try {

	                $data = new Topik;
	                $data->id_jadwal = $r->id_jadwal;
	                $data->id_dosen = $r->id_dosen;
	                $data->creator = $r->id_dosen;
	                $data->judul = $r->judul;
	                $data->konten = $r->konten;
	                $data->save();

	                Rmt::success('Berhasil menambah topik');

	            } catch( \Exception $e ) {
	                return Response::json([$e->getMessage()], 422);
	            }

	        }

	        public function lmsTopikEdit($id)
	        {

	            $topik = Topik::findOrFail($id);
	            ?>

	                    <?= csrf_field() ?>
	                    <input type="hidden" name="id" value="<?= $id ?>">
	                    <div class="form-group">
	                        <label>Judul</label>
	                        <input type="text" class="form-control" name="judul" value="<?= $topik->judul ?>">
	                    </div>
	                    <div class="form-group">
	                        <label>Konten</label>
	                        <textarea name="konten" id="konten-topik" class="form-control" rows="8"><?= $topik->konten ?></textarea>
	                    </div>
	                    <div class="form-group">
	                        <button class="btn btn-primary btn-sm" id="btn-submit-edit-topik"><i class="fa fa-save"></i> Simpan</button>
	                        <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
	                    </div>
	                </form>
	            <?php
	        }

	        public function lmsTopikUpdate(Request $r)
	        {
	            $this->validate($r, [
	                'judul' => 'required',
	                'konten' => 'required'
	            ]);

	            try {

	                $data = Topik::find($r->id);
	                $data->judul = $r->judul;
	                $data->konten = $r->konten;
	                $data->save();

	                Rmt::success('Berhasil mengubah topik');

	            } catch( \Exception $e ) {
	                return Response::json([$e->getMessage()], 422);
	            }

	        }

	        public function lmsTopikDetail(Request $r, $id)
	        {

	            $data['r'] = $this->jadwal($r->jdw);

	            $data['topik'] = Topik::findOrFail($id);

	            return view('mobile.lms-dsn.topik-detail', $data);
	        }

	        public function lmsTopikReply(Request $r, $id)
	        {

	            $this->validate($r, [
	                'konten' => 'required'
	            ]);

	            try {

	                $data = new TopikJawaban;
	                $data->id_topik = $id;
	                $data->id_user = $r->id_dosen;
	                $data->people = 'dsn';
	                $data->konten = $r->konten;
	                $data->save();

	                $res = [
	                    'id' => $data->id,
	                    'delete_url' => route('dsnm_lms_topik_reply_toggle_delete', ['id' => $data->id, 'id_topik' => $id, 'deleted' => 1, 'id_dosen' => $r->id_dosen]),
	                    'konten' => nl2br($r->konten)
	                ];

	                return Response::json($res);

	            } catch( \Exception $e ) {
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	        public function lmsTopikReplyUpdate(Request $r)
	        {

	            $this->validate($r, [
	                'konten' => 'required'
	            ]);

	            try {

	                $data = TopikJawaban::find($r->id);
	                $data->konten = $r->konten;
	                $data->save();

	            } catch( \Exception $e ) {
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	        public function lmsTopikDelete($id, $id_dosen)
	        {
	            DB::transaction(function()use($id, $id_dosen){
	                Topik::where('id', $id)
	                    ->where('id_dosen', $id_dosen)
	                    ->delete();

	                TopikJawaban::where('id_topik', $id)->delete();
	            });

	            Rmt::success('Berhasil menghapus data');
	            return redirect()->back();
	        }

	        public function lmsTopikReplyToggleDelete($id, $id_topik, $deleted, $id_dosen)
	        {
	            // untuk validasi delete ilegal
	            $topik = Topik::where('id', $id_topik)
	                        ->where('id_dosen', $id_dosen)
	                        ->count();

	            if ( $topik > 0 ) {
	                TopikJawaban::where('id', $id)->update(['is_deleted' => $deleted]);
	            } else {
	                return Response::json('404', 404);
	            }

	            Rmt::success('Aksi berhasil');
	            return redirect()->back();
	        }

	        public function lmsTopikTutup($id, $id_dosen)
	        {
	            $topik = Topik::where('id', $id)
	                        ->where('id_dosen', $id_dosen)
	                        ->update(['is_closed' => 1]);

	            Rmt::success('Berhasil menutup diskusi');
	            return redirect()->back();
	        }
	    /* End topik */

	    public function lmsUpdateUrutan(Request $r)
	    {
	        $urutan = explode("&",$r->urutan);

	        $no = 1;
	        foreach( $urutan as $val ){
	            $val = explode("=", $val);
	            $Menu = Resources::find($val[1]);
	            $Menu->urutan = $no;
	            $Menu->save();
	            $no++;
	        }
	    }

	    public function lmsPindahPertemuan(Request $r)
	    {
	        if ( empty($r->data) ) {
	            return redirect()->back();
	        }

	        try {

	            $data = json_decode($r->data);

	            foreach( $data as $val ) {
	                $res = Resources::find($val);

	                $urutan = Resources::where('id', $val)
	                                    ->where('pertemuan_ke', $r->pertemuan)
	                                    ->max('urutan');
	                $urutan = empty($urutan) ? 1 : $urutan + 1;

	                $res->pertemuan_ke = $r->pertemuan;
	                $res->save();
	            }

	            Rmt::success('Berhasil memindahkan materi');

	        } catch( \Exception $e ) {
	            return Response::json([$e->getMessage()], 422);
	        }
	    }

	    public function lmsDeleteResources(Request $r)
	    {

	        switch ($r->jenis) {
	            case 'materi':
	                $cek = DB::table('lms_resources as rs')
	                ->leftJoin('lms_materi as m', 'rs.id_resource', 'm.id')
	                ->where('rs.jenis', 'materi')
	                ->where('rs.id', $r->id)
	                ->where('m.id_dosen', $r->id_dosen)
	                ->select('rs.*')
	                ->first();

	                if ( !empty($cek) ) {
	                    DB::transaction(function()use($cek, $r){
	                        Resources::find($r->id)->delete();
	                        Materi::find($cek->id_resource)->delete();
	                    });
	                }
	            break;

	            case 'tugas':
	                $cek = DB::table('lms_resources as rs')
	                    ->leftJoin('lms_tugas as t', 'rs.id_resource', 't.id')
	                    ->where('rs.jenis', 'tugas')
	                    ->where('rs.id', $r->id)
	                    ->where('t.id_dosen', $r->id_dosen)
	                    ->select('rs.*', 't.file')
	                    ->first();

	                if ( !empty($cek) ) {

	                    DB::transaction(function()use($cek, $r){
	                        Resources::find($r->id)->delete();
	                        $file = config('app.lms-tugas').'/'.$r->id_dosen.'/'.$cek->file;
	                        if ( file_exists($file) ) {
	                            unlink($file);
	                        }

	                        Tugas::find($cek->id_resource)->delete();

	                        DB::table('lms_jawaban_tugas')->where('id_tugas', $cek->id_resource)->delete();
	                    });
	                }
	            break;

	            case 'catatan':
	                $cek = DB::table('lms_resources as rs')
	                    ->leftJoin('lms_catatan as t', 'rs.id_resource', 't.id')
	                    ->where('rs.jenis', 'catatan')
	                    ->where('rs.id', $r->id)
	                    ->where('t.id_dosen', $r->id_dosen)
	                    ->select('rs.*')
	                    ->first();

	                if ( !empty($cek) ) {

	                    DB::transaction(function()use($cek, $r){
	                        Resources::find($r->id)->delete();
	                        Catatan::find($cek->id_resource)->delete();
	                    });
	                } else {
	                    Rmt::error('Tidak ada data yang bisa dihapus');
	                    return redirect()->back();
	                }
	            break;

	            default:
	                Rmt::error('Tidak ada data yang bisa dihapus');
	                return redirect()->back();
	            break;
	        }

	        Rmt::success('Berhasil menghapus data');
	        return redirect()->back();
	    }

	    /* Peserta undangan */
	        public function getMhs(Request $r)
	        {

	        	$periode = Rmt::semesterBerjalan();

	            try {

	                if ( !empty($r->cari) ) {

	                    $mahasiswa = DB::table('krs_status as krs')
	                        ->rightJoin('mahasiswa_reg as m2', 'krs.id_mhs_reg', '=', 'm2.id')
	                        ->rightJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
	                        ->join('prodi as pr', 'm2.id_prodi', 'pr.id_prodi')
	                        ->select('m2.id','m2.nim','m1.nm_mhs','pr.nm_prodi', 'pr.jenjang')
	                        ->whereNotNull('krs.id')
	                        ->where('krs.id_smt', $periode['id'])
	                        ->where(function($q)use($r){
	                            $q->where('m2.nim', 'like', '%'.trim($r->cari).'%')
	                                ->orWhere('m1.nm_mhs', 'like', '%'.trim($r->cari).'%');
	                        })->take(10)->get();

	                    if ( count($mahasiswa) > 0 ) { ?>

	                        <table class="table table-bordered table-hover">
	                            <tr>
	                                <th>NIM</th>
	                                <th>Nama</th>
	                                <th>Prodi</th>
	                                <th>Aksi</th>
	                            </tr>
	                            <?php foreach( $mahasiswa as $mhs ) {

	                                $cek = PesertaUndangan::where('id_jadwal', $r->id_jadwal)
	                                        ->where('id_peserta', $mhs->id)->first(); ?>

	                                <tr>
	                                    <td align="center"><?= $mhs->nim ?></td>
	                                    <td><?= $mhs->nm_mhs ?></td>
	                                    <td><?= $mhs->nm_prodi ?> (<?= $mhs->jenjang ?>)</td>
	                                    <td align="center">
	                                        <?php
	                                        if ( !empty($cek) ) {
	                                            if ( $cek->aktif == '1' ) {
	                                                echo 'Telah bergabung';
	                                            } else { ?>
	                                                <a href="<?= route('dsnm_lms_approval_mhs', [$r->id_jadwal, $mhs->id]) ?>?approv=1"
	                                                    class="btn btn-danger btn-xs"
	                                                    onclick="return confirm('Anda ingin menyetujui mahasiswa ini bergabung?')">
	                                                    Setujui
	                                                </a>
	                                            <?php }

	                                        } else {

	                                            $is_peserta = DB::table('nilai')
	                                            ->where('id_mhs_reg', $mhs->id)
	                                            ->where('id_jdk', $r->id_jadwal)
	                                            ->count();

	                                            if ( $is_peserta > 0 ) {
	                                                echo 'Telah bergabung';
	                                            } else { ?>
	                                                <a href="javascript:;" class="mhs-<?= $mhs->id ?> btn btn-primary btn-xs" onclick="gabung('<?= $mhs->id ?>')">Masukkan</a>
	                                            <?php } ?>
	                                        <?php } ?>
	                                    </td>
	                                </tr>
	                            <?php } ?>
	                        </table>

	                    <?php } else { ?>
	                        Matakuliah tidak ditemukan, coba kata pencarian yang lain
	                    <?php }

	                } else {
	                    return Response::json(['Kolom pencarian belum diisi'], 422);
	                }

	            } catch( \Exception $e ) {
	                return Response::json([$e->getMessage()], 422);
	            }
	        }

	        public function undangMhs(Request $r, $id_jadwal, $id_peserta)
	        {
	            try {

	                $cek = PesertaUndangan::where('id_jadwal', $id_jadwal)
	                        ->where('id_peserta', $id_peserta)
	                        ->count();

	                if ( $cek > 0 ) {
	                    Rmt::error('Mahasiswa ini telah ada');
	                    return redirect()->back();
	                }

	                $data = new PesertaUndangan;
	                $data->id_peserta = $id_peserta;
	                $data->id_jadwal = $id_jadwal;
	                $data->aktif = '1';
	                $data->save();

	                Rmt::success('Berhasil memasukkan mahasiswa.');

	            } catch( \Exception $e ) {
	                Rmt::error('Gagal memasukkan mahasiswa, coba muat ulang halaman ini.');
	                return redirect()->back();
	            }

	            return redirect()->back();
	        }

	        public function approvalMhs(Request $r, $id_jadwal, $id_peserta = '')
	        {

	            try {

	                if ( $r->approv == 'all' ) {
	                    $data = PesertaUndangan::where('id_jadwal', $id_jadwal)
	                            ->update(['aktif' => '1']);
	                    Rmt::success('Berhasil memasukkan peserta');
	                } else {

	                    $data = PesertaUndangan::where('id_peserta', $id_peserta)
	                        ->where('id_jadwal', $id_jadwal);

	                    if ( $r->approv == '1' ) {
	                        Rmt::success('Berhasil memasukkan peserta');
	                        $data->update(['aktif' => '1']);
	                    } else {
	                        Rmt::success('Berhasil menolak peserta');
	                        $data->delete();
	                    }
	                }

	           } catch( \Exception $e ) {
	                Rmt::error($e->getMessage());
	                return redirect()->back();
	            }

	            return redirect()->back();
	        }

	        public function hapusUndangan($id_peserta, $id_jadwal)
	        {
	            PesertaUndangan::where('id_jadwal', $id_jadwal)
	                ->where('id_peserta', $id_peserta)
	                ->delete();

	            Rmt::success('Berhasil menghapus permintaan');

	            return redirect()->back();
	        }
}
