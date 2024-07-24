<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, Carbon, Excel;
use App\Aktivitas;

class AktivitasController extends Controller
{
    public function index(Request $r)
    {
        $data['mahasiswa'] = Sia::akm()->paginate(15);

        return view('aktivitas.index', $data);
    }

    public function filter(Request $r)
    {
        if ( $r->ajax() ) {
            Sia::filter($r->value,'akm_'.$r->modul);
        } else {
            Session::pull('akm_ta');
            Session::pull('akm_angkatan');
            Session::pull('akm_prodi');
            Session::pull('akm_status');
            Session::pull('akm_jns_daftar');
            Session::pull('akm_search');
        }
        
        return redirect(route('akm'));
    }

    public function cari(Request $r)
    {
        if ( !empty($r->q) ) {
            Session::put('akm_search',$r->q);
        } else {
            Session::pull('akm_search');
        }

        return redirect(route('akm'));
    }

    public function add(Request $r)
    {
        return view('aktivitas.add');
    }

    public function mhs(Request $r )
    {
        $param = $r->input('query');
        if ( !empty($param) ) {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            // ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('m1.id_prodi', Sia::getProdiUser())
                            ->where(function($q)use($param){
                                $q->where('m1.nim', 'like', '%'.$param.'%')
                                    ->orWhere('m2.nm_mhs', 'like', '%'.$param.'%');
                            })
                            ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
        } else {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            // ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('m1.id_prodi', Sia::getProdiUser())
                            ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
        }

        $data = [];
        foreach( $mahasiswa as $r ) {
            $data[] = ['data' => $r->id, 'value' => $r->nim.' - '.$r->nm_mhs];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'mahasiswa' => 'required',
            'status' => 'required'
        ]);

        $rule = Aktivitas::where('id_mhs_reg', $r->mahasiswa)
                            ->where('id_smt', Sia::sessionPeriode())->count();

        if ( $rule > 0 ) {
            return Response::json(['error' => 1, 'msg' => 'Aktivitas mahasiswa ini telah ada pada semester ini']);
        }

        try {
            $data = new Aktivitas;
            $data->id_smt = Sia::sessionPeriode();
            $data->id_mhs_reg = $r->mahasiswa;
            $data->ips = !empty($r->ips) ? str_replace(',', '.', $r->ips) : '';
            $data->ipk = !empty($r->ipk) ? str_replace(',', '.', $r->ipk) : '';
            $data->sks_smt = $r->sks_semester;
            $data->sks_total = $r->sks_total;
            $data->status_mhs = $r->status;
            $data->save();
         } 
         catch(\Exception $e)
         {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
         }

