<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Session, Sia, Rmt;

class StatusKrsController extends Controller
{
    public function index(Request $r)
    {

        if ( !Session::has('statusKrs.smt') ) {
            Session::put('statusKrs.smt',Sia::sessionPeriode());
        }

    	$query = DB::table('krs_status as krs')
                ->join('mahasiswa_reg as m2', 'krs.id_mhs_reg', '=', 'm2.id')
                ->join('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
                ->join('prodi as p', 'p.id_prodi','=','m2.id_prodi')
                ->select('krs.id','m2.id as id_mhs_reg','m2.nim','m1.nm_mhs','m2.semester_mulai','p.jenjang','p.nm_prodi','krs.status_krs');

        $data['mahasiswa'] = $this->filter($query)->orderBy('nim')->paginate(10);

    	return view('krs-status.index', $data);
    }

    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('statusKrs.cari',$r->cari);
        } else {
            Session::pull('statusKrs.cari');
        }

        return redirect(route('status_krs'));
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('statusKrs.'.$r->modul);
            } else {
                Session::put('statusKrs.'.$r->modul,$r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull('statusKrs');
        }

        return redirect(route('status_krs'));
    }

    private function filter($query)
    {
        if ( Session::has('statusKrs.cari') ) {
            $query->where(function($q){
                $q->where('m2.nim', 'like', '%'.Session::get('statusKrs.cari').'%')
                    ->orWhere('m1.nm_mhs', 'like', '%'.Session::get('statusKrs.cari').'%');
            });
        }

        if ( Session::has('statusKrs.smt') ) {
            $query->where('krs.id_smt', Session::get('statusKrs.smt'));
        }

        if ( Session::has('statusKrs.status') ) {
            $query->where('krs.status_krs', Session::get('statusKrs.status'));
        }

        if ( Session::has('statusKrs.angkatan') ) {
            $query->whereRaw("left(m2.nim,4) = '".Session::get('statusKrs.angkatan')."'");
        }

        if ( Session::has('statusKrs.prodi') ) {
            $query->where('m2.id_prodi', Session::get('statusKrs.prodi'));
        } else {
            $query->whereIn('m2.id_prodi', Sia::getProdiUser());
        }

        return $query;
    }

    public function update(Request $r)
    {

        try {
            DB::transaction(function()use($r){

                DB::table('krs_status')
                    ->where('id', $r->id)
                    ->update(['status_krs' => $r->status]);

                $krs = DB::table('nilai as n')
                        ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                        ->where('jdk.id_smt', Sia::sessionPeriode())
                        ->where('n.id_mhs_reg', $r->id_mhs_reg)
                        ->pluck('n.id');

                DB::table('nilai')->whereIn('id', $krs)->delete();
            });

        } catch(\Exception $e) {
            Rmt::error('Gagal menyimpan data. '.$e->getMessage());
            return redirect()->back();
        }

        Rmt::success('Berhasil mengubah data');

        return redirect()->back();
    }

}