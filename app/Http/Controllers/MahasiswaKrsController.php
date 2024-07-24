<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, QrCode, Excel;

class MahasiswaKrsController extends Controller
{
	
    public function index(Request $r)
    {
    	if ( empty(Session::get('mhs_krs.smt')) ) {
    		Session::put('mhs_krs.smt', Sia::sessionPeriode());
    	}

    	$sks_diprogram = '(SELECT sum(mk.sks_mk) FROM nilai as n
    						left join jadwal_kuliah as jdk on jdk.id = n.id_jdk
    						left join matakuliah as mk on mk.id = jdk.id_mk
    						where jdk.id_smt = ks.id_smt
    						and n.id_mhs_reg = m1.id ) as sks_diprogram';

        // $query = DB::table('nilai as n')
        //             ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
        //             ->leftJoin('mahasiswa_reg as m1', 'n.id_mhs_reg', 'm1.id')
        //             ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id');

    	$query = DB::table('krs_status as ks')
    				->leftJoin('mahasiswa_reg as m1', 'ks.id_mhs_reg', 'm1.id')
    				->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('semester as smt', 'ks.id_smt', 'smt.id_smt')
    				->select('ks.id', 'm1.id as id_mhs_reg', 'm1.nim', 'm1.kode_kelas','m2.nm_mhs', 'ks.id_smt', 
    						'ks.jalur', 'm2.id as id_mhs', 'smt.nm_smt', DB::raw($sks_diprogram))
    				->where('ks.status_krs', '1');
    				// ->where('ks.valid', '1');

    	if ( Session::has('mhs_krs.cari') ) {
			$query->where(function($q){
				$q->where('m1.nim', 'like', '%'.Session::get('mhs_krs.cari').'%')
					->orWhere('m2.nm_mhs', 'like', '%'.Session::get('mhs_krs.cari').'%');
			});
		}

		if ( Session::has('mhs_krs.jalur') ) {
			$query->where('ks.jalur', Session::get('mhs_krs.jalur'));
		}

        if ( Session::has('mhs_krs.prodi') ) {
            $query->where('m1.id_prodi', Session::get('mhs_krs.prodi'));
        }

		if ( Session::has('mhs_krs.smt') ) {
			$query->where('ks.id_smt', Session::get('mhs_krs.smt'));
		}

    	$data['mahasiswa'] = $query->paginate(10);

    	return view('mahasiswa-krs.index', $data);
    }

	public function cari(Request $r)
	{
		if ( !empty($r->cari) ) {
			Session::put('mhs_krs.cari',$r->cari);
		} else {
			Session::pull('mhs_krs.cari');
		}

		return redirect(route('mhs_krs_lap'));
    }

    public function filter(Request $r)
    {
		if ( !empty($r->modul) ) {

			if ( $r->val == 'all' ) {
				Session::pull('mhs_krs.'.$r->modul);
			} else {
				Session::put('mhs_krs.'.$r->modul,$r->val);
			}
		}

		if ( $r->remove ) {
			Session::pull('mhs_krs');
		}

		return redirect(route('mhs_krs_lap'));
    }

    public function rollback(Request $r)
    {
    	DB::table('krs_status')
    		->where('id', $r->id)
    		->update(['status_krs' => 0, 'valid' => 0]);
    	Rmt::success('Berhasil mengembalikan KRS');
    	return redirect()->back();
    }

    public function cetak(Request $r, $id)
    {
        $periode = $r->id_smt ? $r->id_smt : Session::get('mhs_krs.smt');
        $data['mhs'] = \App\Mahasiswareg::find($id);


        $krs_tmp = Sia::krsMhsTmp($id, $periode);
        $krs = Sia::krsMhs($id, $periode, 1)->get();

        if ( count($krs) == 0 ) {
            $data['krs'] = $krs_tmp;
        } else {
            $data['krs'] = $krs;
        }

        $qr = 'KRS-'.$data['mhs']->nim.','.$r->nama.','.$r->nm_periode;
            
        QrCode::generate($qr, storage_path().'/qr-code/'.$data['mhs']->nim.'.svg');

        return view('mahasiswa-krs.cetak', $data);
    }

	public function excel(Request $r)
	{

    	$sks_diprogram = '(SELECT sum(mk.sks_mk) FROM nilai as n
    						left join jadwal_kuliah as jdk on jdk.id = n.id_jdk
    						left join matakuliah as mk on mk.id = jdk.id_mk
    						where jdk.id_smt = ks.id_smt
    						and n.id_mhs_reg = m1.id ) as sks_diprogram';

    	$query = DB::table('krs_status as ks')
    				->leftJoin('mahasiswa_reg as m1', 'ks.id_mhs_reg', 'm1.id')
    				->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('semester as smt', 'ks.id_smt', 'smt.id_smt')
    				->select('ks.id', 'm1.id as id_mhs_reg', 'm1.nim', 'm1.kode_kelas','m2.nm_mhs', 'ks.id_smt', 
    						'ks.jalur', 'm2.id as id_mhs', 'smt.nm_smt', DB::raw($sks_diprogram))
    				->where('ks.status_krs', '1');
    				// ->where('ks.valid', '1');

    	if ( Session::has('mhs_krs.cari') ) {
			$query->where(function($q){
				$q->where('m1.nim', 'like', '%'.Session::get('mhs_krs.cari').'%')
					->orWhere('m2.nm_mhs', 'like', '%'.Session::get('mhs_krs.cari').'%');
			});
		}

		if ( Session::has('mhs_krs.jalur') ) {
			$query->where('ks.jalur', Session::get('mhs_krs.jalur'));
		}

        if ( Session::has('mhs_krs.prodi') ) {
            $query->where('m1.id_prodi', Session::get('mhs_krs.prodi'));
        }

		if ( Session::has('mhs_krs.smt') ) {
			$query->where('ks.id_smt', Session::get('mhs_krs.smt'));
		}

    	$data['mahasiswa'] = $query->get();

		try {
			Excel::create('Laporan KRS Mahasiswa', function($excel)use($data) {

			    $excel->sheet('New sheet', function($sheet)use($data) {

			        $sheet->loadView('mahasiswa-krs.excel', $data);

			    });

			})->download('xlsx');
		} catch(\Exception $e) {
			echo $e->getMessage();
		}

	}

}
