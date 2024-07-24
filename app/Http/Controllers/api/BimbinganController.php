<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mahasiswareg;
use Rmt, DB, Response, Sia;

class BimbinganController extends Controller
{
    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {
        $periode = Rmt::semesterBerjalan();

        $ta_berjalan = $periode->id_smt;

        if ( empty($r->ta) ) {

            $ta_aktif = $periode->id_smt;

        } else {

            $ta_aktif = $r->ta;
        }

        $bimbingan = Rmt::bimbinganDosen($ta_aktif, $r->id_dosen)->get();

        $semester = DB::table('penguji as p')
                        ->join('semester as smt', 'smt.id_smt', 'p.id_smt')
                        ->where('p.id_dosen', $r->id_dosen)
                        ->select('smt.id_smt','smt.nm_smt')
                        ->groupBy('p.id_smt')
                        ->orderBy('p.id_smt', 'desc')
                        ->get();

        $jenis = [ 
            'P' => 'Seminar Proposal',
            'H' => 'Seminar Hasil',
            'S' => 'Ujian Skripsi/Tesis'
        ];

        $data = [
            'bimbingan' => $bimbingan,
            'jenis' => $jenis,
            'semester' => $semester,
            'ta' => $ta_aktif
        ];

        return Response::json($data);
    }

}
