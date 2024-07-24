<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use App\Kurikulum, App\Matakuliah;
use DB, Sia, Rmt, Excel, Response, Session;

class KurikulumController extends Controller
{
    public function index(Request $r)
    {
    	$query = Sia::kurikulum();

    	$data['kurikulum'] = $query->paginate(10);
    	return view('kurikulum.index', $data);
    }

    public function detail($id)
    {
    	$data['kur'] = Sia::kurikulumFirst($id);
    	$data['mk_kur'] = Sia::matakuliahKurikulum($id);
    	return view('kurikulum.detail', $data);
    }

    public function matakuliah(Request $r)
    {
		$param = $r->input('query');
		if ( !empty($param) ) {
			$matakuliah = Matakuliah::where('id_prodi', $r->prodi)
							->where(function($q)use($param){
								$q->where('nm_mk','like','%'.$param.'%')
								->orWhere('kode_mk','like','%'.$param.'%');
							})->orderBy('nm_mk','asc')->get();
		} else {
			$matakuliah = Matakuliah::where('id_prodi',$r->prodi)->get();
		}
		$data = [];
		foreach( $matakuliah as $r ) {
			$data[] = ['data' => $r->id, 'value' => $r->kode_mk.' - '.trim($r->nm_mk).' ('.$r->sks_mk.' sks)'];
		}
		$response = ['query' => 'Unit', 'suggestions' => $data];
		return Response::json($response,200);
    }

    public function cari(Request $r)
    {
		if ( !empty($r->q) ) {
			Session::put('kur_search',$r->q);
		} else {
			Session::pull('kur_search');
		}

		return redirect(route('kurikulum'));
    }

    public function filter(Request $r)
    {
		if ( $r->ajax() ) {
			Sia::filter($r->value,'kur_'.$r->modul);
		} else {
			Session::pull('kur_search_');
			Session::pull('kur_prodi');
		}
		
		return redirect(route('kurikulum'));
    }

    public function eksporPrint(Request $r)
    {
		$query = Sia::matakuliah();

    	$data['matakuliah'] = $query->get();
    	return view('matakuliah.print', $data);
    }

    public function eksporExcel(Request $r)
    {
    	$query = Sia::matakuliah();
    	$data['matakuliah'] = $query->get();

		try {
			Excel::create('Matakuliah', function($excel)use($data) {

			    $excel->sheet('New sheet', function($sheet)use($data) {

			        $sheet->loadView('matakuliah.excel', $data);

			    });

			})->download('xlsx');;
		} catch(\Exception $e) {
			echo $e->getMessage();
		}
    }

    public function add(Request $r)
    {
    	return view('kurikulum.add');
    }

    public function store(Request $r)
    {
    	$this->validate($r, [
    		'nm_kurikulum' => 'required',
    		'prodi' => 'required',
    		'sks_wajib' => 'numeric',
    		'sks_pilihan' => 'numeric',
            'berlaku' => 'required'
    	]);

    	try {

    		DB::transaction(function()use($r){

    			$kur = new Kurikulum;
				$kur->id = Rmt::uuid();
				$kur->nm_kurikulum = $r->nm_kurikulum;
                $kur->id_prodi = $r->prodi;
				$kur->mulai_berlaku = $r->berlaku;
				$kur->aktif = $r->aktif;
				$kur->jml_sks_wajib = (int)$r->sks_wajib;
				$kur->jml_sks_pilihan = (int)$r->sks_pilihan;
				$kur->jml_sks_lulus = (int)$r->sks_wajib + (int)$r->sks_pilihan;
				$kur->save();
    		});

    	} catch(\Exception $e) {
    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
    	}

    	Rmt::success('Berhasil menyimpan data');

    	return Response::json(['error' => 0,'msg' => 'sukses'], 200);
    }

    public function edit($id)
    {
    	$data['kur'] = Kurikulum::find($id);
    	return view('kurikulum.edit', $data);
    }

    public function update(Request $r)
    {
    	$this->validate($r, [
    		'nm_kurikulum' => 'required',
    		'prodi' => 'required',
    		'sks_wajib' => 'numeric',
    		'sks_pilihan' => 'numeric',
            'berlaku' => 'required'
    	]);

    	try {

    		DB::transaction(function()use($r){

    			$kur = Kurikulum::find($r->id);
				$kur->nm_kurikulum = $r->nm_kurikulum;
				$kur->id_prodi = $r->prodi;
                $kur->mulai_berlaku = $r->berlaku;
				$kur->aktif = $r->aktif;
				$kur->jml_sks_wajib = (int)$r->sks_wajib;
				$kur->jml_sks_pilihan = (int)$r->sks_pilihan;
				$kur->jml_sks_lulus = (int)$r->sks_wajib + (int)$r->sks_pilihan;
				$kur->save();
    		});

    	} catch(\Exception $e) {
    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
    	}

    	Rmt::success('Berhasil menyimpan data');

    	return Response::json(['error' => 0,'msg' => 'sukses'], 200);
    }

