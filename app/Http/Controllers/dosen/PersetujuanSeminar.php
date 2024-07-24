<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;
use App\Seminar;
use DB, Sia, Rmt, Response, Session;

trait PersetujuanSeminar
{

    public function seminar(Request $r)
    {
        if ( !Session::has('sem') ) {
            Session::put('sem.smt', Sia::sessionPeriode());
        }

        if ( $r->pencarian ) {
            if ( !empty($r->cari) ) {
                Session::put('sem.cari',$r->cari);
            } else {
                Session::pull('sem.cari');
            }

            return redirect()->route('dsn_approv_seminar');
        }

        // Data pada tabel seminar_pendaftaran
        $query = DB::table('seminar_pendaftaran as sp')
                    ->join('penguji as p', function($join){
                        $join->on('sp.id_mhs_reg', 'p.id_mhs_reg')
                            ->on('sp.jenis', 'p.jenis')
                            ->on('sp.id_smt', 'p.id_smt')
                            ->where('p.id_dosen', Sia::sessionDsn());
                    })
                    ->leftJoin('mahasiswa_reg as m1', 'sp.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('sp.id','sp.id_mhs_reg', 'sp.jenis', 'm1.nim', 'm2.nm_mhs','p.setuju')
                    ->where('sp.id_smt', Session::get('sem.smt'));

        $this->filter($query);

        $data['seminar'] = $query->paginate(20);

        return view('dsn.seminar.index', $data);
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('sem.'.$r->modul);
            } else {
                Session::put('sem.'.$r->modul, $r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull('sem');
        }

        return redirect(route('dsn_approv_seminar'));
    }

    public function filter($query)
    {
        if ( Session::has('sem.status') ) {
            $query->where('p.setuju', Session::get('sem.status'));
        }

        if ( Session::has('sem.jenis') ) {
            $query->where('sp.jenis', Session::get('sem.jenis'));
        }

        if ( Session::has('sem.cari') ) {
            $query->where(function($q) {
                $q->where('m1.nim','LIKE','%'.Session::get('sem.cari').'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.Session::get('sem.cari').'%');
            });
        }

    }

    public function seminarDetail(Request $r, $id)
    {
        $seminar = DB::table('seminar_pendaftaran as sp')
                    ->leftJoin('mahasiswa_reg as m1', 'sp.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('sp.id','sp.id_smt', 'sp.id_mhs_reg', 'sp.id_smt', 'sp.jenis', 'm1.nim', 'm2.nm_mhs')
                    ->where('sp.id', $id)
                    ->first();

        $data['ujian'] = DB::table('ujian_akhir')
                                ->where('id_mhs_reg', $seminar->id_mhs_reg)
                                ->where('jenis', $seminar->jenis)
                                ->where('id_smt', $seminar->id_smt)
                                ->first();

        $data['penguji'] = DB::table('penguji as p')
                            ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                            ->select('p.*', DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as nm_dosen'),'p.nilai','d.ttd')
                            ->where('p.id_mhs_reg', $seminar->id_mhs_reg)
                            ->where('p.jenis', $seminar->jenis)
                            ->where('p.id_smt', $seminar->id_smt)
                            ->get();

        $data['sem'] = $seminar;

        return view('dsn.seminar.detail', $data);
    }

    public function seminarUpdate(Request $r)
    {
        try {

            $data = ['setuju' => $r->disetujui];

            DB::table('penguji')->where('id', $r->id)
                ->update($data);

            Rmt::success('Berhasil mengupdate data');

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

}