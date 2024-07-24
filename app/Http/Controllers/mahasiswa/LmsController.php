<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sia, Rmt, DB, Response, Auth, Session, File, Carbon;
use App\Tugas, App\JawabanTugas, App\Topik, App\TopikJawaban, App\PesertaUndangan, App\Video;

class LmsController extends Controller
{

    public function index(Request $r)
    {
        if ( !Session::has('smt_in_lms') ) {
            Session::put('smt_in_lms', Sia::sessionPeriode());
        }

        $id_reg_pd = Sia::sessionMhs('id_mhs_reg');

        if ( $r->smt ) {
            Session::put('smt_in_lms', $r->smt);
        }

        if ( !empty($r->ubah_jenis) ) {
            Session::put('jeniskrs_in_lms', $r->ubah_jenis);
        } else {
            Session::put('jeniskrs_in_lms', 1);
        }

        $data['semester'] = DB::table('semester')
                                ->whereBetween('id_smt', [Sia::sessionMhs('smt_mulai'), Sia::sessionPeriode()])
                                ->orderBy('id_smt','desc')->get();

        $query = Sia::jadwalKuliahMahasiswa($id_reg_pd, 1);
        $data['jadwal'] = $query->where('jdk.id_smt', Session::get('smt_in_lms'))
                            ->where('jdk.hari', '<>', 0)
                            ->get();

        $query2 = Sia::jadwalKuliahMahasiswa($id_reg_pd, 2);
        $data['jadwal_sp'] = $query2->where('jdk.id_smt', Session::get('smt_in_lms'))
                            ->where('jdk.hari', '<>', 0)
                            ->get();


        return view('mahasiswa-member.lms.index', $data);
    }

