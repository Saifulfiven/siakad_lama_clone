<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mahasiswareg, App\Bimbinganmhs, App\Bimbingandetail;
use Rmt, DB, Response, Sia;

class BimbinganMahasiswaController extends Controller
{
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        try {

            $mhs = Mahasiswareg::where('nim', $r->nim)->first();

            $periode = Rmt::semesterBerjalan();

            $ta_berjalan = $periode->id_smt;

            if ( empty($r->ta) ) {

                $ta_aktif = $periode->id_smt;

            } else {

                $ta_aktif = $r->ta;
            }

            $jenis = 'P';

            if ( $r->jenis ) {
                $jenis = $r->jenis;
            } 

            // Ambil data pembimbing dan penguji dari table penguji & ujian_akhir
            $bimbingan = Rmt::bimbinganMhs($ta_aktif, $mhs->id, $jenis);

            $cek_bimbingan = Bimbinganmhs::where('id_mhs_reg', $mhs->id)
                                ->where('jenis', $jenis)
                                ->where('id_smt', $ta_aktif)
                                ->count();

            // Insert data ke bimbingan
            if ( empty($cek_bimbingan) ) {
                $bimb = new Bimbinganmhs;
                $bimb->id_mhs_reg = $mhs->id;
                $bimb->jenis = $jenis;
                $bimb->id_smt = $ta_aktif;
                $bimb->save();
            }

            // Ambil 1 data bimbingan_mhs dari tabel bimbingan_mhs
            $data_bim = Bimbinganmhs::where('id_mhs_reg', $mhs->id)
                                ->where('jenis', $jenis)
                                ->where('id_smt', $ta_aktif)
                                ->first();

            $semester = DB::table('bimbingan_mhs as bm')
                            ->join('semester as s', 's.id_smt', 'bm.id_smt')
                            ->where('bm.id_mhs_reg', $mhs->id)
                            ->groupBy('bm.id_smt')
                            ->select('s.id_smt','s.nm_smt')
                            ->get();

            $jenis_seminar = Rmt::jenisSeminar();

            $data = [
                'bimbingan' => $bimbingan,
                'data_bimbingan' => $data_bim,
                'semester' => $semester,
                'jenis_seminar' => $jenis_seminar,
                'ta_aktif' => $ta_aktif,
                'jenis' => $jenis
            ];

            return Response::json($data);

        } catch( \Exception $e ) {
            return Response::json($e->getMessage(), 422);
        }
    }

    public function riwayat(Request $r)
    {
        $riwayat = Bimbingandetail::where('id_bimbingan_mhs', $r->id_bimbingan)
                    ->where('jabatan_pembimbing', $r->jabatan)
                    ->get();

        $data = [
            'riwayat' => $riwayat,
            'ket' => $r->jabatan == 'KETUA' ? 'Pembimbing I' : 'Pembimbing II'
        ];

        return Response::json($data);
    }

    public function lampiran(Request $r)
    {
        $file = Bimbingandetail::where('id_bimbingan_mhs', $r->id_bimbingan)
                ->where('id', $r->id_bimbingan_detail)->first();


        if ( !empty($file) ) { 
            $path = config('app.file-bimbingan').'/'.$r->jenis;
            $pathToFile = $path.'/'.$file->file;
            
            if ( file_exists($pathToFile) ) {
                return Response::download($pathToFile, $file->file);
            } else {
                return Response::json('File tidak ditemukan', 404);
            }
        } else {
            return Response::json('File tidak ditemukan', 404);
        }
    }

    public function download(Request $r)
    {
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $file = Bimbinganmhs::where('id_mhs_reg', $mhs->id)
                ->where('id', $r->id_bimbingan)->first();

        if ( !empty($file) ) {
            $path = config('app.file-bimbingan').'/'.$r->jenis;
            $pathToFile = $path.'/'.$file->file;
            
            if ( file_exists($pathToFile) ) {
                return Response::download($pathToFile, $file->file);
            } else {
                return Response::json('File tidak ditemukan', 404);
            }
        } else {
            return Response::json('File tidak ditemukan', 404);
        }
    }
}
