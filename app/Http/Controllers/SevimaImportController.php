<?php

namespace App\Http\Controllers;

use Sia;
use App\Exports\ProdiExport;
use App\JadwalKuliah;
use App\Jamkuliah;
use App\Kelas;
use App\Kurikulum;
use App\Matakuliah;
use App\MatakuliahKurikulum;
use App\Models\Prodi;
use App\Mahasiswareg;
use App\Mahasiswa;
use App\Nilai;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Rmt;
use Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


// TABLE
// 1.  KELAS (DONE)
// 2.  KRS (ON PROGRESS)
// 3.  KURIKULUM MK (DONE)
// 4.  LULUSAN (DONE)
// 5.  MAHASISWA (DONE)
// 6.  NILAI PINDAHAN (DONE)
// 7.  PEGAWAI
// 8.  PERWALIAN
// 9.  PROPORSI NILAI (DONE)
// 11. SKRIPSI (DONE)
// 15. YUDISIUM (DONE)

// BUGS
// MK KURIKULUM
// NILAI
// MHS_REG PASSWORD


//delete from `kelas` WHERE id_kelas_sevima is not null;
//delete from `jam_kuliah` WHERE id_kelas_sevima is not null;
//delete from `jadwal_kuliah` WHERE id_kelas_sevima is not null;
//delete from `jadwal_kuliah` where id_smt = 20231;
//delete from `krs_mhs` WHERE id_krs_sevima is not null;
//delete from `nilai` WHERE date(created_at) = date('2024-06-08');

// hati2 dalam mengimport matakuliah, kurikulum dan mk kurikulum nanti double
// dan berpengaruh ke nilai,krs, dan jadwal kuliah
class SevimaImportController extends Controller
{
    use MahasiswaDokumen;

    public function deleteMahasiswa()
    {
        // GET ALL MAHASISWA_REG KURIKULUM 2022 >
        $arrayTahun = [20231, 20232, 20233];
        $mahasiswaReg = Mahasiswareg::whereIn('semester_mulai', $arrayTahun)->get();
        $mahasiswaIDS = $mahasiswaReg->pluck('id_mhs');
        $mahasiswaRegIDS = $mahasiswaReg->pluck('id');

        $mahasiswaSevima = DB::connection('sevima')
            ->table('mahasiswa')
            ->where('periode_masuk', 'like', '%2023%')
            ->get();

        // DELETE ALL RELATED TABLE
        // GET ALL MAHASISWA BASED ON MAHASISWA_REG ID
        $mahasiswa = Mahasiswa::whereIn('id', $mahasiswaIDS)->get();
        $mahasiswaSevimas = $mahasiswaSevima->pluck('email');
//        Log::error(json_encode($mahasiswaSevima));
        $user =DB::table('users')->whereIn('email', $mahasiswaSevimas)->delete();
        // NILAI
        $nilai = DB::table('nilai')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // NILAI MBKM
        $nilaiMbkm = DB::table('nilai_mbkm')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // NILAI TRANSFER
        $nilaiTransfer = DB::table('nilai_transfer')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // PEMBAYARAN
        $pembayaran = DB::table('pembayaran')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // PEMBAYARAN DELETED
        $pembayaranDeleted = DB::table('pembayaran_deleted')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // PENDAFTARAN IJAZAH
        $pendaftaran = DB::table('pendaftaran_ijazah')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // PENGUJI
        $penguji = DB::table('penguji')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // PESERTA UJIAN
        $pesertaUjian = DB::table('peserta_ujian')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // PKM PESERTA
        $pkmPeserta = DB::table('pkm_peserta')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // POTONGAN BIAYA KULIAH
        $potonganBiaya = DB::table('potongan_biaya_kuliah')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        // UJIAN AKHIR
        $ujianAkhir = DB::table('ujian_akhir')->whereIn('id_mhs_reg', $mahasiswaRegIDS)->delete();
        Mahasiswareg::whereIn('semester_mulai', $arrayTahun)->delete();
        return 'berhasil delete';
//        return [
//            'mahasiswa' => $mahasiswa,
//            'nilai' => $nilai,
//            'nilai_mbkm' => $nilaiMbkm,
//            'nilai_transfer' => $nilaiTransfer,
//            'pembayaran' => $pembayaran,
//            'pembayaran_deleted' => $pembayaranDeleted,
//            'pendaftaran_ijazah' => $pendaftaran,
//            'penguji' => $penguji,
//            'peserta_ujian' => $pesertaUjian,
//            'pkm_peserta' => $pkmPeserta,
//            'potongan_biaya_kuliah' => $potonganBiaya,
//            'ujian_akhir' => $ujianAkhir
//        ];
    }

