<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response, Hash;
use App\Mahasiswareg, App\konsentrasiPps;
use DB;

class PilihKonsentrasiController extends Controller
{
    use Library;

    public function __construct(Request $r)
    {
        Rmt::auth(config('app.token'), $r->token);
    }


    public function index(Request $r)
    {
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();


        $mhs = DB::table('mahasiswa_reg as m2')
                ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                ->select('m.id','m.nm_mhs', 'm2.id as id_reg_pd','m2.nim','m.jenkel','m2.id_prodi',
                        'm2.semester_mulai','p.nm_prodi', 'p.jenjang')
                ->where('m2.nim', $r->nim)->first();
        
        if ( empty($mhs) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada bisa ditampilkan']);
        }

        $periode = $this->semesterBerjalan($mhs->id_reg_pd);

        $konsentrasi = DB::table('konsentrasi_pps as kp')
                        ->leftJoin('konsentrasi as k', 'k.id_konsentrasi', 'kp.id_konsentrasi')
                        ->where('id_mhs_reg', $mhs->id_reg_pd)
                        ->select('kp.*', 'k.nm_konsentrasi')
                        ->first();

        $listKonsentrasi = DB::table('konsentrasi')
                            ->where('id_prodi', $mhs->id_prodi)
                            ->where('aktif', '1')
                            ->get();

        $smt_mhs = $this->posisiSemesterMhs($mhs->semester_mulai, $periode['id']);

        $kelas = [];

        foreach( $this->listKelasKonsentrasi() as $key => $val ) {
            foreach( range('A', $val) as $bag ) {
                $kelas[] = $key.'-'.$bag;
            }
        }

        // $kelas[] = ['XII-H1','XII-H2','XIII-A','XIII-B','XIII-C','XIII-D','XIII-E'];

        foreach( $this->listKelasKonsentrasi2() as $key => $val ) {
            foreach( range('A', $val) as $bag ) {
                $kelas[] = $key.'-'.$bag;
            }
        }

        if ( $smt_mhs == 2 && $mhs->id_prodi == '61101' ) {
            $eligible = true;
        } else {
            $eligible = false;
        }

        $data = [
            'mhs' => $mhs,
            'konsentrasi' => $konsentrasi,
            'listKonsentrasi' => $listKonsentrasi,
            'eligible' => $eligible,
            'kelas' => $kelas
        ];

        $result = ['error' => 0, 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    
    }

    public function listKelasKonsentrasi2()
    {
        // $data = ['X' => 'O','XI' => 'N'];
        $data = ['XIV' => 'K', 'XV' => 'E'];
        return $data;
    }

    public function store(Request $r)
    {

        try {
            $mhs = Mahasiswareg::where('nim', $r->nim)->first();

            
            if ( empty($mhs) ) {
                return Response::json(['error' => 1, 'msg' => 'Tidak ada bisa ditampilkan']);
            }

            $periode = $this->semesterBerjalan($mhs->id);

            DB::transaction(function()use($r, $mhs, $periode) {
                $data = new konsentrasiPps;
                $data->id_smt = $periode['id'];
                $data->id_mhs_reg = $mhs->id;
                $data->id_konsentrasi = $r->id_konsentrasi;
                $data->kelas = $r->kelas;
                $data->save();

                $mhs = Mahasiswareg::find($mhs->id);
                $mhs->id_konsentrasi = $r->id_konsentrasi;
                $mhs->save();
            });
            

        } catch(\Exception $e) {
            $result = ['error' => 1, 'msg' => $e->getMessage()];
            return Response::json($result, 422, [], JSON_UNESCAPED_SLASHES);
        }

        $result = ['error' => 0, 'msg' => 'Berhasil menyimpan data'];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }
}
