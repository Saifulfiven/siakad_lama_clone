<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use DB, Sia, Rmt, Response, Session;

class DaftarSpController extends Controller
{
    public function index(Request $r)
    {
    	if ( !Session::has('daftar_sp') ) {
	    	Session::put('daftar_sp.cari','');
            Session::put('daftar_sp.smt', Sia::sessionPeriode());
	    }

    	$query = DB::table('daftar_sp as sp')
    			->leftJoin('mahasiswa_reg as m1', 'm1.id', 'sp.id_mhs_reg')
    			->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
    			->leftJoin('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
    			->leftJoin('semester as smt', 'smt.id_smt', 'sp.id_smt')
    			->select('sp.id', 'sp.jml_sks', 'm1.nim','m2.nm_mhs', 'pr.nm_prodi',
    					'pr.jenjang', 'smt.id_smt', 'smt.nm_smt',
    					DB::raw('(select count(id_mhs_reg) from krs_status
                                    where id_smt=sp.id_smt
                                    and jenis=\'SP\'
                                    and id_mhs_reg=sp.id_mhs_reg) as sudah_bayar'));

    	$this->doFilter($query);


    	$data['mahasiswa'] = $query->paginate(10);
    	return view('daftar-sp.index', $data);
    }

    private function doFilter($query)
    {
    	if ( Session::has('daftar_sp.cari') ) {
			$query->where(function($q){
				$q->where('m1.nim', 'like', '%'.Session::get('daftar_sp.cari').'%')
					->orWhere('m2.nm_mhs', 'like', '%'.Session::get('daftar_sp.cari').'%');
			});
		}

		if ( Session::has('daftar_sp.smt') ) {
			$query->where('sp.id_smt', Session::get('daftar_sp.smt'));
		}

		if ( Session::has('daftar_sp.prodi') ) {
			$query->where('m1.id_prodi', Session::get('daftar_sp.prodi'));
		}
    }

	public function cari(Request $r)
	{
		if ( !empty($r->cari) ) {
			Session::put('daftar_sp.cari',$r->cari);
		} else {
			Session::pull('daftar_sp.cari');
		}

		return redirect(route('daftar_sp'));
    }

    public function filter(Request $r)
    {
		if ( !empty($r->modul) ) {

			if ( $r->val == 'all' ) {
				Session::pull('daftar_sp.'.$r->modul);
			} else {
				Session::put('daftar_sp.'.$r->modul,$r->val);
			}
		}

		if ( $r->remove ) {
			Session::pull('daftar_sp');
		}

		return redirect(route('daftar_sp'));
    }

    public function getMhs(Request $r )
    {
        $param = $r->input('query');
        if ( !empty($param) ) {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('id_prodi', Sia::getProdiUser())
                            ->where(function($q)use($param){
                                $q->where('m1.nim', 'like', '%'.$param.'%')
                                    ->orWhere('m2.nm_mhs', 'like', '%'.$param.'%');
                            })
                            ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
        } else {
            $mahasiswa = DB::table('mahasiswa_reg as m1')
                            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                            ->where('m1.id_jenis_keluar', 0)
                            ->whereIn('id_prodi', Sia::getProdiUser())
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
    		'jml_sks' => 'numeric|min:1|max:9'
    	]);

    	try{
	    	DB::transaction(function()use($r){
	    		$data = [
	    			'id_mhs_reg' => $r->mahasiswa,
	    			'id_smt' => $r->id_smt,
	    			'jml_sks' => $r->jml_sks
	    		];

	    		DB::table('daftar_sp')->insert($data);
	    	});
	    } catch( \Exception $e) {
	    	return Response::json(['error' => 1,'msg' => $e->getMessage()]);
	    }

	    Rmt::success('Berhasil menyimpan data');
	    return Response::json(['error' => 0, 'msg' => 'sukses']);
    }

    public function update(Request $r)
    {
    	$this->validate($r, [
    		'jml_sks' => 'required|numeric|min:1|max:9'
    	]);

        try {
            DB::transaction(function()use($r){

                $data = [
                    'jml_sks' => $r->jml_sks
                ];

                DB::table('daftar_sp')
                    ->where('id', $r->id)
                    ->update($data);

            });
        } catch( \Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }

    	Rmt::success('Berhasil menyimpan data');
    	return Response::json(['error' => 0, 'msg' => '']);
    }

    public function delete($id)
    {

        DB::table('daftar_sp')->where('id', $id)->delete();

	    Rmt::success('Berhasil menghapus data');
	    return redirect()->back();
    }
}