    //
    public function mahasiswa()
    {
        // generate mahasiswa
        $mahasiswaSevima = DB::connection('sevima')
            ->table('mahasiswa')
            ->where('periode_masuk', 'like', '%2023%')
            ->get();

        $users = [];
        $mahasiswas = [];
        $mahasiswaRegs = [];

        foreach ($mahasiswaSevima as $item) {
            $idUser = Rmt::uuid();
            $idMHS = Rmt::uuid();
            $tanggalLahir = Carbon::createFromFormat('d/m/Y', $item->tanggal_lahir);
            $email = $item->email;
            if ($item->email === "" || $item->email === null) {
                $email = $item->nim . "@nobel.ac.id";
            }

            $users[] = [
                'id' => $idUser,
                'nama' => $item->nama,
                'username' => $item->nim,
                'email' => $email,
                'password' => $tanggalLahir->format('dmY'),
                'level' => 'mahasiswa',
                'created_at' => Carbon::now()->format('Y-m-d'),
            ];

            $agama = DB::table('agama')->where('nm_agama', 'like', '%' . $item->agama . '%')->first();
            // NO DATA FROM SEVIMA
            // $wilayah = DB::table('wilayah')->where('nm_wil','like','%'.$item->wilayah.'%')->first();

            $pendidikanAyah = DB::table('pendidikan')->where('nm_pdk', 'like', '%' . $item->pendidikan_ayah . '%')->first();
            $pendidikanIbu = DB::table('pendidikan')->where('nm_pdk', 'like', '%' . $item->pendidikan_ibu . '%')->first();
            $pendidikanWali = DB::table('pendidikan')->where('nm_pdk', 'like', '%' . $item->pendidikan_wali . '%')->first();

            $pekerjaanAyah = DB::table('pekerjaan')->where('nm_pekerjaan', 'like', '%' . $item->pekerjaan_ayah . '%')->first();
            $pekerjaanIbu = DB::table('pekerjaan')->where('nm_pekerjaan', 'like', '%' . $item->pekerjaan_ibu . '%')->first();
            $pekerjaanWali = DB::table('pekerjaan')->where('nm_pekerjaan', 'like', '%' . $item->pekerjaan_wali . '%')->first();

            $penghasilanAyah = DB::table('penghasilan')->where('nm_penghasilan', 'like', '%' . $item->penghasilan_ayah . '%')->first();
            $penghasilanIbu = DB::table('penghasilan')->where('nm_penghasilan', 'like', '%' . $item->penghasilan_ibu . '%')->first();
            $penghasilanWali = DB::table('penghasilan')->where('nm_penghasilan', 'like', '%' . $item->penghasilan_wali . '%')->first();
            $mahasiswas[] = [
                'id' => $idMHS,
                'id_user' => $idUser,
                'nm_mhs' => trim($item->nama),
                'jenkel' => trim($item->jenis_kelamin),
                'nik' => trim($item->nik),
                'nisn' => trim($item->nim_asal),
                'npwp' => @trim($item->npwp),
                'tempat_lahir' => trim($item->tempat_lahir),
                'tgl_lahir' => $tanggalLahir,
                'id_agama' => isset($agama->id_agama) ? $agama->id_agama : "99",
                'alamat' => trim($item->alamat),
                'dusun' => trim($item->dusun),
                'des_kel' => trim($item->desa_kelurahan),
                'rt' => trim($item->rt),
                'rw' => trim($item->rw),
                'id_wil' => "000000",
                'pos' => trim($item->kode_pos),
                'hp' => trim($item->hp),
                'email' => $email,
                'kewarganegaraan' => trim($item->kewarganegaraan),
                'nm_sekolah' => trim($item->universitas_awal),
                'tahun_lulus_sekolah' => @trim($item->tahun_lulus_sekolah),
                'nik_ibu' => null,
                'nm_ibu' => trim($item->nama_ibu),
                'tgl_lahir_ibu' => $item->tanggal_lahir_ibu,
                'id_pdk_ibu' => isset($pendidikanIbu->id_pdk) ? $pendidikanIbu->id_pdk : 99,
                'id_pekerjaan_ibu' => isset($pekerjaanIbu->id_pekerjaan) ? $pekerjaanIbu->id_pekerjaan : 99,
                'id_penghasilan_ibu' => isset($penghasilanIbu->id_penghasilan) ? $penghasilanIbu->id_penghasilan : 11,
                'hp_ibu' => trim($item->telepon_ibu),
                'nik_ayah' => null,
                'nm_ayah' => trim($item->nama_ayah),
                'tgl_lahir_ayah' => $item->tanggal_lahir_ayah,
                'id_pdk_ayah' => isset($pendidikanAyah->id_pdk) ? $pendidikanAyah->id_pdk : 99,
                'id_pekerjaan_ayah' => isset($pekerjaanAyah->id_pekerjaan) ? $pekerjaanAyah->id_pekerjaan : 99,
                'hp_ayah' => trim($item->telepon_ayah),
                'id_penghasilan_ayah' => isset($penghasilanAyah->id_penghasilan) ? $penghasilanAyah->id_penghasilan : 11,
                'nm_wali' => trim($item->nama_wali),
                'tgl_lahir_wali' => $item->tanggal_lahir_wali,
                'id_pdk_wali' => isset($pendidikanWali->id_pdk) ? $pendidikanWali->id_pdk : 99,
                'id_pekerjaan_wali' => isset($pekerjaanWali->id_pekerjaan) ? $pekerjaanWali->id_pekerjaan : 99,
                'id_penghasilan_wali' => isset($penghasilanWali->id_penghasilan) ? $penghasilanWali->id_penghasilan : 11,
                'hp_wali' => trim($item->telepon_wali),
                'jenis_tinggal' => 99,
                'alat_transpor' => 99,
                'id_info_nobel' => "8",
                'created_at' => Carbon::now()->format('Y-m-d'),
            ];


            $prodi = DB::table('prodi')->where('nm_prodi', 'like', '%' . $item->program_studi . '%')->first();
            $periodeMasukArray = explode(" ", $item->periode_masuk);

            // NO DATA FROM SEVIMA
            $mahasiswaRegs[] = [
                'id' => Rmt::uuid(),
                'id_prodi' => isset($prodi->id_prodi) ? $prodi->id_prodi : null,
                'id_konsentrasi' => null,
                'id_mhs' => $idMHS,
                'jenis_daftar' => 1,
                'jam_kuliah' => null,
                'jalur_masuk' => 1,
                'nim' => $item->nim,
                'tgl_daftar' => Carbon::createFromFormat('d/m/Y', $item->tanggal_daftar),
                'dosen_pa' => null,//$r->dosen_pa,
                'id_kurikulum' => null,//$r->id_kurikulum,
                'id_jenis_keluar' => 0,
                'tgl_keluar' => null,
                'semester_mulai' => $periodeMasukArray[0] . ($periodeMasukArray[1] === "Ganjil" ? "1" : "2"),
                'semester_keluar' => null,
                'jalur_skripsi' => null,
                'judul_skripsi' => null,
                'awal_bimbingan' => null,
                'akhir_bimbingan' => null,
                'sk_yudisium' => null,
                'tgl_sk_yudisium' => null,
                'seri_ijazah' => null,
                'nm_pt_asal' => $item->universitas_awal,
                'nm_prodi_asal' => $item->nim_asal,
                'created_at' => Carbon::now()->format('Y-m-d'),
            ];

        }

        DB::transaction(function () use ($users, $mahasiswas, $mahasiswaRegs) {
            // generate users
            User::insert($this->uniqueByEmail($users));

            // generate mahasiswa
            Mahasiswa::insert($mahasiswas);

            // genearate mahasiswa reg
            Mahasiswareg::insert($mahasiswaRegs);
        });
        return 'Berhasil import';
//        return [
//            'user' => $users,
//            'mahasiswa' => $mahasiswas,
//            'mahasiswa_reg' => $mahasiswaRegs
//        ];

    }