    public function delete($id)
    {
        $count_mk = DB::table('mk_kurikulum')->where('id_kurikulum',$id)->count();
        $count_mhs = DB::table('mahasiswa_reg')->where('id_kurikulum',$id)->count();
        if ( $count_mk + $count_mhs > 0 ) {
            Rmt::error('Gagal menghapus, ada matakuliah/mahasiswa yang memakai kurikulum ini');
            return redirect()->back();
        }

    	DB::table('kurikulum')->where('id',$id)->delete();
    	Rmt::success('Berhasil Menghapus data');
    	return redirect()->back();
    }

    /* Matakuliah kurikulum */
    	public function mkStore(Request $r)
    	{
    		$this->validate($r,[
    			'matakuliah' => 'required',
    			'semester' => 'required',
    		]);

    		try {
	    		DB::transaction(function()use($r){

                    $periode = $r->semester % 2 == 0 ? 2 : 1;

	    			$data = [
	    				'id' => Rmt::uuid(),
	    				'id_kurikulum' => $r->id_kurikulum,
	    				'id_mk' => $r->matakuliah,
                        'periode' => $periode,
	    				'smt' => $r->semester
	    			];
	    			DB::table('mk_kurikulum')->insert($data);
	    		});
	    	} catch( \Exception $e ) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage() ], 200);
	    	}

	    	Rmt::success('Berhasil menyimpan data');
	    	return Response::json(['error' => 0, 'msg' => $r->id_kurikulum], 200);

    	}

        public function mkAdd($id)
        {
            $data['kur'] = Sia::kurikulumFirst($id);
            $data['matakuliah'] = DB::table('matakuliah as mk')
                        ->leftJoin('prodi as pr', 'mk.id_prodi', '=', 'pr.id_prodi')
                        ->select('mk.*','pr.nm_prodi','pr.jenjang',
                            DB::raw('(SELECT smt from mk_kurikulum
                                where id_mk = mk.id and id_kurikulum = \''.$id.'\') as mk_smt'),
                            DB::raw('(SELECT periode from mk_kurikulum
                                where id_mk = mk.id and id_kurikulum = \''.$id.'\') as mk_periode'))
                        ->where('mk.id_prodi', $data['kur']->id_prodi)
                        ->orderBy('mk_smt','asc')->get();

            return view('kurikulum.mk-add', $data);
        }

        public function mkStoreArr(Request $r)
        {
            $this->validate($r, [
                'matakuliah' => 'required'
            ]);

            try {
                foreach( $r->matakuliah as $key => $value ) {
                    $periode = $r->smt[$key] % 2 == 0 ? 2 : 1;
                    $data[] = [
                            'id' => Rmt::uuid(),
                            'id_kurikulum' => $r->id_kurikulum,
                            'id_mk' => $value,
                            'periode' => $periode,
                            'smt' => $r->smt[$key]
                        ];
                }

                if ( $data ) {
                    DB::transaction(function()use($r,$data){
                        DB::table('mk_kurikulum')->where('id_kurikulum', $r->id_kurikulum)->delete();
                        DB::table('mk_kurikulum')->insert($data);
                    });
                } else {
                    return Response::json(['error' => 1, 'msg' => 'Terjadi kesalahan ulangi lagi'],200);
                }

            } catch(\Exception $e) {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
            }

            Rmt::success('Berhasil menyimpan data');
            return Response::json(['error' => 0, 'msg' => $r->id_kurikulum], 200);
        }

        public function mkSalin(Request $r)
        {
            $this->validate($r, ['id_kurikulum' => 'required']);

            $mk = DB::table('mk_kurikulum')->where('id_kurikulum', $r->id_kurikulum)->get();
 
            if ( count($mk) == 0 ) {
                return Response::json(['error' => 1, 'msg' => 'Tidak terdapat matakuliah pada kurikulum yang ingin anda salin']);
            }

            foreach( $mk as $m ) {
                $data[] = [
                    'id' => Rmt::uuid(),
                    'id_kurikulum' => $r->kurikulum_tujuan,
                    'id_mk' => $m->id_mk,
                    'periode' => $m->periode,
                    'smt' => $m->smt
                ];
            }

            try {
                DB::transaction(function()use($data){
                    DB::table('mk_kurikulum')->insert($data);
                });
            } catch(\Exception $e) {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
            }

            Rmt::success('Berhasil menyalin matakuliah');
            return Response::json(['error' => 0, 'msg' => ''], 200);
        }   

    	public function mkDelete($id)
    	{
    		try {
	    		DB::table('mk_kurikulum')->where('id',$id)->delete();
	    		Rmt::success('Berhasil menghapus data');
    			return redirect()->back();
	    	} catch(\Exception $e) {
	    		Rmt::error($e->getMessage());
	    		return redirect()->back();
	    	}
    	}
    /* end matakuliah kurikulum */
}
