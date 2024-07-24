<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Seminar;
use DB, Rmt, Response, Session;

class PersetujuanSeminarController extends Controller
{

    public function index(Request $r)
    {
        
        $periode = Rmt::semesterBerjalan();

        $ta_berjalan = $periode->id_smt;

        if ( empty($r->ta) ) {

            $ta_aktif = $periode->id_smt;

        } else {

            $ta_aktif = $r->ta;
        }

    	$query = DB::table('seminar_validasi as sv')
                    ->leftJoin('seminar_pendaftaran as sp', 'sv.id_seminar', 'sp.id')
                    ->leftJoin('mahasiswa_reg as m1', 'sp.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('sv.*', 'sp.id_mhs_reg', 'sp.jenis', 'm1.nim', 'm2.nm_mhs')
                    ->where('sv.id_dosen', $r->id_dosen)
                    ->where('sp.id_smt', $ta_aktif);

        // Filter
        if ( $r->jenis ) {
            $query->where('sp.jenis', $r->jenis);
        }

        if ( $r->status ) {
            $query->where('sv.disetujui', $r->status);
        }

        if ( $r->cari ) {
            $query->where(function($q)use($r) {
                $q->where('m1.nim','LIKE','%'.$r->cari.'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.$r->cari.'%');
            });
        }

        $seminar = $query->get();

        $result = [
            'jenis_seminar' => Rmt::jenisSeminar(),
            'seminar' => $seminar
        ];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function update(Request $r)
    {

        try {


            DB::beginTransaction();

            $validasi = DB::table('seminar_validasi')->where('id', $r->id)->first();

            if ( $r->disetujui == 1 ) {

                // Cek semua validasi dari bauk dan pembimbing lain
                $telah_disetujui = Rmt::checkPersetujuanSeminar($validasi->id_seminar, $r->id);

                if ( $telah_disetujui ) {

                    // Update table seminar_pendaftaran
                    DB::table('seminar_pendaftaran')->where('id', $validasi->id_seminar)
                        ->update(['disetujui' => '1']);
                
                }

            
            } else {

                // Update table seminar_pendaftaran
                DB::table('seminar_pendaftaran')->where('id', $validasi->id_seminar)
                    ->update(['disetujui' => '0']);

            }


            // Update table seminar_validasi
            $data = ['disetujui' => $r->disetujui];

            DB::table('seminar_validasi')->where('id', $r->id)
                ->update($data);

            DB::commit();


        } catch( \Exception $e ) {
            DB::rollback();
            return Response::json([$e->getMessage()], 422);
        }
    }

}