         Rmt::success('Berhasil menyimpan data');
         return Response::json(['error' => 0, 'msg' => ''], 200);
    }

    public function edit(Request $r)
    {
        $data['mhs'] = Sia::akm(false)->where('akm.id', $r->id)->first();

        return view('aktivitas.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'mahasiswa' => 'required',
            'status' => 'required'
        ]);
        
        try {

            $data = Aktivitas::find($r->id);

            $rule = Aktivitas::where('id_mhs_reg', $r->mahasiswa)
                            ->where('id_smt', $data->id_smt)
                            ->where('id', '<>', $r->id)->count();

            if ( $rule > 0 ) {
                return Response::json(['error' => 1, 'msg' => 'Aktivitas mahasiswa ini telah ada pada semester ini']);
            }

            $data->id_mhs_reg = $r->mahasiswa;
            $data->ips = str_replace(',', '.', $r->ips);
            $data->ipk = str_replace(',', '.', $r->ipk);
            $data->sks_smt = $r->sks_semester;
            $data->sks_total = $r->sks_total;
            $data->status_mhs = $r->status;
            $data->save();
         }
         catch(\Exception $e)
         {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
         }

         Rmt::success('Berhasil menyimpan data');
         return Response::json(['error' => 0, 'msg' => ''], 200);
    }

    public function hitungAkm(Request $r)
    {
        $data['prodi'] = Sia::getProdiUser();
        $smt = Sia::sessionPeriode();
        $data['max_angkatan'] = substr($smt,0,4);

        $angkatan_1 = DB::table('mahasiswa_reg')
                ->where('id_jenis_keluar', 0)
                // ->where('semester_keluar', '<', Sia::sessionPeriode())
                ->where('id_prodi', isset($r->prodi) ? $r->prodi : $data['prodi'][0])
                ->min('semester_mulai');

                $data['angkatan_1'] = empty($angkatan_1) ? date('Y') : substr($angkatan_1,0,4);
                
        if ( !isset($r->prodi) || !isset($r->angkatan) ) {
            return redirect(route('akm_hitung',['prodi' => @$data['prodi'][0], 'angkatan' => $data['max_angkatan']]));
        }

        $data['mahasiswa'] = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('m1.id','m1.nim','m2.nm_mhs','m1.semester_keluar',
                        DB::raw('(SELECT sum(mk.sks_mk) from nilai_mbkm as nm
                          left join matakuliah as mk on mk.id = nm.id_mk
                          where nm.id_mhs_reg = m1.id) as mbkm'),
                        DB::raw('(SELECT sum(mk.sks_mk * nm.nil_indeks) from nilai_mbkm as nm
                                  left join matakuliah as mk on mk.id = nm.id_mk
                                  where nm.id_mhs_reg = m1.id) as kumulatif1'),
                        DB::raw('(select sum(n.nilai_indeks * mk.sks_mk) from nilai as n
                                    left join jadwal_kuliah as jdk on jdk.id=n.id_jdk
                                    left join matakuliah as mk on jdk.id_mk=mk.id
                                    where n.id_mhs_reg=m1.id and jdk.id_smt=' . $smt . ' and jdk.jenis = 1)
                                as kumulatif2'),
                        DB::raw('(SELECT sum(mk.sks_mk) from nilai as nil 
                                    left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
                                    left join matakuliah as mk on jdk.id_mk = mk.id
                                    where nil.id_mhs_reg=m1.id
                                        and jdk.id_smt = '.$smt.' and jdk.jenis = 1) 
                                as sks_smt'),
                        DB::raw('(SELECT sum(mk.sks_mk) from nilai as nil 
                                    left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
                                    left join matakuliah as mk on jdk.id_mk = mk.id
                                    where nil.id_mhs_reg=m1.id
                                    and nil.nilai_indeks!="")
                                as sks_total'),
                        DB::raw('(select sum(nm.nil_indeks) from nilai_mbkm as nm where nm.id_smt = '.Sia::sessionPeriode().' and nm.id_mhs_reg = m1.id) as status_mbkm'),
                        DB::raw('(select sum(n.nilai_indeks * mk.sks_mk)/sum(mk.sks_mk) as ips from nilai as n
                                    left join jadwal_kuliah as jdk on jdk.id=n.id_jdk
                                    left join matakuliah as mk on jdk.id_mk=mk.id
                                    where n.id_mhs_reg=m1.id and jdk.id_smt='.$smt.' and jdk.jenis = 1)
                                as ips'),
                        DB::raw('(SELECT status_mhs FROM aktivitas_kuliah
                                    where id_mhs_reg=m1.id
                                    and id_smt='.Sia::sessionPeriode().') as akm'))
                        ->whereRaw('left(semester_mulai,4)='.$r->angkatan)
                        ->where(function($q){
                            $q->where('m1.id_jenis_keluar', 0)
                                ->orWhere('m1.semester_keluar', Sia::sessionPeriode());
                        })
                        ->where('m1.id_prodi', $r->prodi)
                        ->orderBy('m1.nim')
                        ->get();

        return view('aktivitas.hitung-akm', compact('data'));
    }

    public function hitungAkmSp(Request $r)
    {
        $prodi =  isset($r->prodi) ? $r->prodi : '61201';
        $smt = Sia::sessionPeriode();

        $in_sp = DB::select("SELECT n.id_mhs_reg
                    FROM jadwal_kuliah as jdk 
                    join nilai as n on jdk.id = n.id_jdk
                    join mahasiswa_reg as m on m.id = n.id_mhs_reg
                    where jdk.id_smt=$smt and jdk.jenis=2
                    and jdk.id_prodi = $prodi
                    group by n.id_mhs_reg
                    order by m.nim");

        $in_sp_ = [];
        foreach ( $in_sp as $sp ) {
            $in_sp_[] = $sp->id_mhs_reg;
        }

        $data['mahasiswa'] = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('m1.id','m1.nim','m2.nm_mhs','m1.semester_keluar',
                        DB::raw('(SELECT sum(mk.sks_mk) from nilai as nil 
                                    left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
                                    left join matakuliah as mk on jdk.id_mk = mk.id
                                    where nil.id_mhs_reg=m1.id
                                        and jdk.jenis = 2
                                        and jdk.id_smt = '.$smt.') 
                                as sks_smt'),
                        DB::raw('(SELECT sum(mk.sks_mk) from nilai as nil 
                                    left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
                                    left join matakuliah as mk on jdk.id_mk = mk.id
                                    where nil.id_mhs_reg=m1.id
                                    and nil.nilai_indeks!="")
                                as sks_total'),
                        DB::raw('(select sum(n.nilai_indeks * mk.sks_mk)/sum(mk.sks_mk) as ips from nilai as n
                                    left join jadwal_kuliah as jdk on jdk.id=n.id_jdk
                                    left join matakuliah as mk on jdk.id_mk=mk.id
                                    where n.id_mhs_reg=m1.id and jdk.id_smt='.$smt.' and jdk.jenis = 2)
                                as ips'))
                        ->where(function($q){
                            $q->where('m1.id_jenis_keluar', 0)
                                ->orWhere('m1.semester_keluar', Sia::sessionPeriode());
                        })
                        ->where('m1.id_prodi', $prodi)
                        ->where('m1.id_jenis_keluar', 0)
                        ->whereIn('m1.id', $in_sp_)
                        ->orderBy('m1.nim', 'asc')
                        ->get();

        return view('aktivitas.akm-sp', $data);
    }

    public function storeArr(Request $r)
    {

        try {

            DB::beginTransaction();

            $test = [];

                foreach( $r->status as $key => $val ) {

                    $akm = Aktivitas::where('id_smt', Sia::sessionPeriode())
                            ->where('id_mhs_reg', $r->id_mhs_reg[$key])
                            ->first();

                    if ( !empty($akm) ) {
                        
                        $data = Aktivitas::find($akm->id);
                        $data->ips = $r->ips[$key];
                        $data->ipk = $r->ipk[$key];
                        $data->sks_smt = $r->sks_smt[$key];
                        $data->sks_total = $r->sks_total[$key];
                        $data->status_mhs = $r->status[$key];

                    } else {

                        $data = new Aktivitas;
                        $data->id_smt = Sia::sessionPeriode();
                        $data->id_mhs_reg = $r->id_mhs_reg[$key];
                        $data->ips = $r->ips[$key];
                        $data->ipk = $r->ipk[$key];
                        $data->sks_smt = $r->sks_smt[$key];
                        $data->sks_total = $r->sks_total[$key];
                        $data->status_mhs = $r->status[$key];
                    }

                    $data->save();

                }

            DB::commit();
            Rmt::success('Berhasil menyimpan data');
            return redirect()->back();

        } catch(\Exception $e) {
            DB::rollback();
            Rmt::error('Gagal menyimpan : '.$e->getMessage());
            return redirect()->back();
        }
    }

    public function cetak()
    {
        $data['mahasiswa'] = Sia::akm()->get();

        return view('aktivitas.cetak', $data);
    }

    public function excelFeeder()
    {
        $data['mahasiswa'] = Sia::akm()->orderBy('m1.nim','asc')->get();

        try {
            Excel::create('Aktivitas', function($excel)use($data) {

                $excel->sheet('New sheet', function($sheet)use($data) {

                    $sheet->loadView('aktivitas.excel', $data);

                });

            })->download('xlsx');
        } catch(\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function delete($id)
    {
        Aktivitas::find($id)->delete();

        Rmt::success('Berhasil menghapus data');
        return redirect()->back();
    }

}
