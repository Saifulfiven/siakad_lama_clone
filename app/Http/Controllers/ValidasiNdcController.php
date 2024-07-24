<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seminar, App\Mahasiswareg;
use DB, Sia, Rmt, Response, Session, Carbon;

class ValidasiNdcController extends Controller
{

    public function index(Request $r)
    {
        if ( !Session::has('ndc') ) {
            Session::put('ndc.smt', Sia::sessionPeriode());
        }

        if ( $r->pencarian ) {
            if ( !empty($r->cari) ) {
                Session::put('ndc.cari',$r->cari);
            } else {
                Session::pull('ndc.cari');
            }

            return redirect()->route('val_ndc');
        }


    	$query = DB::table('seminar_pendaftaran as sp')
                    ->join('seminar_file as sf', 'sp.id', 'sf.id_seminar')
                    ->leftJoin('mahasiswa_reg as m1', 'sp.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                    ->select('sp.*', 'm1.nim', 'm2.nm_mhs', 'pr.nm_prodi', 'pr.jenjang')
                    ->where('sp.jenis', 'H')
                    ->where('sp.id_smt', Session::get('ndc.smt'))
                    ->where('sf.jenis_file', 'olah-data')
                    ->groupBy('sp.id_mhs_reg');

        $this->filter($query);

        $data['seminar'] = $query->orderBy('sp.created_at','desc')->paginate(20);

	    return view('validasi-ndc.index', $data);
    }

    public function detail(Request $r)
    {
        $select_file = "(SELECT GROUP_CONCAT(file) from seminar_file
                            where id_seminar=sp.id and jenis_file='olah-data') as files";

        $data['seminar'] = DB::table('seminar_pendaftaran as sp')
                    ->leftJoin('mahasiswa_reg as m1', 'sp.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('sp.*', 'm1.nim','m2.nm_mhs', DB::raw($select_file))
                    ->where('sp.id', $r->id_seminar)
                    ->first();

        $data['files'] = DB::table('seminar_file')
                            ->where('id_seminar', $r->id_seminar)
                            ->where('jenis_file', 'olah-data')
                            ->orderBy('id')
                            ->get();

        return view('validasi-ndc.detail', $data);

    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('ndc.'.$r->modul);
            } else {
                Session::put('ndc.'.$r->modul, $r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull('ndc');
        }

        return redirect(route('val_ndc'));
    }

    public function filter($query)
    {
        
        if ( Session::has('ndc.status') ) {
            $query->where('sp.validasi_ndc', Session::get('ndc.status'));
        }

        if ( Session::has('ndc.prodi') ) {
            $query->where('m1.id_prodi', Session::get('ndc.prodi'));
        }

        if ( Session::has('ndc.cari') ) {
            $query->where(function($q) {
                $q->where('m1.nim','LIKE','%'.Session::get('ndc.cari').'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.Session::get('ndc.cari').'%');
            });
        }

    }

    public function proses(Request $r)
    {

        try {

            $seminar = Seminar::findOrFail($r->id);

            if ( $r->disetujui == 1 ) {

                $seminar->validasi_ndc = 1;

            
            } else {

                $seminar->validasi_ndc = 0;

            }

            $seminar->save();

            Rmt::success('Berhasil menyimpan perubahan');

            return redirect()->back();

        } catch( \Exception $e ) {

            Rmt::error('Gagal menyimpan perubahan.'. $e->getMessage());
            return redirect()->back();

        }
    }

}