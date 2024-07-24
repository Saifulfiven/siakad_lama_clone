<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Excel, Response, Session;
use App\konsentrasiPps, App\Mahasiswareg;

class PilihKonsentrasiController extends Controller
{
	
    public function index(Request $r)
    {
    	if ( !Session::has('konsentrasi') ) {
	    	Session::put('konsentrasi.smt', Sia::sessionPeriode());
	    }

    	$mhs = DB::table('konsentrasi_pps as kp')
    					->leftJoin('mahasiswa_reg as m1', 'kp.id_mhs_reg', 'm1.id')
    					->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
    					->leftJoin('konsentrasi as k', 'kp.id_konsentrasi', 'k.id_konsentrasi')
    					// ->where('kp.id_smt', Session::get('konsentrasi.smt'))
    					->select('kp.*', 'm2.nm_mhs','m1.nim','k.nm_konsentrasi');

    	$this->filter($mhs);

    	$data['mhs'] = $mhs->orderBy('m1.nim')->paginate(10);

    	return view('konsentrasi-pps.index', $data);
    }

    private function filter($query)
    {
    	if ( Session::has('konsentrasi.cari') ) {
			$query->where(function($q){
				$q->where('m1.nim', 'like', '%'.Session::get('konsentrasi.cari').'%')
					->orWhere('m2.nm_mhs', 'like', '%'.Session::get('konsentrasi.cari').'%')
					->orWhere('k.nm_konsentrasi', 'like', '%'.Session::get('konsentrasi.cari').'%');
			});
		}

		if ( Session::has('konsentrasi.konsentrasi') ) {
			$query->where('kp.id_konsentrasi', Session::get('konsentrasi.konsentrasi'));
		}

		if ( Session::has('konsentrasi.prodi') ) {
			$query->where('m1.id_prodi', Session::get('konsentrasi.prodi'));
		}

		if ( Session::has('konsentrasi.kelas') ) {
			$query->where('kp.kelas', Session::get('konsentrasi.kelas'));
		}

    }

	public function cari(Request $r)
	{
		if ( !empty($r->cari) ) {
			Session::put('konsentrasi.cari',$r->cari);
		} else {
			Session::pull('konsentrasi.cari');
		}

		return redirect(route('konsentrasi'));
    }

    public function setFilter(Request $r)
    {
		if ( !empty($r->modul) ) {

			if ( $r->val == 'all' ) {
				Session::pull('konsentrasi.'.$r->modul);
			} else {
				Session::put('konsentrasi.'.$r->modul,$r->val);
			}
		}

		if ( $r->remove ) {
			Session::pull('konsentrasi');
		}

		return redirect(route('konsentrasi'));
    }

    public function eksporExcel(Request $r)
    {

		$mhs = DB::table('konsentrasi_pps as kp')
				->leftJoin('mahasiswa_reg as m1', 'kp.id_mhs_reg', 'm1.id')
				->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
				->leftJoin('konsentrasi as k', 'kp.id_konsentrasi', 'k.id_konsentrasi')
				->where('kp.id_smt', Session::get('konsentrasi.smt'))
				->select('kp.*', 'm2.nm_mhs','m1.nim', 'k.nm_konsentrasi');

    	$this->filter($mhs);

    	$data['mhs'] = $mhs->orderBy('m1.nim')->get();

			try {
				Excel::create('Konsentrasi mahasiswa', function($excel)use($data) {

				    $excel->sheet('New sheet', function($sheet)use($data) {

				        $sheet->loadView('konsentrasi-pps.excel', $data);

				    });

				})->download('xlsx');
			} catch(\Exception $e) {
				echo $e->getMessage();
			}
    }

    public function eksporPrint(Request $r)
    {
    	$mhs = DB::table('konsentrasi_pps as kp')
				->leftJoin('mahasiswa_reg as m1', 'kp.id_mhs_reg', 'm1.id')
				->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
				->leftJoin('konsentrasi as k', 'kp.id_konsentrasi', 'k.id_konsentrasi')
				->where('kp.id_smt', Session::get('konsentrasi.smt'))
				->select('kp.*', 'm2.nm_mhs','m1.nim', 'k.nm_konsentrasi');

    	$this->filter($mhs);

    	$data['mhs'] = $mhs->orderBy('m1.nim')->get();

    	return view('konsentrasi-pps.print', $data);
    }

    public function add(Request $r)
    {
    	return view('konsentrasi-pps.add');
    }

	public function getMahasiswa(Request $r)
	{
		$param = $r->input('query');
		if ( !empty($param) ) {

			$mahasiswa = DB::table('mahasiswa_reg as m1')
							->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
							->where('m1.id_jenis_keluar', '0')
							->where('m1.id_prodi', 61101)
							->where(function($q)use($param){
								$q->where('m1.nim', 'like', '%'.$param.'%')
									->orWhere('m2.nm_mhs', 'like', '%'.$param.'%');
							})
							->select('m1.id', 'm2.nm_mhs','m1.nim')
							->take(10)->get();
		} else {
			$mahasiswa = DB::table('mahasiswa_reg as m1')
							->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
							->where('m1.id_jenis_keluar', '0')
							->where('m1.id_prodi', 61101)
							->select('m1.id', 'm2.nm_mhs','m1.nim')
							->take(10)->get();
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
    		'kelas' => 'required',
    		'konsentrasi' => 'required',
    	]);

		try {

            $data = new konsentrasiPps;
            $data->id_smt = $r->id_smt;
            $data->id_mhs_reg = $r->mahasiswa;
            $data->id_konsentrasi = $r->konsentrasi;
            $data->kelas = $r->kelas;
            $data->save();

            $mhs = Mahasiswareg::find($r->mahasiswa);
            $mhs->id_konsentrasi = $r->konsentrasi;
            $mhs->save();

            Rmt::success('Berhasil menyimpan data');

        } catch(\Exception $e) {
            return Response::json([$e->getMessage()], 422);
        }

	    Rmt::success('Berhasil menyimpan data');
	    return Response::json(['error' => 0, 'msg' => 'sukses']);
    }

    public function edit($id)
    {
    	$data['konsen'] = DB::table('konsentrasi_pps as kp')
				->leftJoin('mahasiswa_reg as m1', 'kp.id_mhs_reg', 'm1.id')
				->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
				->leftJoin('konsentrasi as k', 'kp.id_konsentrasi', 'k.id_konsentrasi')
				->where('kp.id', $id)
				->select('kp.*', 'm2.nm_mhs','m1.nim')
				->first();

    	return view('konsentrasi-pps.edit', $data);
    }

    public function update(Request $r)
    {
    	$this->validate($r, [
    		'kelas' => 'required',
    		'konsentrasi' => 'required',
    	]);

		try {

            $data = konsentrasiPps::find($r->id);
            $data->id_smt = $r->id_smt;
            $data->id_konsentrasi = $r->konsentrasi;
            $data->kelas = $r->kelas;
            $data->save();
            Rmt::success('Berhasil menyimpan data');

        } catch(\Exception $e) {
            return Response::json([$e->getMessage()], 422);
        }

	    Rmt::success('Berhasil menyimpan data');
	    return Response::json(['error' => 0, 'msg' => 'sukses']);
    }

    public function delete($id)
    {
    	konsentrasiPps::find($id)->delete();
	    Rmt::success('Berhasil menghapus data');
	    return redirect()->back();
    }
}
