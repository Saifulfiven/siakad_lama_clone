<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use DB;
use App\Mahasiswareg;

trait Library
{
    public function option($key)
    {
        $data = DB::table('options')->select('value')->where('id', $key)->first();
        return $data->value;
    }

    /**
     * Semester yang sedang berjalan, semester tidak berubah-ubah kecuali saat naik semester
     * untuk kepentingan seperti posisi semester mahasiswa dll.
     */
    public function semesterBerjalan($id_mhs_reg)
    {
        $id_fakultas = $this->getFakultasUser($id_mhs_reg);
        $result = DB::table('semester_aktif as sa')
                    ->leftJoin('semester as smt','sa.id_smt','=','smt.id_smt')
                    ->select('smt.id_smt','smt.nm_smt','smt.smt')
                    ->where('sa.id_fakultas', $id_fakultas)->first();

        $data = ['id' => $result->id_smt, 'nama' => $result->nm_smt, 'ket' => $result->smt];

        return $data;
    }

    private function semesterBerjalanS2($id_mhs_reg)
    {
        $data = DB::table('nilai as n')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('semester as smt', 'jdk.id_smt', 'smt.id_smt')
                ->select('jdk.id_smt','smt.nm_smt','smt.smt')
                ->where('n.id_mhs_reg', $id_mhs_reg)
                ->orderBy('jdk.id_smt', 'desc')
                ->first();

        return $data;
    }

    public function getFakultasUser($id_mhs_reg)
    {
        $data = DB::table('mahasiswa as m')
                    ->leftJoin('mahasiswa_reg as m2', 'm.id', 'm2.id_mhs')
                    ->leftJoin('prodi as pr','m2.id_prodi','=','pr.id_prodi')
                    ->leftJoin('fakultas as f', 'pr.id_fakultas', 'f.id')
                    ->select('f.id')
                    ->where('m2.id', $id_mhs_reg)->first();

        return empty($data->id)?'':$data->id;
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
            ->where('jdu.jenis_ujian', 'UAS')
            ->where('jdk.id_prodi', '61101')
            ->count();

        return $data > 0 ? 'UAS':'UTS';
    }

    public function listSmt()
    {
        $smt = DB::table('semester_aktif')
                ->max('id_smt');

        $data = DB::table('semester')
                ->where('id_smt', '<=', $smt)
                ->take(3)
                ->orderBy('id_smt', 'desc')
                ->get();

        return $data;
    }

    public function posisiSemesterMhs($smt_mulai, $smt_max = NULL) {
        /**
         * semester sekarang
         * @value => 20171 dst
         */
        $id_smt_akhir = $smt_max;

        /**
         * posisi semester sekarang, ganjil/genap
         * @value => 1: ganjil atau 2: genap
         */
        $jenis_smt_akhir = substr($smt_max,4,1);
        $jenis_smt_mulai = substr($smt_mulai, 4,1);

        $thn_mulai = substr($smt_mulai,0,4);
        $thn_akhir = substr($id_smt_akhir,0,4);

        $selisih_tahun = $thn_akhir - $thn_mulai;

        if ( $selisih_tahun == 0 ) {

            $smt = ( $selisih_tahun + 1 ) * 2;

        } else {
            
            $smt = $selisih_tahun * 2;
            
        }

        if ( $jenis_smt_mulai == 1 && $jenis_smt_akhir == 1 ) { // Pasti ganjil
            if ( $selisih_tahun == 0 ) {
                $smt -= 1;
            } else {
                $smt += 1;
            }
        } elseif ( $jenis_smt_mulai == 1 && $jenis_smt_akhir == 2 ) { // Pasti genap
            if ( $selisih_tahun > 0 ) {
                $smt += 2;
            }
            //Cat: Untuk kondisi 2 1 diabaikan (kondisi ini pasti semester genap)
        } elseif ( $jenis_smt_mulai == 2 && $jenis_smt_akhir == 2 ) { // Pasti Ganjil
            if ( $selisih_tahun == 0 ) {
                $smt -= 1;
            } else {
                $smt += 1;
            }
        }

        return $smt;
    }

    public function listJenisSmt($jenis = 1)
    {
        return $jenis == 1 ? [1,3,5,7] : [2,4,6,8];
    }


