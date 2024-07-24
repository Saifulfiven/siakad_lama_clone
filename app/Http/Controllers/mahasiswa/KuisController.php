<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Sia, Rmt, Session, Carbon, Response;
use App\Kuis, App\KuisSoal, App\KuisHasil, App\BankSoal, App\Resources, App\JadwalKuliah, App\TelahKuis;

class KuisController extends Controller
{

    public function index(Request $r, $id_jdk, $id_kuis, $id_dosen)
    {

        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id_jdk)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jdk)->first();
        }

        $data['kuis'] = Kuis::findOrFail($id_kuis);

        // Set kuis menjadi selesai apabila waktu telah berakhir
        // Dengan set sisa_waktu = 0
        // Periksa di table lmsk_telah_kuis
        if ( !empty($data['kuis']->tgl_tutup) && Carbon::now() >= $data['kuis']->tgl_tutup ) {
            $cek_telah_kuis = DB::table('lmsk_telah_kuis')
                    ->where('id_peserta', Sia::sessionMhs())
                    ->where('id_kuis', $id_kuis)
                    ->where('sisa_waktu','<>',0)
                    ->count();

            // Jika dapat
            if ( $cek_telah_kuis > 0 ) {
                // Update tabel lmsk_telah_selesai
                DB::table('lmsk_telah_kuis')
                    ->where('id_peserta', Sia::sessionMhs())
                    ->where('id_kuis', $id_kuis)
                    ->update(['sisa_waktu' => 0]);
            }
        }

        if ( Sia::sessionMhs('prodi') == 61101 ) {

            $jenis = Sia::jenisUjianPasca($data['r']->id_smt);
        } else {
            $jenis = Sia::jenisUjian($data['r']->id_smt);
            
        }
        
        if ( $data['r']->jenis == '1' ) { 

            $data['kartu_ujian'] = DB::table('kartu_ujian')
                    ->where('id_mhs_reg', Sia::sessionMhs())
                    ->where('id_smt', Sia::sessionPeriode())
                    ->where('jenis', $jenis)
                    ->count();

        } else {
            // SP
            $data['kartu_ujian'] = 1;
        }

        $data['id_dosen'] = $id_dosen;

        return view('mahasiswa-member.lms.kuis.index', $data);
    }

    public function kerja(Request $r, $id_jdk, $id_kuis, $id_dosen)
    {
        $query = Sia::jadwalKuliah('x');

        $data['r'] = $query->where('jdk.id',$id_jdk)->first();

        if ( empty($data['r']) ) {
            // Semester pendek
            $query = Sia::jadwalKuliah('x', 2);
            $data['r'] = $query->where('jdk.id',$id_jdk)->first();
        }

        $data['kuis'] = Kuis::findOrFail($id_kuis);

        if ( Carbon::now() < $data['kuis']->tgl_mulai ) {
            dd('Waktu kuis belum saatnya');
        }

        if ( !empty($data['kuis']->tgl_tutup) && Carbon::now() >= $data['kuis']->tgl_tutup ) {
            dd('Waktu kuis telah berakhir');
        }

        if ( Sia::telahMengerjakanKuis(Sia::sessionMhs(), $data['kuis']->id) > 0 ) {
            dd('Anda telah mengerjakan kuis ini');
        }


        // Simpan telah kuis untuk mengambil sisa waktu
        $telah_kuis = TelahKuis::where('id_kuis', $id_kuis)
                    ->where('id_peserta', Sia::sessionMhs())
                    ->first();

        if ( empty($telah_kuis) ) {

            $now = Carbon::now();
            $sisa_waktu = Rmt::sisaMenit($now, $data['kuis']->tgl_tutup);
            $waktu_kerja = $data['kuis']->waktu_kerja;

            if ( $sisa_waktu < $waktu_kerja ) {
                $waktu_kerja = $sisa_waktu;
            }

            $telah = new TelahKuis;
            $telah->id_peserta = Sia::sessionMhs();
            $telah->id_kuis = $id_kuis;
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
            return redirect(route('mhs_kuis', ['id_jdk' => $id_jdk, 'id_kuis' => $id_kuis, 'id_dosen' => $id_dosen]));
        }


        // Get soal
        $soal = DB::table('lmsk_kuis_soal as ks')
                ->leftJoin('lmsk_bank_soal as bs', 'bs.id', 'ks.id_bank_soal')
                ->where('ks.id_kuis', $id_kuis)
                ->select('bs.*','ks.id as id_kuis_soal');

        if ( $data['kuis']->acak == '1' ) {
            $soal = $soal->inRandomOrder();
        } else {
            $soal = $soal->orderBy('ks.id');
        }

        $data['soal'] = $soal->get();

        $data['id_dosen'] = $id_dosen;

        $data['jumlah_soal'] = Sia::jumlahSoal($id_kuis);

        if ( $data['kuis']->tampilan == 'all' ) {
            return view('mahasiswa-member.lms.kuis.kerja', $data);
        } else {
            return view('mahasiswa-member.lms.kuis.kerja-persoal', $data);
        }

    }

    public function updateWaktu(Request $r)
    {
        $data = TelahKuis::find($r->id);
        $data->sisa_waktu = $r->waktu < 0 ? 0 : $r->waktu;
        $data->save();
    }

    public function store(Request $r)
    {
        if ( count($r->jawaban) == 0 ) {
            return Response::json(['Anda belum mengerjakan satupun'], 422);
        }

        $nilai_per_soal = 100 / $r->jumlah_soal;

        try {

            DB::transaction(function()use($r, $nilai_per_soal){

                $id_mhs_reg = Sia::sessionMhs();
                if ( empty($id_mhs_reg) ) {
                    $id_mhs_reg = $r->id_peserta;
                }

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
            return Response::json([$e->getMessage().'. COBA ULANGI LAGI..'], 422);
        }

    }

    public function storeSingle(Request $r)
    {

        $nilai_per_soal = 100 / $r->jml_soal;

        try {

            $id_mhs_reg = Sia::sessionMhs();

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
            return Response::json([$e->getMessage().'. COBA ULANGI LAGI..'], 422);
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
}