    // DONE
    // LULUSAN, YUDISIUM, SKRIPSI HAMPIR SAMA ISINYA
    public function yudisium()
    {
        $yudisium = DB::connection('sevima')
            ->table('yudisium')
            ->where('periode_yudisium', 'like', '%2023%')
            ->get();

        $yudisium->map(function ($item) {
            Mahasiswareg::where('nim', $item->NIM)->update([
                'semester_keluar' => $item->PERIODE_YUDISIUM,
                'tgl_sk_yudisium' => $item->TGL_SK_YUDISIUM,
                'sk_yudisium' => $item->NOMER_SK_YUDISIUM,
                'tgl_ijazah' => null,
                'seri_ijazah' => null,
                'pin' => $item->PIN,
                'ipk' => $item->IPK_LULUS,
            ]);
        });
        return $yudisium;
    }

    // DONE
    // LULUSAN, YUDISIUM, SKRIPSI HAMPIR SAMA ISINYA
    public function lulusan()
    {
        $lulusan = DB::connection('sevima')
            ->table('lulusan')
            ->where('periode_yudisium', 'like', '%2023%')
            ->get();

//        return Mahasiswareg::whereIn('nim', $lulusan->pluck('NIM'))->get();
        $lulusan->map(function ($item) {
//            $tanggalIjazah = Carbon::createFromFormat('d/m/Y', $item->TGL_SK_YUDISIUM);
            Mahasiswareg::where('nim', $item->NIM)->update([
                'semester_keluar' => $item->PERIODE_YUDISIUM,
                'tgl_sk_yudisium' => $item->TGL_SK_YUDISIUM,
                'sk_yudisium' => $item->NO_SK_YUDISIUM,
                'tgl_ijazah' => $item->TGL_IJAZAH,
                'seri_ijazah' => $item->NO_IJAZAH,
                'judul_skripsi' => $item->JUDUL_SKRIPSI,
                'id_jenis_keluar' => "1",
                'tgl_keluar' => $item->TGL_SK_YUDISIUM
            ]);
        });
        return $lulusan;
    }