    public function jadwalKuliahMahasiswa($id_mhs_reg, $jenis = 1, $smt)
    {
        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
                ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
                ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
                ->select('jdk.id','jdk.hari','jdk.kode_kls','mk.nm_mk','r.nm_ruangan','jk.jam_masuk','jk.jam_keluar',
                    DB::raw('
                        (SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen, ", ",dos.gelar_belakang SEPARATOR \' & \') from dosen_mengajar as dm
                        left join dosen as dos on dm.id_dosen=dos.id
                        where dm.id_jdk=jdk.id) as dosen'),
                    DB::raw('(SELECT COUNT(*) as agr from nilai where id_jdk=jdk.id) as terisi'))
                ->where('jdk.jenis', $jenis)
                ->where('jdk.id_smt', $smt)
                ->where('n.id_mhs_reg', $id_mhs_reg)
                ->orderBy('jdk.hari','asc')
                ->orderBy('jk.jam_masuk','asc');

        return $data;
    }

    public function nilai($id_mhs_reg, $id_smt, $jenis = 1)
    {

        $krs = $this->krsMhs($id_mhs_reg, $id_smt, $jenis)
                ->select('n.*','jdk.kode_kls','jdk.id_jam','mk.id as id_mk','mk.kode_mk','mk.nm_mk','mk.sks_mk')
                ->get();

        return $krs;
    }

    public function ipk($id_mhs_reg, $smt_mulai, $id_smt)
    {
        $ipk = $this->ipkKhs($id_mhs_reg, $smt_mulai, $id_smt);

        return $ipk;
    }

    public function semester($smt_mulai, $id_smt, $smt_aktif)
    {
        $data = DB::table('semester')
        ->whereBetween('id_smt', [$smt_mulai, $id_smt])
        ->orderBy('id_smt','desc')->get();

        $ta = [];

        foreach( $data as $r ) {
            $ta[] = ['ta_aktif' => $smt_aktif,'ta' => $r->id_smt, 'nm_ta' => $r->nm_smt];
        }

        return $ta;
    }

    private function krsMhs($id_mhs_reg,$id_smt = '', $jenis = 1)
    {
        // Jenis 1 = KULIAH, 2 = SP
        $smt = empty($id_smt) ? $this->sessionPeriode() : $id_smt;

        $data = DB::table('nilai as n')
            ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', '=', 'n.id_jdk')
            ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
            ->leftJoin('matakuliah as mk', 'mk.id', '=', 'mkur.id_mk')
            ->select('n.*','jdk.kode_kls','mk.kode_mk','mk.nm_mk','mk.sks_mk','jdk.id_smt')
            ->where('n.id_mhs_reg', $id_mhs_reg)
            ->where('jdk.jenis',$jenis)
            ->where('jdk.id_smt', $smt);

        return $data;
    }

    private function ipkKhs($id_mhs_reg, $min_smt, $max_smt)
    {
        $data = DB::table('nilai as nil')
                ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'nil.id_jdk')
                ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                ->whereBetween('jdk.id_smt', [$min_smt,$max_smt])
                ->where('nil.id_mhs_reg', $id_mhs_reg)
                ->whereNotNull('nil.nilai_indeks')
                ->where('nil.nilai_indeks','<>', 0)
                ->select(DB::raw('sum(mk.sks_mk * nil.nilai_indeks) as tot_nilai'),
                        DB::raw('sum(mk.sks_mk) as tot_sks'))->first();

        if ( empty($data->tot_nilai) ) {
            return '0.00';
        } else {
            return round($data->tot_nilai/$data->tot_sks,2);
        }

    }

    public function totalPotonganPerMhs($id_mhs_reg, $smt_masuk, $id_smt)
    {
        $smt = $id_smt - $smt_masuk;

        if ( $smt == 0 ) {
            // Semester 1
            $data = DB::table('potongan_biaya_kuliah')
                    ->where('id_mhs_reg', $id_mhs_reg)
                    ->sum('potongan');
        } else {
            $data = DB::table('potongan_biaya_kuliah')
                    ->where('id_mhs_reg', $id_mhs_reg)
                    ->where('jenis_potongan', 'BPP')
                    ->sum('potongan');
        }

        return $data;
    }

    public function historyBayar($id_smt = NULL, $id_jns_bayar = NULL)
    {
        $query = DB::table('pembayaran as p')
            ->leftJoin('bank as b', 'p.id_bank', 'b.id')
            ->select('p.*','b.nm_bank');

        if ( !empty($id_smt) ) {
            $query->where('p.id_smt', $id_smt);
        }

        if ( empty($id_jns_bayar) ) {
            $query->where('p.id_jns_pembayaran',0);
        } else {
            $query->where('p.id_jns_pembayaran', $id_jns_bayar);
        }

        return $query;
    }

