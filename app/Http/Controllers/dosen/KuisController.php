<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Sia, Rmt, Session, Carbon, Response;
use App\Kuis, App\KuisSoal, App\KuisHasil, App\BankSoal, App\Resources, App\JadwalKuliah, App\TelahKuis;

class KuisController extends Controller
{

    public function add(Request $r, $id)
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
        
        return view('dsn.lms.kuis.kuis-add', $data);
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

    public function store(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required',
            'tgl_mulai' => 'required',
            'tgl_berakhir' => 'required',
            'waktu' => 'numeric',
        ]);

        try {

            $tgl_mulai = !empty($r->mulai_berlaku) ? Carbon::parse($r->mulai_berlaku) : '';
            $tgl_berakhir = !empty($r->tgl_berakhir) ? Carbon::parse($r->tgl_berakhir) : '';

            $errors = [];

            if ( !empty($tgl_mulai) && !empty($tgl_berakhir) ) {
                if ( $tgl_mulai->greaterThanOrEqualTo($tgl_berakhir) ) {
                    $errors[] = 'Tanggal mulai harus lebih kecil dari tgl berakhir.';
                }
            }

            if ( empty($r->waktu) ) {
                $errors[] = 'Waktu pengerjaan harus diisi.';
            }

            if ( count($errors) > 0 ) {
                return Response::json($errors, 422);
            }


            $id_dosen = Sia::sessionDsn();

            $kuis = new Kuis;
            $kuis->id_jadwal = $r->id_jadwal;
            $kuis->id_dosen = $id_dosen;
            $kuis->judul = $r->judul;
            $kuis->tgl_mulai = empty($r->tgl_mulai) ? null : Carbon::parse($r->tgl_mulai)->format('Y-m-d H:i');
            $kuis->tgl_tutup = empty($r->tgl_berakhir) ? null : Carbon::parse($r->tgl_berakhir)->format('Y-m-d H:i');
            $kuis->ket = $r->ket;
            $kuis->waktu_kerja = $r->waktu;
            $kuis->jenis = $r->jenis;
            $kuis->tampilan = $r->tampilan;
            $kuis->acak = $r->acak;
            $kuis->save();
            $id_kuis = $kuis->id;


            $urutan = Resources::where('id_jadwal', $r->id_jadwal)
                        ->where('pertemuan_ke', $r->pertemuan)
                        ->max('urutan');
            $urutan = empty($urutan) ? 1 : $urutan + 1; 

            $res = new Resources;
            $res->id_jadwal = $r->id_jadwal;
            $res->id_resource = $id_kuis;
            $res->jenis = 'kuis';
            $res->pertemuan_ke = $r->pertemuan;
            $res->urutan = $urutan;
            $res->save();

            Session::flash('kuis',1);

            return Response::json(['error' => 0, 'id' => $id_kuis]);

        } catch(\Exception $e){
            return Response::json([$e->getMessage()], 422);
        }

    }

    public function addSoal(Request $r, $id, $id_kuis)
    {
        $data['kuis'] = Kuis::findOrFail($id_kuis);
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id)->first();
        }

        return view('dsn.lms.kuis.add-soal', $data);
    }

    public function soalStore(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required',
            'soal' => 'required'
        ]);

        try {

            if ( $r->jenis_soal == 'pg' && empty($r->kunci_jawaban) ) {
                return Response::json(['Kunci jawaban harus diisi'], 422);
            }

            if ( $r->jenis_soal == 'pg' && empty($r->jawaban_a) ) {
                return Response::json(['Isikan Jawaban Pilihan 1'], 422);
            }

            if ( $r->jenis_soal == 'pg' && empty($r->jawaban_b) ) {
                return Response::json(['Isikan Jawaban Pilihan 2'], 422);
            }

            $bank = new BankSoal;
            $bank->judul = $r->judul;
            $bank->id_dosen = Sia::sessionDsn();
            $bank->kode_mk = $r->kode_mk;
            $bank->jenis_soal = $r->jenis_soal;
            $bank->soal = $r->soal;

            if ( $r->jenis_soal == 'pg' ) {
                $bank->jawaban_a = $r->jawaban_a;
                $bank->jawaban_b = $r->jawaban_b;
                $bank->jawaban_c = $r->jawaban_c;
                $bank->jawaban_d = $r->jawaban_d;
                $bank->jawaban_e = $r->jawaban_e;
                $bank->jawaban_benar = $r->kunci_jawaban;
            } else {
                $bank->keyword = $r->keyword;
            }

            $bank->save();
            $id_bank_soal = $bank->id;

            $soal = new KuisSoal;
            $soal->id_kuis = $r->id_kuis;
            $soal->id_bank_soal = $id_bank_soal;
            $soal->save();

            Rmt::success('Berhasil menyimpan soal');

        } catch( \Exception $e ) {
            return Response([$e->getMessage()], 422);
        }
    }

    public function editSoal(Request $r, $id, $id_kuis, $id_soal)
    {
        $data['kuis'] = Kuis::findOrFail($id_kuis);
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id)->first();
        }

        $data['soal'] = BankSoal::findOrFail($id_soal);
        $data['id_soal'] = $id_soal; 

        return view('dsn.lms.kuis.edit-soal', $data);
    }

    public function updateSoal(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required',
            'soal' => 'required'
        ]);

        try {

            if ( $r->jenis_soal == 'pg' && empty($r->kunci_jawaban) ) {
                return Response::json(['Kunci jawaban harus diisi'], 422);
            }

            if ( $r->jenis_soal == 'pg' && empty($r->jawaban_a) ) {
                return Response::json(['Isikan Jawaban Pilihan 1'], 422);
            }

            if ( $r->jenis_soal == 'pg' && empty($r->jawaban_b) ) {
                return Response::json(['Isikan Jawaban Pilihan 2'], 422);
            }

            // Cek apakah soal ada pada kuis lain
            $cek_1 = KuisSoal::where('id_bank_soal', $r->id_bank_soal)
                    ->where('id_kuis','<>', $r->id_kuis)->count();

            if ( $cek_1 != 0 ) {
                // create new soal
                $bank = new BankSoal;
                $bank->judul = $r->judul;
                $bank->id_dosen = Sia::sessionDsn();
                $bank->kode_mk = $r->kode_mk;
                $bank->jenis_soal = $r->jenis_soal;
                $bank->soal = $r->soal;

                if ( $r->jenis_soal == 'pg' ) {
                    $bank->jawaban_a = $r->jawaban_a;
                    $bank->jawaban_b = $r->jawaban_b;
                    $bank->jawaban_c = $r->jawaban_c;
                    $bank->jawaban_d = $r->jawaban_d;
                    $bank->jawaban_e = $r->jawaban_e;
                    $bank->jawaban_benar = $r->kunci_jawaban;
                } else {
                    $bank->keyword = $r->keyword;
                }

                $bank->save();
                $id_bank_soal = $bank->id;

                $soal = KuisSoal::find($r->id_kuis_soal);
                $soal->id_bank_soal = $id_bank_soal;
                $soal->save();

                Rmt::success('Berhasil menyimpan soal');
                return Response::json(['error' => 0, 'id_soal' => $id_bank_soal]);

            } else {
                // Update bank soal
                $bank = BankSoal::find($r->id_bank_soal);
                $bank->judul = $r->judul;
                $bank->soal = $r->soal;

                if ( $r->jenis_soal == 'pg' ) {
                    $bank->jawaban_a = $r->jawaban_a;
                    $bank->jawaban_b = $r->jawaban_b;
                    $bank->jawaban_c = $r->jawaban_c;
                    $bank->jawaban_d = $r->jawaban_d;
                    $bank->jawaban_e = $r->jawaban_e;
                    $bank->jawaban_benar = $r->kunci_jawaban;
                } else {
                    $bank->keyword = $r->keyword;
                }

                $bank->save();
            }

            Rmt::success('Berhasil menyimpan soal');
            return Response::json(['error' => 0, 'id_soal' => $r->id_bank_soal]);


        } catch( \Exception $e ) {
            return Response([$e->getMessage()], 422);
        }
    }

    public function deleteSoal($id_kuis_soal, $id_jadwal)
    {
        $cek = DB::table('jadwal_kuliah')->where('id', $id_jadwal)->count();

        if ( $cek > 0 ) {
            KuisHasil::where('id_kuis_soal', $id_kuis_soal)->delete();
            KuisSoal::where('id', $id_kuis_soal)->delete();
            Rmt::success('Berhasil menghapus soal');
        } else {
            return Response::json(['Not Found'], 404);
        }

        return redirect()->back();
    }

    public function detail(Request $r, $id_jadwal, $id)
    {

        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id_jadwal)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jadwal)->first();
        }

        $data['kuis'] = Kuis::findOrFail($id);

        // Jika kuis tertutup update sisa_waktu = 0 in lmsk_telah_kuis
        // Periksa di table lmsk_telah_kuis
        if ( !empty($data['kuis']->tgl_tutup) && Carbon::now() >= $data['kuis']->tgl_tutup ) {
            $cek_telah_kuis = DB::table('lmsk_telah_kuis')
                    ->where('id_kuis', $id)
                    ->where('sisa_waktu','<>',0)
                    ->count();

            // Jika dapat
            if ( $cek_telah_kuis > 0 ) {
                // Update tabel lmsk_telah_selesai
                DB::table('lmsk_telah_kuis')
                    ->where('id_kuis', $id)
                    ->update(['sisa_waktu' => 0]);
            }
        }

        $data['soal'] = DB::table('lmsk_kuis_soal as ks')
                ->leftJoin('lmsk_bank_soal as bs', 'bs.id', 'ks.id_bank_soal')
                ->where('ks.id_kuis', $id)
                ->select('bs.*','ks.id as id_kuis_soal')
                ->get();

        return view('dsn.lms.kuis.detail', $data);
    }

    public function bankSoal(Request $r)
    {
        $data = BankSoal::where('id_dosen', Sia::sessionDsn())
                ->where('kode_mk', $r->kode_mk)
                ->get();

        $no = 1; ?>

        <table class="table table-bordered table-hover" id="data-table">
            <thead class="custom">
                <th>No</th>
                <th>Judul</th>
                <th>Jenis Soal</th>
                <th>Aksi</th>
            </thead>
            <tbody align="center">
                <?php foreach( $data as $val ) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td align="left"><?= $val->judul ?></td>
                        <td><?= $val->jenis_soal == 'pg' ? 'Pilihan Ganda' : 'Essay' ?></td>
                        <td>
                            <a href="<?= route('kuis_ambil_soal', ['id_soal' => Rmt::engkripAngka($val->id), 'id_kuis' => $r->id_kuis]) ?>" class="btn btn-primary btn-xs ambil">AMBIL</a> 
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <hr>
        <a href="javascript:;" class="btn btn-danger btn-sm pull-right" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</a>
        <br>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/dataTables.bootstrap.js"></script>
        <script>
            $(function(){
                $('#data-table').dataTable({
                    "order": [[ 0, 'asc' ]]
                });

                $('.ambil').click(function(){
                    $(this).html('<i class="fa fa-spin fa-spinner"></i>');
                    $('.ambil').attr('disabled','');
                });
            });
        </script>
        <?php
    }

    public function ambilSoal($id_soal, $id_kuis)
    {
        // Cek apakah soal sudah ada
        $cek = KuisSoal::where('id_kuis', $id_kuis)
                ->where('id_bank_soal', Rmt::dekripAngka($id_soal))
                ->count();

        if ( $cek > 0 ) {
            Rmt::error('GAGAL..! Soal ini telah ada');
            return redirect()->back();
        }

        $data = new KuisSoal;
        $data->id_kuis = $id_kuis;
        $data->id_bank_soal = Rmt::dekripAngka($id_soal);
        $data->save();

        Rmt::success('Berhasil menambah soal');
        return redirect()->back();
    }

    public function jawaban(Request $r, $id_jadwal, $id)
    {
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id_jadwal)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jadwal)->first();
        }

        $data['kuis'] = Kuis::findOrFail($id);

        $peserta_undangan = DB::table('lms_peserta_undangan as pu')
                    ->leftJoin('mahasiswa_reg as m1', 'pu.id_peserta', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
                    ->select('m1.id as id_mhs_reg', 'm1.nim', 'm2.nm_mhs')
                    ->where('pu.aktif',1)
                    ->where('pu.id_jadwal', $id_jadwal);

        $data['peserta_kelas'] = DB::table('nilai as n')
                    ->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
                    ->leftJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
                    ->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
                    ->select('n.id_mhs_reg', 'm2.nim', 'm1.nm_mhs')
                    ->where('n.id_jdk', $id_jadwal)
                    ->union($peserta_undangan)
                    ->orderBy('nim')
                    ->get();

        return view('dsn.lms.kuis.jawaban', $data);
    }

    public function jawabanDetail(Request $r, $id_jadwal, $id, $id_peserta)
    {
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id_jadwal)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jadwal)->first();
        }

        $data['kuis'] = Kuis::findOrFail($id);

        $data['peserta'] = DB::table('mahasiswa_reg as m2')
                    ->leftJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
                    ->select('m2.id as id_mhs_reg', 'm2.nim', 'm1.nm_mhs')
                    ->where('m2.id', $id_peserta)
                    ->first();

        $data['soal'] = DB::table('lmsk_kuis_soal as ks')
                ->leftJoin('lmsk_bank_soal as bs', 'bs.id', 'ks.id_bank_soal')
                ->where('ks.id_kuis', $id)
                ->select('bs.*','ks.id as id_kuis_soal')
                ->get();


        return view('dsn.lms.kuis.jawaban-detail', $data);
    }


    public function edit(Request $r, $id, $id_kuis)
    {
        $data['kuis'] = Kuis::findOrFail($id_kuis);
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id)->first();
        }

        return view('dsn.lms.kuis.kuis-edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required',
            'tgl_mulai' => 'required',
            'tgl_berakhir' => 'required',
            'waktu' => 'numeric',
        ]);

        try {

            $tgl_mulai = !empty($r->mulai_berlaku) ? Carbon::parse($r->mulai_berlaku) : '';
            $tgl_berakhir = !empty($r->tgl_berakhir) ? Carbon::parse($r->tgl_berakhir) : '';

            $errors = [];

            if ( !empty($tgl_mulai) && !empty($tgl_berakhir) ) {
                if ( $tgl_mulai->greaterThanOrEqualTo($tgl_berakhir) ) {
                    $errors[] = 'Tanggal mulai harus lebih kecil dari tgl berakhir.';
                }
            }

            if ( empty($r->waktu) ) {
                $errors[] = 'Waktu pengerjaan harus diisi.';
            }

            if ( count($errors) > 0 ) {
                return Response::json($errors, 422);
            }


            $kuis = Kuis::find($r->id_kuis);
            $kuis->judul = $r->judul;
            $kuis->tgl_mulai = empty($r->tgl_mulai) ? null : Carbon::parse($r->tgl_mulai)->format('Y-m-d H:i');
            $kuis->tgl_tutup = empty($r->tgl_berakhir) ? null : Carbon::parse($r->tgl_berakhir)->format('Y-m-d H:i');
            $kuis->waktu_kerja = $r->waktu;
            $kuis->ket = $r->ket;
            $kuis->jenis = $r->jenis;
            $kuis->tampilan = $r->tampilan;
            $kuis->acak = $r->acak;
            $kuis->save();
            $id_kuis = $kuis->id;

            Rmt::success('Berhasil menyimpan kuis');
            return Response::json(['error' => 0, 'id' => $id_kuis]);

        } catch(\Exception $e){
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function grade(Request $r, $id_jadwal, $id_peserta)
    {
        try {

            // Pastikan nilai pada jadwal valid
            JadwalKuliah::findOrFail($id_jadwal);

            if ( ( (int)$r->value < 1 || (int)$r->value > 100 ) && $r->value != '' ) {
                return Response::json(['Penilaian hanya boleh pada rentang 1 - 100.'], 422);
            }
            
            // $cek = KuisHasil::where('id_kuis_soal', $r->pk)
            //         ->where('id_peserta', $id_peserta)->first();

            // if ( !empty($cek) ) {
            //     $cek->penilaian = number_format($r->value, 2);
            //     $cek->save();
            // } else {
            //     $data = new KuisHasil;
            //     $data->id_peserta = $id_peserta;
            //     $data->id_kuis_soal = $r->pk ;
            //     $data->penilaian = number_format($r->value, 2);
            //     $data->save();
            // }

            $data = KuisHasil::updateOrCreate(
            [
                'id_kuis_soal' => $r->pk,
                'id_peserta' => $id_peserta
            ],
            [
                'penilaian' => number_format($r->value, 2)
            ]);


        } catch(\Exception $e) {
            return Response::json([$e->getMessage()], 422);
        }
    }
}