    // DONE
    public function mataKuliah()
    {
        // GENERATE MATAKULIAH
        $kurikulumSevimas = DB::connection('sevima')
            ->table('kurikulum_mk')
            ->where('kurikulum', 2023)
            ->get();
        $matakuliahs = [];
        foreach ($kurikulumSevimas as $item) {
            $matakuliahs[] = [
                'id' => Rmt::uuid(),
                'id_prodi' => $this->getProdi('nm_prodi', $item->PROGRAM_STUDI)->id_prodi,
                'kode_mk' => $item->KODE_MATAKULIAH,
                'nm_mk' => $item->NAMA_MATAKULIAH,
                'ujian_akhir' => "",
                'jenis_mk' => $item->JENIS_MATAKULIAH,
                'kelompok_mk' => $item->KELOMPOK_MATAKULIAH,
                'id_konsentrasi' => "",
                'sks_mk' => $item->SKS_MATAKULIAH,
                'sks_tm' => $item->SKS_TATAP_MUKA,
                'sks_prak' => $item->SKS_PRAKTIKUM,
                'sks_prak_lap' => $item->SKS_PRAKTIKUM_LAPANGAN,
                'sks_sim' => $item->SKS_SIMULASI,
                'a_sap' => 1,
                'a_silabus' => 1,
                'a_bahan_ajar' => 1,
                'acara_praktek' => 0,
                'a_diktat' => 0,
                'tgl_mulai_efektif' => null,
                'tgl_akhir_efektif' => null,
                'id_jenis_bayar' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }


        // GENERATE KURIKULUM
        $programStudis = DB::connection('sevima')
            ->table('kurikulum_mk')
            ->select('PROGRAM_STUDI')
            ->where('kurikulum', 2023)
            ->distinct()
            ->get()
            ->pluck('PROGRAM_STUDI');
        $prodis = DB::table('prodi')->whereIn('nm_prodi', $programStudis)->get();
        $kurikulums = [];
        foreach ($prodis as $prodi) {
            $kurikulums[] = [
                'id' => Rmt::uuid(),
                'nm_kurikulum' => $prodi->jenjang . " " . $prodi->nm_prodi . " 2023",
                'mulai_berlaku' => 20231,
                'id_prodi' => $prodi->id_prodi,
                'jml_sks_lulus' => 0,
                'jml_sks_wajib' => 0,
                'jml_sks_pilihan' => 0,
                'aktif' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::transaction(function () use ($matakuliahs, $kurikulums) {
            Matakuliah::insert($matakuliahs);
            Kurikulum::insert($kurikulums);
            foreach ($kurikulums as $kurikulum) {
                foreach ($matakuliahs as $matakuliah) {
                    MatakuliahKurikulum::insert([
                        'id' => Rmt::uuid(),
                        'id_kurikulum' => $kurikulum['id'],
                        'id_mk' => $matakuliah['id'],
                        'periode' => 1,
                        'smt' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        });
        return 'Berhasil import';

    }

    //
    public function nilaiPindahan(Request $request)
    {
        $offset = ($request->page - 1) * 500;
        $nilaiPindahanSevimas = DB::connection('sevima')
            ->table('nilai_pindahan')
            ->take(500)
            ->skip($offset)
            ->where('nim','not like','%xx%')
//            ->where('nim', 'like', '2023%')
            ->get();
        

        $result = [];
        foreach ($nilaiPindahanSevimas as $item) {

            $matakuliah = $this->getMataKuliah('kode_mk', $item->KODE_MATAKULIAH_DIAKUI);
            if($matakuliah)
            $result[] = [
                'id' => Rmt::uuid(),
                'id_mhs_reg' => @$this->getMahasiswaReg('nim', $item->NIM)->id,
                'id_mk' => $matakuliah->id,
                'id_nilai_sevima' => $item->id,
                'kode_mk_asal' => $item->KODE_MATAKULIAH_ASAL,
                'nm_mk_asal' => $item->NAMA_MATAKULIAH_ASAL,
                'sks_asal' => $item->SKS_MATAKULIAH_ASAL,
                'nilai_huruf_asal' => $item->NILAI_HURUF_MATAKULIAH_ASAL,
                'nilai_huruf_diakui' => $item->NILAI_HURUF_MATAKULIAH_DIAKUI,
                'nilai_indeks' => $item->NILAI_ANGKA_MATAKULIAH_DIAKUI,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::transaction(function () use ($result) {
            DB::table('nilai_transfer')->insert($result);
        });

        return 'Berhasil import';
    }


    // SETELAH EKSEKUSI KELAS LANGSUNG EKSEKUSI TEST
    public function kelas()
    {
        $kelasSevimas = DB::connection('sevima')
            ->table('kelas')
            ->where('periode', 'like', '%2023%')
            ->get();

        DB::transaction(function () use ($kelasSevimas) {
            foreach ($kelasSevimas as $item) {
                // KELAS
                $idProdi = @$this->getProdi('nm_prodi', $item->PROGRAM_STUDI)->id_prodi;
                $ketWaktu = "PAGI";

                $tempJam = $item->JAM_MULAI_1;
                $tempJam = explode(":", $tempJam);
                if ($tempJam[0] > 8) $ketWaktu = "PAGI";
                if ($tempJam[0] > 12) $ketWaktu = "SIANG";
                if ($tempJam[0] > 16) $ketWaktu = "MALAM";

                if ($idProdi !== null) {
                    $kelas = [
                        'id_prodi' => $idProdi,
                        'id_kelas_sevima' => $item->id,
                        'nm_kelas' => $item->NAMA_KELAS,
                        'ket' => $ketWaktu,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    Kelas::insert($kelas);

                    // JAM KULIAH
                    $jamKuliah = [
                        'id_prodi' => $idProdi,
                        'id_kelas_sevima' => $item->id,
                        'jam_masuk' => $item->JAM_MULAI_1 . ":" . "00",
                        'jam_keluar' => $item->JAM_SELESAI_1 . ":" . "00",
                        'ket' => $ketWaktu,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    Jamkuliah::insert($jamKuliah);

                    $periode = explode(' ', $item->PERIODE);
                    $periodex = $periode[1] === "Ganjil" ? "1" : "2";
                    $semester = $item->TAHUN_KURIKULUM . $periodex;

                    // JADWAL KULIAH
                    $lastKelasID = Jamkuliah::orderBy('id', 'desc')->first()->id;
                    $matakuliahs = Matakuliah::where('kode_mk', $item->KODE_MATA_KULIAH)
                        ->where('id_prodi',$idProdi)
                        ->orderBy('created_at','desc')
                        ->get();
                    $matakuliah = $matakuliahs->first();

                    $kurikulums = Kurikulum::where('mulai_berlaku','like','%'.$item->TAHUN_KURIKULUM.'%')
                        ->where('id_prodi',$idProdi)
                        ->get();

                    $kurikulum = $kurikulums->first();

                    $mataKuliahKurikulums = MatakuliahKurikulum::where('id_mk', @$matakuliah->id)
                        ->where('id_kurikulum', @$kurikulum->id)
                        ->get();
                    $mataKuliahKurikulum = $mataKuliahKurikulums->first();

                    $jadwalKuliah = [
                        'id' => Rmt::uuid(),
                        'id_mkur' => @$mataKuliahKurikulum->id ? $mataKuliahKurikulum->id : "xxxx",
                        'id_kur' => @$kurikulum->id ? $kurikulum->id : "xxxx",
                        'id_mk' => @$matakuliah->id,
                        'id_prodi' => $idProdi,
                        'id_smt' => $periode[0].$periodex,
                        'id_kelas_sevima' => $item->id,
                        'matakuliah_count' => $matakuliahs->count(),
                        'kurikulum_count' => $kurikulums->count(),
                        'mkkurikulum_count' => $mataKuliahKurikulums->count(),
                        'kode_kls' => $item->NAMA_KELAS,
                        'ruangan' => "",
                        'id_jam' => $lastKelasID,
                        'hari' => $item->HARI_1,
                        'kapasitas_kls' => $item->DAYA_TAMPUNG,
                        'jenis' => 1,
                        'publik' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    JadwalKuliah::insert($jadwalKuliah);
                }
            }
            $result = DB::table('jadwal_kuliah')->where('id_mkur','xxxx')->get();

            foreach ($result as $jadwalKuliah) {
                $mkKurikulum = MatakuliahKurikulum::where('id_mk',$jadwalKuliah->id_mk)->first();
                if($mkKurikulum){
                    JadwalKuliah::where('id',$jadwalKuliah->id)->update([
                        'id_mkur' => $mkKurikulum->id
                    ]);
                }
            }
        });
        return 'berhasil import';
    }

//    public function dosenMengajar(){
//        $jadwalKuliah = JadwalKuliah::whereDate('created_at',"2024-05-25")->get();
//        foreach ($jadwalKuliah as $item) {
//            $result = [
//              'id_jdk' => @$item->id,
//              'id_dosen' => "",
//              'jml_tm' => 14,
//              'jml_real' => 14,
//              'dosen_ke' => null,
//              'created_at' => Carbon::now(),
//              'updated_at' => Carbon::now(),
//            ];
//            DB::table('dosen_mengajar')->insert($result);
//        }
//        return $jadwalKuliah;
//        return 'Berhasil import';
//    }


    // KRS MHS
    //
    public function krs(Request $request)
    {
        $offset = ($request->page - 1) * 250;
        $krs = [];
        // MATAKULIAH
        $krsSevima = DB::connection('sevima')
            ->table('krs')
            ->where('periode', 'like', '%2023%')
            ->take(250)
            ->skip($offset)
            ->get();



        foreach ($krsSevima as $item) {
            $mahasiswaReg = Mahasiswareg::where('nim', $item->NIM)->first();


            $idProdi = @$this->getProdi('nm_prodi', $item->PROGRAM_STUDI_PENGAMPU)->id_prodi;
            $matakuliah = Matakuliah::where('kode_mk', $item->KODE_MATAKULIAH)
                ->where('id_prodi',$idProdi)
                ->orderBy('created_at','desc')
                ->first();
            
            $kurikulum = Kurikulum::where('mulai_berlaku','like', "%".$item->KURIKULUM."%")
                ->where('id_prodi',@$matakuliah->id_prodi)
                ->first();
            $mkKurikulum = MatakuliahKurikulum::where('id_mk', $matakuliah->id)
                ->where('id_kurikulum', @$kurikulum->id)
                ->first();
            $periode = explode(' ', $item->PERIODE);
            $periodex = $periode[1] === "Ganjil" ? "1" : "2";
            $krs[] = [
                'id_mhs_reg' => @$mahasiswaReg->id,
                'id_mkur' => @$mkKurikulum->id,
                'id_smt' => $periode[0].$periodex,
                'id_krs_sevima' => $item->id,
                'kurikulum_count' => $kurikulum->count(),
                'matakuliah_count' => $matakuliah->count(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::transaction(function() use ($krs){
            DB::table('krs_mhs')->insert($krs);
        });
        return $krs;
    }

    //
    public function nilai(Request $request)
    {
        $offset = ($request->page - 1) * 250;
        $krsSevimas = DB::connection('sevima')
            ->table('krs')
            ->where('periode', 'like', '%2023%')
//            ->where('kode_matakuliah','MPK2305B')
//            ->where('nama_kelas','AP202')
            ->take(250)
            ->skip($offset)
            ->get();

        $nilai = [];
        foreach ($krsSevimas as $item) {

            // MAHASISWA
            $mahasiswaReg = Mahasiswareg::where('nim', $item->NIM)->first();


            // JADWAL KULIAH
            $idProdi = @$this->getProdi('nm_prodi', $item->PROGRAM_STUDI_PENGAMPU)->id_prodi;
            $matakuliah = Matakuliah::where('kode_mk', $item->KODE_MATAKULIAH)
                ->where('id_prodi',$idProdi)
                ->orderBy('created_at','desc')
                ->first();
            $kurikulum = Kurikulum::where('mulai_berlaku','like', "%".$item->KURIKULUM."%")
                ->where('id_prodi',@$matakuliah->id_prodi)
                ->first();
            $mataKuliahKurikulum = MatakuliahKurikulum::where('id_mk', $matakuliah->id)
                ->where('id_kurikulum', @$kurikulum->id)
                ->first();
            $jadwalKuliahs = JadwalKuliah::where('id_mk', @$matakuliah->id)
                ->where('kode_kls', $item->NAMA_KELAS)
                ->where('id_kur',$kurikulum->id)
                ->where('id_mkur', @$mataKuliahKurikulum->id)
                ->get();
             $jadwalKuliah =  $jadwalKuliahs->first();

            $skalaNilais = DB::connection('sevima')
                ->table('skala_nilai')
                ->where('UNIT','like','%'.$item->PROGRAM_STUDI_PENGAMPU.'%')
                ->get();
            $nilaiIndex = 0;
            foreach ($skalaNilais as $skalaNilai) {
                if($item->NILAI_AKHIR >= $skalaNilai->BATAS_BAWAH &&
                    $item->NILAI_AKHIR <= $skalaNilai->BATAS_ATAS){
                    $nilaiIndex = $skalaNilai->NILAI_ANGKA;
                }
            }
            // ID MK
            $nilai[] = [
                'id' => Rmt::uuid(),
                'id_krs_sevima' => $item->id,
                'id_matakuliah' => @$matakuliah->id ? $matakuliah->id : 'xxxx',
                'id_kurikulum' => @$kurikulum->id ? $kurikulum->id : 'xxxx',
                'id_mkkur' => @$mataKuliahKurikulum->id ? $mataKuliahKurikulum->id : 'xxxx',
                'id_mhs_reg' => @$mahasiswaReg->id,
                'id_jdk' => @$jadwalKuliah->id ? $jadwalKuliah->id : 'xxxx',
                'semester_mk' => $item->PERIODE === 'Genap' ? "2" : "1",
                'nil_kehadiran' => '0',
                'nil_tugas' => $this->findProporsiNilai($item->NIM, $item->NAMA_MATAKULIAH, 'TUGAS_INDIVIDU'),
                'nil_mid' => $this->findProporsiNilai($item->NIM, $item->NAMA_MATAKULIAH, 'UTS'),
                'nil_final' => $this->findProporsiNilai($item->NIM, $item->NAMA_MATAKULIAH, 'UAS'),
                'nilai_angka' => $item->NILAI_AKHIR,
                'nilai_huruf' => $item->NILAI_HURUF,
                'nilai_indeks' => sprintf('%.2f', $nilaiIndex),
                'jdk_count' => $jadwalKuliahs->count(),
                'a_1' => '0',
                'a_2' => '0',
                'a_3' => '0',
                'a_4' => '0',
                'a_5' => '0',
                'a_6' => '0',
                'a_7' => '0',
                'a_8' => '0',
                'a_9' => '0',
                'a_10' => '0',
                'a_11' => '0',
                'a_12' => '0',
                'a_13' => '0',
                'a_14' => '0',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }


        DB::transaction(function () use ($nilai) {
            Nilai::insert($nilai);
        });
        return 'Berhasil Input Nilai';
    }


    public function seriIjazah()
    {
        $result = DB::connection('sevima')
            ->table('seri_ijazah')
            ->get();
        foreach ($result as $item) {
            Mahasiswareg::where('nim', $item->nim)->update(['seri_ijazah' => $item->seri_ijazah]);
        }

        return "Berhasil Update";

    }






    // UNKNOWN
    public function users()
    {

        $query = Sia::mahasiswa()
            ->where('m2.id_jenis_keluar','<>', 0)
            ->select('m2.id as id_mhs_reg','m1.nm_mhs','m1.gelar_depan','m1.gelar_belakang','m1.jenkel','a.nm_agama','m1.tgl_lahir','p.nm_prodi','m2.pin',
                'p.jenjang','m2.jam_kuliah','m2.semester_mulai','m2.nim','jk.ket_keluar',
                'm2.semester_keluar','m2.tgl_keluar')
            ->orderBy('m2.seri_ijazah', 'asc');
        return $query->get();
        // Filter
        Sia::lulusKeluarFilter($query);

        $data['mahasiswa'] = $query->paginate(15);
        return $data;
    }

    // HELPER FUNCTION
    private function findProporsiNilai($nim, $matakuliah, $komposisi)
    {
        $result = DB::connection('sevima')
            ->table('proporsi_nilai')
            ->where('nim', 'like', $nim . '%')
            ->where('NAMA_MATAKULIAH', $matakuliah)
            ->where('KOMPOSISI_NILAI', $komposisi)
            ->first();
        if ($result) {
            return $result->NILAI;
        }
        return 0;
    }


    private function getProdi($key, $value)
    {
        return DB::table('prodi')->where($key, $value)->first();
    }

    private function getMahasiswaReg($key, $value)
    {
        return DB::table('mahasiswa_reg')->where($key, $value)->first();
    }

    private function getMataKuliah($key, $value)
    {
        return DB::table('matakuliah')->where($key, $value)->first();
    }



    public function test(){
        $nilai = DB::table('nilai')
            ->where('id_jdk','xxxx')
            ->limit(10)
            ->get();
        return $nilai;

//        $kelasSevimas = DB::connection('sevima')
//            ->table('kelas')
//            ->where('periode', 'like', '%2023 genap%')
//            ->get();
//
//        $prodi = [];
//        foreach ($kelasSevimas as $kelasSevima) {
//
//            $prodix = DB::table('prodi')->where('nm_prodi', $kelasSevima->PROGRAM_STUDI)->first();
//            array_push($prodi, $prodix);
//        }
//        $query = DB::table('jadwal_kuliah as jdk')
//            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
//            ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
//            ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
//            ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
//            ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
//            ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
//            ->select('jdk.*','mk.kode_mk','mk.nm_mk','mk.sks_tm','mk.sks_mk',
//                'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.ket','jk.jam_masuk',
//                'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl',
//                DB::raw('
//							(SELECT group_concat(distinct dm.dosen_ke,". ",dos.gelar_depan," ", dos.nm_dosen,", ", dos.gelar_belakang SEPARATOR \'<br>\') from dosen_mengajar as dm
//							left join dosen as dos on dm.id_dosen=dos.id
//							where dm.id_jdk=jdk.id order by dm.dosen_ke asc) as dosen'),
//                DB::raw('(SELECT COUNT(*) as agr from nilai where id_jdk=jdk.id) as terisi'))
//            ->where('jdk.jenis', "1")
//            ->where('jdk.id_smt','20222')
//            ->orderBy('mkur.smt')
//            ->orderBy('jdk.hari','asc')
//            ->orderBy('jk.jam_masuk','asc');
//        return $query->get()->count();
    }

    private function uniqueByEmail($users) {
        $uniqueUsers = [];
        $emails = collect($users)->map(function ($item){
            return $item['email'];
        })->toArray();

        foreach ($users as $user) {
            if (!in_array($user['email'], $emails)) {
                $emails[] = $user['email'];
                $uniqueUsers[] = $user;
            }
        }

        return $uniqueUsers;
    }
}