    public function detail($id_jdk, $id_dosen)
    {

        $jenis = Session::get('jeniskrs_in_lms');
        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id_jdk)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jdk)->first();
        }

        // $data['peserta'] = Sia::pesertaKelas($id_jdk)->toArray();
        
        $data['id_dosen'] = $id_dosen;

        $jml_pertemuan = Sia::jmlPertemuan($id_jdk);

        $jml_dosen = DB::table('dosen_mengajar')->where('id_jdk', $id_jdk)->count();

        if ( $jml_dosen == 1 && $data['r']->id_prodi == 61101 ) {
            $jml_pertemuan = $jml_pertemuan + 6;
        }

        $id_dosen_arr = [];
        if ( $jml_dosen == 2 ) {
            $dosen = DB::table('dosen_mengajar')->where('id_jdk', $id_jdk)->get()->toArray();
            $id_dosen_arr = [$dosen[0]->id_dosen, $dosen[1]->id_dosen];
        }
        $data['id_dosen_arr'] = $id_dosen_arr;

        // $data['jml_pertemuan'] = $jml_pertemuan == 14 ? $jml_pertemuan + 2 : $jml_pertemuan;
        $data['jml_pertemuan'] = $jml_pertemuan + 2;

        return view('mahasiswa-member.lms.detail', $data);
    }

    public function materiView(Request $r, $id_materi, $id_dosen)
    {

        $materi = DB::table('lms_materi as m')
                ->leftJoin('lms_bank_materi as bm', 'm.id_bank_materi', 'bm.id')
                ->where('m.id', $id_materi)
                ->where('m.id_dosen', $id_dosen)
                ->select('m.*','bm.file')
                ->first();

        // dd($materi);
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

    public function materiPascaDownload(Request $r, $id, $file)
    {

        $materi = DB::table('materi_kuliah_pasca')
                ->where('id', $id)
                ->first();

        if ( !empty($materi) ) {
            
            $file_materi = !empty($materi->file_materi) ? $materi->file_materi : 'undefined';
            $path = config('app.materi-pasca');
            $file = $path.'/'.trim($materi->kode_mk).'/'.$file_materi;
            
            if ( file_exists($file) ) {
                return Response::file($file);
            } else {
                echo "<center><h4>File tidak ditemukan</h4></center>";
            }

        } else {
            echo 'Tidak ada data';
        }
    }

    public function tugasDetail(Request $r, $id_jadwal, $id, $id_dosen)
    {
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id_jadwal)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jadwal)->first();
        }

        $data['tugas'] = Tugas::findOrFail($id);

        if ( $data['r']->id_prodi == '61101' ) {
            $jenis = Sia::jenisUjianPasca($data['r']->id_smt);
        } else {
            $jenis = Sia::jenisUjian($data['r']->id_smt);
        }

        if ( $r->debug ) {
            echo nl2br($data['tugas']->deskripsi);
            exit;
        }

        if ( $data['r']->jenis == '1' ) { 

            $data['kartu_ujian'] = DB::table('kartu_ujian')
                    ->where('id_mhs_reg', Sia::sessionMhs())
                    ->where('id_smt', Sia::sessionPeriode())
                    ->where('jenis', $jenis)
                    ->count();

        } else {
            $data['kartu_ujian'] = 1;
        }

        $data['id_dosen'] = $id_dosen;

        return view('mahasiswa-member.lms.tugas-detail', $data);
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
        try {

            DB::beginTransaction();

            $tgs = Tugas::findOrFail($r->id_tugas);
            $nim = Sia::sessionMhs('nim');
            $nama = trim(Sia::sessionMhs('nama'));

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
            $data->id_peserta = Sia::sessionMhs();
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

            DB::commit();

        } catch( \Exception $e ) {

            DB::rollback();
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

            DB::beginTransaction();

            // $tgs = Tugas::findOrFail($r->id_tugas);
            $nim = Sia::sessionMhs('nim');
            $nama = trim(Sia::sessionMhs('nama'));

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

            $data->id_peserta = Sia::sessionMhs();
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

            DB::commit();

        } catch( \Exception $e ) {
            DB::rollback();
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function tugasDownload(Request $r, $id_tugas, $file)
    {
        $nim = Sia::sessionMhs('nim');
        $nama = trim(Sia::sessionMhs('nama'));
        $pathToFile = config('app.lms-tugas').'/'.$id_tugas.'-'.$r->jdk.'/'.$nim.'-'.$nama.'/'.$file;
        
        return Response::download($pathToFile);
    }


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
                    <a href="<?= route('mhs_lms_topik_detail', ['id' => $val->id, 'jdw' => $r->id_jadwal]) ?>">
                        <h4 class="font-bold text-gray-900">
                            <?= $val->judul ?>
                            <span class="pull-right">
                                <?php $jawaban =  $val->jawaban()->count(); ?>
                                <i class="fa fa-comment text-gray-600"></i>
                                <?= $jawaban ?>
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
                        <a class="text-green-darker mr-2 font-bold">
                            <?php if ( $val->creator == $id_dosen ) { ?>
                                Dosen Matakuliah
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
                            <?php if ( Sia::sessionMhs() == $val->creator ) { ?>

                                <?php if ( $val->is_closed == 1 ) { ?>
                                    <a style="color: #0aa699"><i class="fa fa-check"></i> Topik ini telah selesai</a> &nbsp;
                                <?php } else { ?>
                                    <a href='javascript:;' onclick="ubahTopik('<?= $val->id ?>')" class='btn btn-default btn-xs'><i class='fa fa-pencil'></i> Ubah</i></a>
                                <?php } ?>

                                <a href="<?= route('mhs_lms_topik_delete', ['id' => $val->id, 'id_dosen' => $id_dosen]) ?>" onclick="return confirm('Anda ingin menghapus topik ini.?')" class='btn btn-danger btn-xs'><i class='fa fa-times'></i> Hapus</i></a>

                            <?php } else { ?>
                                <?php if ( $val->is_closed == 1 ) { ?>
                                    <a style="color: #0aa699"><i class="fa fa-check"></i> Topik ini telah selesai</a> &nbsp;
                                <?php } ?>
                            <?php } ?>
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
                $data->id_dosen = $r->id_dosen;
                $data->creator = Sia::sessionMhs();
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
                $data['r'] = $query->where('jdk.id',$r->jdw)->first();
            }

            $data['topik'] = Topik::findOrFail($id);

            return view('mahasiswa-member.lms.topik-detail', $data);
        }

        public function lmsTopikReply(Request $r, $id)
        {

            $this->validate($r, [
                'konten' => 'required'
            ]);

            try {

                $data = new TopikJawaban;
                $data->id_topik = $id;
                $data->id_user = Sia::sessionMhs();
                $data->people = 'mhs';
                $data->konten = $r->konten;
                $data->save(); 

                $res = [
                    'id' => $data->id,
                    'delete_url' => route('mhs_lms_topik_reply_toggle_delete', ['id' => $data->id, 'id_topik' => $id, 'deleted' => 1, 'id_dosen' => $r->id_dosen]),
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

    public function getJadwal(Request $r)
    {
        $query = Sia::jadwalKuliah('x');

        try {

            if ( !empty($r->cari) ) {

                $query->where('jdk.id_smt', Sia::sessionPeriode());
                $query->whereNotIn('jdk.id', explode(',', $r->jadwalku));

                $query->where(function($q)use($r){
                    $q->where('mk.kode_mk', 'like', '%'.$r->cari.'%')
                        ->orWhere('mk.nm_mk', 'like', '%'.$r->cari.'%');
                });

                $jadwal = $query->take(10)->get();

                if ( count($jadwal) > 0 ) { ?>

                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>Hari, Jam</th>
                            <th>Matakuliah</th>
                            <th>Prodi</th>
                            <th>Dosen</th>
                            <th>Aksi</th>
                        </tr>
                        <?php foreach( $jadwal as $jdk ) { 

                            $cek = PesertaUndangan::where('id_jadwal', $jdk->id)->first(); ?>

                            <tr>
                                <td align="center">
                                    <?= empty($jdk->hari) ? '-': Rmt::hari($jdk->hari) ?><br>
                                    <?= substr($jdk->jam_masuk,0,5) ?> - <?= substr($jdk->jam_keluar,0,5) ?>
                                </td>
                                <td align="center">
                                    <?= $jdk->kode_mk ?> <br>
                                    <?= $jdk->nm_mk ?>
                                </td>
                                <td><?= $jdk->nm_prodi ?> (<?= $jdk->jenjang ?>)</td>
                                <td align="left"><?= $jdk->dosen ?></td>
                                <td>
                                    <?php 
                                    if ( !empty($cek) ) {
                                        if ( $cek->aktif == '1' ) {
                                            echo 'Telah bergabung';
                                        } else {
                                            echo 'Permintaan terkirim';
                                        }
                                    } else { ?>
                                        <a href="javascript:;" class="jdk-<?= $jdk->id ?> btn btn-primary btn-xs" onclick="gabung('<?= $jdk->id ?>')">Minta Bergabung</a>
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

    public function gabung(Request $r, $id_jadwal)
    {
        try {

            $data = new PesertaUndangan;
            $data->id_peserta = Sia::sessionMhs();
            $data->id_jadwal = $id_jadwal;
            $data->save();

            Rmt::success('Berhasil mengirim permintaan, tunggu hingga dosen menyetujui permintaan anda untuk bergabung.');

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function batalGabung($id_peserta, $id_jadwal)
    {
        PesertaUndangan::where('id_jadwal', $id_jadwal)
            ->where('id_peserta', $id_peserta)
            ->delete();

        Rmt::success('Berhasil menghapus permintaan');

        return redirect()->back();
    }

    public function video(Request $r, $id_jdk, $id_video, $id_dosen)
    {

        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id_jdk)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jdk)->first();
        }

        $data['video'] = Video::findOrFail($id_video);

        $data['id_dosen'] = $id_dosen;

        return view('mahasiswa-member.lms.video.index', $data);

    }

    public function videoUpdateKetersediaan(Request $r)
    {
        $video = Video::find($r->id_video);
        $video->siap = 'y';
        $video->save();
    }
}