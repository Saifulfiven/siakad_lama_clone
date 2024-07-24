<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sia, DB, Rmt, Session, Response, Excel;
use App\Mahasiswareg;

class KelasController extends Controller
{

	public function __construct()
	{
		if ( !Session::has('non_kelas') ) {
			Session::put('non_kelas', 0);
    	}
	}

    public function index()
    {

    	$query = Sia::mahasiswa()
				->select('m2.id_prodi','m2.id as id_mhs_reg', 'm2.kode_kelas', 'm2.nim', 'm1.nm_mhs', 'p.nm_prodi', 'p.jenjang')
				->orderBy('m2.nim', 'desc');
		
		$this->filter($query);

		$data['mahasiswa'] = $query->paginate(10);

        return view('kelas.index', $data);
    }

    public function update(Request $r)
    {
    	if ( strlen(trim($r->value)) > 5 ) {
    		return Response::json(['Kode kelas maksimal 5 karakter'], 402);
    	}

        $cek = Mahasiswareg::where('kode_kelas', $r->value)
                ->where('id_jenis_keluar', 0)
                ->count();

        if ( $cek > 40 ) {
            return Response::json(['Peserta Kelas: '.$r->value.' telah penuh. (40 Peserta)'], 402);
        }

    	$data = Mahasiswareg::find($r->pk);
    	$data->kode_kelas = trim($r->value);
    	$data->save();

    	return Response::json(['']);
    }

    public function cari(Request $r)
    {
    	Session::pull('non_kelas');

        if ( !empty($r->cari) ) {
            Session::put('kls.cari',$r->cari);
        } else {
            Session::pull('kls.cari');
        }

        return redirect(route('kelas'));
    }

    public function setFilter(Request $r)
    {
        Session::pull('kls');
        Session::pull('non_kelas');

        if ( is_array($r->prodi) && count($r->prodi) > 0 ) {
            foreach( $r->prodi as $pr ) {
                Session::push('kls.prodi', $pr);
            }
        }
        
        if ( is_array($r->kelas) && count($r->kelas) > 0 ) {
            foreach( $r->kelas as $kls ) {
                Session::push('kls.kode', $kls);
            }
        }

        if ( $r->remove ) {
            Session::pull('kls');
        }

        return redirect()->back();
    }

    public function nonKelasFilter(Request $r)
    {
    	if ( $r->value == 'true' ) {
    		Session::put('non_kelas', 1);
    	} else {
    		Session::put('non_kelas', 0);
    	}
    }

    public function filter($query)
    {

        if ( Session::has('kls.cari') ) {
            $query->where(function($q){
                $q->where('m2.nim', 'like', '%'.Session::get('kls.cari').'%')
                    ->orWhere('m1.nm_mhs', 'like', '%'.Session::get('kls.cari').'%')
                    ->orWhere('m2.kode_kelas', 'like', '%'.Session::get('kls.cari').'%');
            });
        }

        if ( Session::has('kls.prodi') ) {
            $query->whereIn('m2.id_prodi',Session::get('kls.prodi'));
        } else {
            $query->whereIn('m2.id_prodi', Sia::getProdiUser());
        }

        if ( Session::has('kls.kode') ) {
            $query->whereIn('m2.kode_kelas',Session::get('kls.kode'));
        }

        if ( Session::get('non_kelas') == 1 ) {
        	$query->where(function($q){
        		$q->where('m2.kode_kelas', '')
        			->orWhereNull('m2.kode_kelas');
        	});
        }
    }

		public function prin(Request $r)
		{
			$query = Sia::mahasiswa()
				->select('m2.id_prodi','m2.id as id_mhs_reg', 'm2.kode_kelas', 'm2.nim', 'm1.nm_mhs', 'p.nm_prodi', 'p.jenjang')
				->orderBy('m2.nim', 'desc');
			
			$this->filter($query);

			$data['mahasiswa'] = $query->get();

			return view('kelas.print', $data);
		}

		public function ekspor(Request $r)
		{
			$query = Sia::mahasiswa()
				->select('m2.id_prodi','m2.id as id_mhs_reg', 'm2.kode_kelas', 'm2.nim', 'm1.nm_mhs', 'p.nm_prodi', 'p.jenjang')
				->orderBy('m2.nim', 'desc');
			
			$this->filter($query);

			$data['mahasiswa'] = $query->get();

			try {
				Excel::create('Kelas Mahasiswa', function($excel)use($data) {

				    $excel->sheet('New sheet', function($sheet)use($data) {

				        $sheet->loadView('kelas.excel', $data);

				    });

				})->download('xlsx');;
			} catch(\Exception $e) {
				echo $e->getMessage();
			}

		}

}