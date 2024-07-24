<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Sia, Rmt, Response, Session, Auth;

trait KeadaanMahasiswa
{
    public function maba($angkatan,$prodi = null)
    {
        $maba_l = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->whereRaw("left(m1.semester_mulai,4)=$angkatan")
                    ->where('m2.jenkel', 'L');
        if ( !empty($prodi) ) {
            $maba_l->where('m1.id_prodi', $prodi);
        }
        $laki = $maba_l->count();

        $maba_p = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->whereRaw("left(m1.semester_mulai,4)=$angkatan")
                    ->where('m2.jenkel', 'P');
        if ( !empty($prodi) ) {
            $maba_p->where('m1.id_prodi', $prodi);
        }
        $perempuan = $maba_p->count();

        return [$laki,$perempuan];
    }

    public function aktif($angkatan,$status = 'A',$prodi = null)
    {
        $aktif_p = DB::table('aktivitas_kuliah as ak')
                ->leftJoin('mahasiswa_reg as m', 'ak.id_mhs_reg', 'm.id')
                ->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
                ->where('ak.id_smt', Sia::sessionPeriode())
                ->whereRaw('left(nim,4)='.$angkatan)
                ->where('m2.jenkel', 'P');

        if ( !empty($prodi) ) {
            $aktif_p->where('m.id_prodi', $prodi);
        }

        if ( $status == 'A' ) {
            $aktif_p->where('ak.status_mhs', $status);
        } else {
            $aktif_p->where('ak.status_mhs','<>', 'A');
        }

        $aktif_l = DB::table('aktivitas_kuliah as ak')
                ->leftJoin('mahasiswa_reg as m', 'ak.id_mhs_reg', 'm.id')
                ->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
                ->where('ak.id_smt', Sia::sessionPeriode())
                ->whereRaw('left(nim,4)='.$angkatan)
                ->where('m2.jenkel', 'L');

        if ( !empty($prodi) ) {
            $aktif_l->where('m.id_prodi', $prodi);
        }

        if ( $status == 'A' ) {
            $aktif_l->where('ak.status_mhs', $status);
        } else {
            $aktif_l->where('ak.status_mhs','<>', 'A');
        }

        $laki = $aktif_l->count();
        $perempuan = $aktif_p->count();

        return [$laki,$perempuan];
    }

    public function alumni($angkatan, $jenis_keluar = 1, $prodi = null)
    {

        $lulus_l = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->whereRaw("left(m1.semester_mulai, 4)=$angkatan")
                    ->where('m2.jenkel', 'L');

        if ( !empty($prodi) ) {
            $lulus_l = $lulus_l->where('m1.id_prodi', $prodi);
        }

        if ( $jenis_keluar == 1 || $jenis_keluar == 3 ) {
            $lulus_l->where('m1.id_jenis_keluar', $jenis_keluar);
        } else {
            $lulus_l->whereNotIn('m1.id_jenis_keluar',[1,3,0]);
        }

        $lulus_laki = $lulus_l->count();

        $lulus_p = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->whereRaw("left(m1.semester_mulai, 4)=$angkatan")
                    ->where('m2.jenkel', 'P');

        if ( !empty($prodi) ) {
            $lulus_p = $lulus_p->where('m1.id_prodi', $prodi);
        }

        if ( $jenis_keluar == 1 || $jenis_keluar == 3 ) {
            $lulus_p->where('m1.id_jenis_keluar', $jenis_keluar);
        } else {
            $lulus_p->whereNotIn('m1.id_jenis_keluar',[1,3,0]);
        }

        $lulus_perempuan = $lulus_p->count();

        return [$lulus_laki, $lulus_perempuan];
    }
}