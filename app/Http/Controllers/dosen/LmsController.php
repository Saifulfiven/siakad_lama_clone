<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB, Sia, Rmt, Response, Session, Auth, File, Carbon, Zipper;
use App\Materi, App\BankMateri, App\Resources, App\Tugas, App\Catatan, App\Topik, App\TopikJawaban, App\PesertaUndangan, App\Kuis, App\KuisHasil, App\KuisSoal, App\TelahKuis, App\Video;

trait LmsController
{

    public function lms(Request $r, $id)
    {

    	$jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : $r->jenis;
    	$query = Sia::jadwalKuliah('x', $jenis);

	    $data['r'] = $query->where('jdk.id',$id)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id)->first();
        }

    	$data['peserta'] = Sia::pesertaKelas($data['r']->id)->toArray();

        $data['id_jdk'] = $id;

        $jml_pertemuan = Sia::jmlPertemuan($id);

        // $data['jml_pertemuan'] = $jml_pertemuan == 14 ? $jml_pertemuan + 2 : $jml_pertemuan;
        $data['jml_pertemuan'] = $jml_pertemuan + 2;

        $data['undangan'] = $this->pesertaUndangan('0', $id);

        $data['peserta_undangan'] = $this->pesertaUndangan('1', $id);
        
	    return view('dsn.lms.index', $data);
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

    public function lmsUploadFile(Request $r)
    {
        $id_dosen = Sia::sessionDsn();

        try {
            if ( $r->hasFile('file') ) {

                // DB::transaction(function() use($r, $id_dosen, &$res, &$id_materi) {

                    $name = $r->file->getClientOriginalName();
                    $cek = BankMateri::where('file', $name)->first();

                    if ( empty($cek) ) {
                        $destinationPath = config('app.lms-materi').'/'.$id_dosen;
                        $r->file->move($destinationPath, $name);

                        $bm = new BankMateri;
                        $bm->id_dosen = $id_dosen;
                        $bm->file = $name;
                        $bm->save();
                        $id_bank_materi = $bm->id;
                    } else {
                        $id_bank_materi = $cek->id;
                    }

                    $m = new Materi;
                    $m->id_jadwal = $r->id_jadwal;
                    $m->id_bank_materi = $id_bank_materi;
                    $m->id_dosen = $id_dosen;
                    $m->judul = Rmt::removeExtensi($name);
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
                // });

                return Response::json(['id' => $res->id, 'id_materi' => $id_materi]);

            } else {
                return Response::json('Tidak ada file', 422);
            }
        } catch( \Exception $e ) {
            return Response::json($e->getMessage(), 422);
        }
    }

    public function lmsUploadMateri(Request $r)
    {
        $id_dosen = Sia::sessionDsn();

        if ( empty($id_dosen) ) {
            return Response::json(['Data Not Found'], 404);
        }

        try {

            // DB::transaction(function() use($r, $id_dosen, &$res, &$id_materi, &$bm) {

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
            // });

            return Response::json(['id' => $res->id, 'id_materi' => $id_materi, 'file' => $bm->file]);

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function lmsMateriAdd(Request $r, $id)
    {
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id)->first();
        }
        return view('dsn.lms.materi-add', $data);
    }

    public function lmsMateriStore(Request $r)
    {

        $this->validate($r, [
            'judul' => 'required|max:255',
            'nama_file' => 'required'
        ]);


        try {

            // DB::transaction(function() use($r) {
                $id_dosen = Sia::sessionDsn();

                $id_bank_materi = $r->id_bank_materi;

                if ( $r->sumber_materi == 'upload' ) {

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

                    $tmp = config('app.lms-tmp').'/' . $r->nama_file;
                    if ( file_exists($tmp) ) {
                        unlink($tmp);
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
            // });

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
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id)->first();
        }

        $data['materi'] = DB::table('lms_materi as m')
                        ->leftJoin('lms_bank_materi as bm', 'm.id_bank_materi', 'bm.id')
                        ->where('m.id', $r->id_materi)
                        ->select('m.*','bm.file', 'bm.id as id_bm')
                        ->first();

        return view('dsn.lms.materi-edit', $data);
    }

    public function lmsMateriUpdate(Request $r)
    {

        $this->validate($r, [
            'judul' => 'required|max:255',
            'nama_file' => 'required'
        ]);


        try {

            DB::transaction(function() use($r) {
                $id_dosen = Sia::sessionDsn();

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

            $file_materi = !empty($materi->file) ? $materi->file : 'undefined';

            $file = config('app.lms-materi').'/'.$id_dosen.'/'.$file_materi;

            if ( file_exists($file) ) {
                return Response::file($file);
            } else {
                echo "<center><h4>File tidak ditemukan</h4></center>";
            }

        } else {
            echo 'Tidak ada data';
        }
    }

    /* Tugas */
        public function lmsTugasAdd(Request $r, $id)
        {
            $query = Sia::jadwalKuliah('x');

            $data['r'] = $query->where('jdk.id',$id)->first();

            if ( empty($data['r']) ) {
                // Semester pendek
                $query = Sia::jadwalKuliah('x', 2);
                $data['r'] = $query->where('jdk.id',$id)->first();
            }

            $jenis_ujian = $this->jenisUjian(Sia::sessionPeriode());

            $data['jadwal_ujian'] = DB::table('jadwal_ujian')
                                ->where('id_jdk', $id)
                                ->where('jenis_ujian', $jenis_ujian)
                                ->first();

            return view('dsn.lms.tugas-add', $data);
        }

        private function jenisUjian($id_smt)
        {
            $data = DB::table('jadwal_ujian as jdu')
                ->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
                ->where('jdk.id_smt', $id_smt)
                ->where('jdu.jenis_ujian', 'UAS')
                ->count();

            return $data > 0 ? 'UAS':'UTS';
        }

        public function lmsTugasStore(Request $r)
        {
            // dd($r);

            $this->validate($r, [
                'judul' => 'required|max:255'
            ]);

            try {
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

                $id_dosen = Sia::sessionDsn();

                DB::transaction(function() use($r) {
                    $id_dosen = Sia::sessionDsn();

                    $nm_file = $r->nama_file;
                    // $file = $r->file('file');
                    // $name_file = uniqid() . '.';
                    // $file->getClientOriginalExtension();

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
                    //  elseif ( $r->sumber_file == 'dokumen' ) {
                    //     $bm = BankMateri::find($r->id_bank_materi);
                    //     $bm->file = $r->nama_file;
                    //     $bm->save();
                    // }

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
                    $data->mulai_berlaku = empty($r->mulai_berlaku) ? null : Carbon::parse($r->mulai_berlaku)->format('Y-m-d H:i');
                    $data->tgl_berakhir = empty($tgl_berakhir) ? null : Carbon::parse($tgl_berakhir)->format('Y-m-d H:i');
                    $data->tgl_tutup = empty($r->tgl_tutup) ? null : Carbon::parse($r->tgl_tutup)->format('Y-m-d H:i');
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
            $query = Sia::jadwalKuliah('x');

            $data['r'] = $query->where('jdk.id',$id)->first();

            if ( empty($data['r']) ) {
                // Semester pendek
                $query = Sia::jadwalKuliah('x', 2);
                $data['r'] = $query->where('jdk.id',$id)->first();
            }

            $data['tugas'] = Tugas::findOrFail($r->id_tugas);

            return view('dsn.lms.tugas-edit', $data);
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
                    $id_dosen = Sia::sessionDsn();

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
                    $data->file = $r->nama_file;
                    $data->jenis = $r->jenis;
                    $data->deskripsi = $r->deskripsi;
                    $data->mulai_berlaku = empty($r->mulai_berlaku) ? null : Carbon::parse($r->mulai_berlaku)->format('Y-m-d H:i');
                    $data->tgl_berakhir = empty($tgl_berakhir) ? null : Carbon::parse($tgl_berakhir)->format('Y-m-d H:i');
                    $data->tgl_tutup = empty($r->tgl_tutup) ? null : Carbon::parse($r->tgl_tutup)->format('Y-m-d H:i');
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
            $query = Sia::jadwalKuliah('x');

            $data['r'] = $query->where('jdk.id',$id_jadwal)->first();

            if ( empty($data['r']) ) {
                // Semester pendek
                $query = Sia::jadwalKuliah('x', 2);
                $data['r'] = $query->where('jdk.id',$id_jadwal)->first();
            }

            $data['tugas'] = Tugas::findOrFail($id);

            if ( $r->debug ) {
                echo nl2br($data['tugas']->deskripsi);
                exit;
            }

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
                return view('dsn.lms.tugas-grade', $data);
            } else {
                return view('dsn.lms.tugas-detail', $data);
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

            try {

                $file = config('app.lms-tugas').'/'.$id.'-'.$r->id_jadwal.'/'.$r->nim.'-'.trim($r->nama).'/'.$r->file;

                return Response::download($file);

            } catch(\Exception $e){
                Rmt::error('Gagal mendownload data: '.$e->getMessage());
                return redirect()->back();
            }
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
                if ( !file_exists($file) ) {
                    echo '<center><h3>File tidak ditemukan</h3></center>';
                    exit;
                }

                return Response::download($file);

            } else {
                echo 'Tidak ada data';
            }
        }

    /* end tugas */

    /* Catatan */

        public function lmsCatatanAdd(Request $r, $id)
        {
            $query = Sia::jadwalKuliah('x');

            $data['r'] = $query->where('jdk.id',$id)->first();
            if ( empty($data['r']) ) {
                // Semester pendek
                $query = Sia::jadwalKuliah('x', 2);
                $data['r'] = $query->where('jdk.id',$id)->first();
            }

            return view('dsn.lms.catatan-add', $data);
        }

        public function lmsCatatanStore(Request $r)
        {
            $this->validate($r, [
                'konten' => 'required'
            ]);

            try {

                $data = new Catatan;
                $data->konten = $r->konten;
                $data->id_dosen = Sia::sessionDsn();
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

        public function lmsCatatanEdit(Request $r, $id, $id_catatan)
        {
            $query = Sia::jadwalKuliah('x');

            $data['r'] = $query->where('jdk.id',$id)->first();

            if ( empty($data['r']) ) {
                // Semester pendek
                $query = Sia::jadwalKuliah('x', 2);
                $data['r'] = $query->where('jdk.id',$id)->first();
            }

            $data['note'] = Catatan::findOrFail($id_catatan);

            return view('dsn.lms.catatan-edit', $data);
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
            $id_dosen = Sia::sessionDsn();

            $topik = Topik::where('id_jadwal', $r->id_jadwal)
                            ->where('id_dosen', $id_dosen)
                            ->orderBy('created_at', 'desc')
                            ->get();

            foreach( $topik as $val ) { ?>

                <div class="thread-card" style="<?= $val->is_closed == 1 ? 'border-color: #0aa699':'' ?>">
                    <a href="<?= route('dsn_lms_topik_detail', ['id' => $val->id, 'jdw' => $r->id_jadwal]) ?>">
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
                        <img src="http://siakad.test/resources/assets/img/avatar.png" class="img-circle w-6 rounded-full mr-3">
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
                            <a href="<?= route('dsn_lms_topik_delete', ['id' => $val->id, 'id_dosen' => Sia::sessionDsn()]) ?>" onclick="return confirm('Anda ingin menghapus topik ini.?')" class='btn btn-danger btn-xs'><i class='fa fa-times'></i> Hapus</i></a>
                        </div>
                    </div>
                </div>

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
                $data->id_dosen = Sia::sessionDsn();
                $data->creator = Sia::sessionDsn();
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
            $query = Sia::jadwalKuliah('x');

            $data['r'] = $query->where('jdk.id',$r->jdw)->first();

            if ( empty($data['r']) ) {
                // Semester pendek
                $query = Sia::jadwalKuliah('x', 2);
                $data['r'] = $query->where('jdk.id',$id)->first();
            }

            $data['topik'] = Topik::findOrFail($id);

            return view('dsn.lms.topik-detail', $data);
        }

        public function lmsTopikReply(Request $r, $id)
        {

            $this->validate($r, [
                'konten' => 'required'
            ]);

            try {

                $data = new TopikJawaban;
                $data->id_topik = $id;
                $data->id_user = Sia::sessionDsn();
                $data->people = 'dsn';
                $data->konten = $r->konten;
                $data->save();

                $res = [
                    'id' => $data->id,
                    'delete_url' => route('dsn_lms_topik_reply_toggle_delete', ['id' => $data->id, 'id_topik' => $id, 'deleted' => 1, 'id_dosen' => Sia::sessionDsn()]),
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
                ->where('m.id_dosen', Sia::sessionDsn())
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
                    ->where('t.id_dosen', Sia::sessionDsn())
                    ->select('rs.*', 't.file')
                    ->first();

                if ( !empty($cek) ) {

                    DB::transaction(function()use($cek, $r){
                        Resources::find($r->id)->delete();
                        $file = config('app.lms-tugas').'/'.Sia::sessionDsn().'/'.$cek->file;
                        if ( file_exists($file) ) {
                            unlink($file);
                        }

                        // $tugas = Tugas::find($cek->id_resource)->first();

                        // if ( !empty($tugas) ) {
                        //     $path_jawaban = storage_path('upload/tugas/'.$tugas->id.'-'.$tugas->id_jadwal);
                        //     if ( file_exists($path_jawaban) ) {
                        //         unlink($path_jawaban);
                        //     }
                        // }

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
                    ->where('t.id_dosen', Sia::sessionDsn())
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

            case 'kuis':
                $cek = DB::table('lms_resources as rs')
                    ->leftJoin('lmsk_kuis as k', 'rs.id_resource', 'k.id')
                    ->where('rs.jenis', 'kuis')
                    ->where('rs.id', $r->id)
                    ->where('k.id_dosen', Sia::sessionDsn())
                    ->select('rs.*')
                    ->first();

                if ( !empty($cek) ) {

                    DB::transaction(function()use($cek, $r){
                        Resources::destroy($r->id);
                        Kuis::destroy($cek->id_resource);

                        $kuis = DB::table('lmsk_kuis_soal as ks')
                                ->leftJoin('lmsk_kuis_hasil as kh', 'ks.id', 'kh.id_kuis_soal')
                                ->where('ks.id_kuis', $cek->id_resource)
                                ->select('kh.id')
                                ->get();
                        foreach( $kuis as $val ) {
                            KuisHasil::destroy($val->id);
                        }

                        KuisSoal::where('id_kuis', $cek->id_resource)->delete();
                        TelahKuis::where('id_kuis', $cek->id_resource)->delete();
                    });

                } else {
                    Rmt::error('Tidak ada data yang bisa dihapus');
                    return redirect()->back();
                }
            break;

            case 'video':
                $cek = DB::table('lms_resources as rs')
                ->leftJoin('lms_video as v', 'rs.id_resource', 'v.id')
                ->where('rs.jenis', 'video')
                ->where('rs.id', $r->id)
                ->where('v.id_dosen', Sia::sessionDsn())
                ->select('rs.*')
                ->first();

                if ( !empty($cek) ) {
                    DB::transaction(function()use($cek, $r){
                        $vid = Video::findOrFail($cek->id_resource);
                        $path = config('app.video-files').'/'.$vid->id_dosen.'/'.$vid->file;
                        if ( file_exists($path) ) {
                            @unlink($path);
                        }

                        $vid->delete();
                        Resources::find($r->id)->delete();
                    });
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

            try {

                if ( !empty($r->cari) ) {

                    $mahasiswa = DB::table('krs_status as krs')
                        ->rightJoin('mahasiswa_reg as m2', 'krs.id_mhs_reg', '=', 'm2.id')
                        ->rightJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
                        ->join('prodi as pr', 'm2.id_prodi', 'pr.id_prodi')
                        ->select('m2.id','m2.nim','m1.nm_mhs','pr.nm_prodi', 'pr.jenjang')
                        ->whereNotNull('krs.id')
                        ->where('krs.id_smt', Sia::sessionPeriode())
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
                                                <a href="<?= route('dsn_lms_approval_mhs', [$r->id_jadwal, $mhs->id]) ?>?approv=1"
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

    /* File Manager */
        public function filemanager()
        {
            $data['file'] = BankMateri::where('id_dosen', Sia::sessionDsn())
                            ->orderBy('created_at', 'desc')
                            ->get();

            return view('dsn.lms.filemanager', $data);
        }

        public function fmStore(Request $r)
        {
            $id_dosen = Sia::sessionDsn();

            try {
                if ( $r->hasFile('file') ) {

                        $name = $r->file->getClientOriginalName();
                        $cek = BankMateri::where('file', $name)->first();

                        if ( empty($cek) ) {
                            $destinationPath = config('app.lms-materi').'/'.$id_dosen;
                            $r->file->move($destinationPath, $name);

                            $bm = new BankMateri;
                            $bm->id_dosen = $id_dosen;
                            $bm->file = $name;
                            $bm->save();
                            Rmt::success('Berhasil menyimpan data');

                        } else {
                            return Response::json('File telah ada', 422);
                        }

                } else {
                    return Response::json('Tidak ada file', 422);
                }
            } catch( \Exception $e ) {
                return Response::json($e->getMessage(), 422);
            }
        }

        public function fmDelete($id)
        {

            try {

                DB::transaction(function()use($id){

                    $bm = BankMateri::findOrFail($id);
                    $materi = Materi::where('id_bank_materi', $id)->get();

                    if ( count($materi) > 0 ) {
                        foreach( $materi as $mt ) {
                            DB::table('lms_resources')->where('id_resource', $mt->id)->delete();
                        }
                    }

                    Materi::where('id_bank_materi', $id)->delete();

                    $path_file = config('app.lms-materi').'/'.Sia::sessionDsn().'/'.$bm->file;
                    if ( file_exists($path_file) ) {
                        unlink($path_file);
                    }

                    $bm->delete();

                });

                Rmt::success('Berhasil menghapus data');
                return redirect()->back();

            } catch( \Exception $e ) {
                Rmt::error('Gagal menghapus data');
                return redirect()->back();
            }



        }

        public function fmUpdate(Request $r)
        {
            if ( empty($r->value) ) {
                return Response::json(['Judul kosong.'], 422);
            }

            try {
                $file = BankMateri::findOrFail($r->pk);
                $ekstensi = Rmt::get_file_extension($file->file);

                $file->file = $r->value.'.'.$ekstensi;
                $file->save();
                Rmt::success('Berhasil menyimpan data');
            } catch( \Exception $e ) {
                return Response::json([$e->getMessage()], 422);
            }
        }
    /* End filemanager */
}