    public function tunggakan($id_mhs_reg, $smt_mulai, $smt_now, $jml_smt)
    {
        $tunggakan = 0;

        $smstr = $jml_smt;

        $mhs = Mahasiswareg::find($id_mhs_reg);

        /* Potongan */
            $pot_spp = DB::table('potongan_biaya_kuliah')
                        ->where('id_mhs_reg', $id_mhs_reg)
                        ->where('jenis_potongan', 'SPP')
                        ->first();
            $pot_spp = empty($pot_spp) ? 0 : $pot_spp->potongan;

            $pot_bpp = DB::table('potongan_biaya_kuliah')
                        ->where('id_mhs_reg', $id_mhs_reg)
                        ->where('jenis_potongan', 'BPP')
                        ->first();
            $pot_bpp = empty($pot_bpp) ? 0 : $pot_bpp->potongan;
        /* end potongan */

        $angkatan = substr($smt_mulai, 0, 4);

        if ( $smstr > 0 ) {
            $biaya = DB::table('biaya_kuliah')
                        ->where('tahun', $angkatan)
                        ->where('id_prodi', $mhs->id_prodi)
                        ->first();
            if ( !empty($biaya) ) {

                if ( $smt_mulai == $mhs->semester_mulai ) {
                    // tagihan semester 1
                    $tagihan_1 = $biaya->spp + $biaya->seragam + $biaya->lainnya - $pot_spp;
                } else {
                    $tagihan_1 = 0;
                }

                // Tagihan semester 2 dst..
                $tagihan_2 = ( $biaya->bpp - $pot_bpp ) * $smstr;

                $telah_bayar = DB::table('pembayaran')
                                ->where('id_jns_pembayaran', 0)
                                ->where('id_mhs_reg', $id_mhs_reg)
                                ->where('id_smt', '<', $smt_now)
                                ->where('id_smt', '>=', $smt_mulai)
                                ->sum('jml_bayar');

                $tunggakan = $tagihan_1 + $tagihan_2 - $telah_bayar;
            }
        }

        return $tunggakan;
    }

    public function get_file_extension($file_name) {
        return substr(strrchr($file_name,'.'),1);
    }

    public function listKelasKonsentrasi()
    {
        // $data = ['X' => 'O','XI' => 'N'];
        $data = ['XII' => 'S'];
        return $data;
    }

    private function skalaNilai($id_prodi)
    {
        $data = DB::table('skala_nilai')
                ->where('id_prodi', $id_prodi)
                ->where('nilai_huruf', '<>', 'T')
                ->orderBy('nilai_indeks','desc')->get();
        return $data;
    }

    private function cekKehadiran($id_jdk, $id_mhs_reg)
    {
        $cek = DB::table('nilai')
                ->where('id_jdk', $id_jdk)
                ->where('id_mhs_reg', $id_mhs_reg)
                ->selectRaw('(a_1 + a_2 + a_3 + a_4 + a_5 + a_6 + a_7 + a_8 + a_9 + a_10 + a_11 + a_12 + a_13 + a_14) as hadir')
                ->first();

        if ( !empty($cek) ) {
            return $cek->hadir;
        } else {
            return 0;
        }
    }

    private function pesertaKelas($id_jdk)
    {
        $data = DB::table('nilai as n')
                ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'n.id_jdk')
                ->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
                ->leftJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
                ->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
                ->select('n.id as id_nilai','jdk.jenis', 'n.nil_kehadiran','n.nil_tugas','n.nil_mid','n.nil_final','n.nilai_huruf','m2.id as id_mhs_reg', 'm2.nim','m1.nm_mhs')
                ->where('n.id_jdk', $id_jdk)
                ->orderBy('m2.nim')
                ->get();

        $data_arr = [];

        foreach( $data as $val ) {
            $min_hadir = $val->jenis == '1' ? 8 : 7;

            $cek_hadir = $this->cekKehadiran($id_jdk, $val->id_mhs_reg);
            $kehadiran = $cek_hadir >= $min_hadir ? true : false;

            $data_arr[] = [
                'id_mhs_reg' => $val->id_mhs_reg,
                'id_nilai' => $val->id_nilai,
                'nil_kehadiran' => $val->nil_kehadiran,
                'nil_tugas' => $val->nil_tugas,
                'nil_mid' => $val->nil_mid,
                'nil_final' => $val->nil_final,
                'nilai_huruf' => $val->nilai_huruf,
                'nim' => $val->nim,
                'nm_mhs' => $val->nm_mhs,
                'kehadiran' => $kehadiran
            ];
        }

        return $data_arr;
    }

}
