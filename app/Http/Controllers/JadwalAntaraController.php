<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use App\Dosen, App\JadwalKuliah, App\Nilai, App\KrsStatus;
use DB, Sia, Rmt, Excel, Response, Session, Carbon;

class JadwalAntaraController extends Controller
{	

	/* Jadwal */
	    public function index(Request $r)
	    {
	    	if ( !Session::has('jda_smt') ) {
	    		Session::put('jda_smt', [Sia::sessionperiode()]);
	    	}

	    	$query = Sia::jadwalAntara();

	    	$data['jadwal'] = $query->orderBy('created_at','desc')->paginate(10);
	    	return view('jadwal-antara.index', $data);
	    }

	    public function detail($id)
	    {
	    	$query = Sia::jadwalAntara();

	    	$data['r'] = $query->where('jdk.id',$id)->first();
	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();
	    	$data['peserta'] = Sia::pesertaKelas($data['r']->id);

	    	return view('jadwal-antara.detail', $data);

	    }

	    public function cari(Request $r)
	    {
				if ( !empty($r->q) ) {
					Session::put('jda_search',trim($r->q));
				} else {
					Session::pull('jda_search');
				}

				return redirect(route('jda'));
	    }

	    public function filter(Request $r)
	    {
			if ( $r->ajax() ) {
				Sia::filter($r->value,'jda_'.$r->modul);
			} else {
				Session::pull('jda_search');
				Session::pull('jda_prodi');
				Session::pull('jda_smt');
				Session::pull('jda_ket');
			}
			
			return redirect(route('jda'));
	    }

	    public function eksporPrint(Request $r)
	    {
				$query = Sia::jadwalAntara();

	    	$data['jadwal'] = $query->where('jdk.id_jam','<>',0)->get();
	    	return view('jadwal-antara.print', $data);
	    }

	    public function add(Request $r)
	    {
	    	if ( Session::has('dosen_ke') ) {
	    		Session::pull('dosen_ke');
	    	}

	    	if ( !Sia::isGenap() ) {
	    		echo 'Anda hanya dapat menambah jadwal antara pada periode genap';
	    		exit;
	    	}

	    	return view('jadwal-antara.add');
	    }

	    public function ajax(Request $r)
	    {
	    	switch ($r->tipe) {
	    		case 'jam':
	    			if ( empty($r->prodi) ) { ?>
	    				<select class="form-control mw-3" disabled="">
	                        <option value="">-- Jam --</option>
	                    </select>
	    			<?php } else { ?>
	    			
		                <select class="form-control mw-3" name="id_jam">
		                    <option value="">-- Jam --</option>
		                    <?php $jamkul = Sia::jamKuliah($r->prodi) ?>
		                    <?php foreach( $jamkul as $j ) { ?>
		                        <option value="<?= $j->id ?>"><?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?> (<?= $j->ket ?>)</option>
		                    <?php } ?>
		                </select>

	                <?php } ?>

	    		<?php break;
	    		
	    		default:
	    			# code...
	    			break;
	    	}
	    }

	    public function addDosen(Request $r) 
	    { 

	    	$id_form = Rmt::uuid();

	    	if ( Session::has('dosen_ke') ) {
	    		$dosen_ke = Session::get('dosen_ke') + 1;
	    		Session::put('dosen_ke', $dosen_ke);
	    	} else {
	    		Session::put('dosen_ke', 2);
	    		$dosen_ke = 2;
	    	} ?>

	    	<hr>
	    	<h5>Dosen ke-<?= $dosen_ke ?></h5>
	    	<table border="0" class="table-hover table-form" width="100%">
	            <tr>
	                <td width="150px">Nama Dosen <span>*</span></td>
	                <td>
	                    <div style="position: relative;">
	                        <div class="input-icon right"> 
	                            <span id="<?= $id_form ?>-spinner" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
	                            <input type="text" class="form-control" id="<?= $id_form ?>">
	                            <input type="hidden" id="<?= $id_form ?>-input" name="dosen[]">
	                        </div>
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <td>Jumlah rencana tatap muka <span>*</span></td>
	                <td>
	                    <input type="number" maxlength="2" size="2" class="form-control mw-1" name="tatap_muka[]" value="14">
	                </td>
	            </tr>
	            <tr>
	                <td>Jumlah realisasi tatap muka</td>
	                <td>
	                    <input type="number" maxlength="2" size="2" class="form-control mw-1" name="real_tm[]" value="14">
	                </td>
	            </tr>
	        </table>
	        <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.autocomplete.js"></script>
			<script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.mockjax.js"></script>
	        <script>
	            $(function(){
		            // Initialize ajax autocomplete for dosen:
		            $('#<?= $id_form ?>').autocomplete({
		                serviceUrl: '<?= route('jda_dosen') ?>',
		                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
		                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
		                    return re.test(suggestion.value);
		                },
		                onSearchStart: function(data) {
		                    $('#<?= $id_form ?>-spinner').show();
		                },
		                onSearchComplete: function(data) {
		                    $('#<?= $id_form ?>-spinner').hide();
		                },
		                onSelect: function(suggestion) {
		                    $('#<?= $id_form ?>-input').val(suggestion.data);
		                },
		                onInvalidateSelection: function() {
		                }
		            });
		        });
	        </script>
	        <?php

	    }

	    public function matakuliah(Request $r)
	    {
			$param = $r->input('query');
			if ( !empty($param) ) {
				$matakuliah = DB::table('mk_kurikulum as mkur')
							->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
							->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
							->select('mk.id','mk.kode_mk','mk.nm_mk','mk.sks_mk','mkur.id as id_mkur', 'mkur.smt', 'kur.nm_kurikulum')
							->where('kur.id_prodi', $r->prodi)
							->where('mk.jenis_mk','<>', 'E')
							->where(function($q)use($param){
								$q->where('mk.nm_mk', 'like', '%'.$param.'%')
								->orWhere('mk.kode_mk', 'like', '%'.$param.'%');
							})->orderBy('mk.nm_mk','asc')->take(10)->get();
			} else {
				$matakuliah = DB::table('mk_kurikulum as mkur')
							->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
							->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
							->select('mk.id','mk.kode_mk','mk.nm_mk','mk.sks_mk','mkur.id as id_mkur', 'mkur.smt', 'kur.nm_kurikulum')
							->where('kur.id_prodi', $r->prodi)
							->where('mk.jenis_mk','<>', 'E')
							->take(10)->get();
			}
			$data = [];
			foreach( $matakuliah as $r ) {
				$data[] = ['data' => $r->id, 'value' => $r->kode_mk.' - '.trim($r->nm_mk).' ('.$r->sks_mk.' sks) - '.$r->nm_kurikulum.' - smt '.$r->smt, 'id_mkur' => $r->id_mkur];
			}
			$response = ['query' => 'Unit', 'suggestions' => $data];
			return Response::json($response,200);
	    }

	    public function dosen(Request $r)
	    {
				$param = $r->input('query');
				if ( !empty($param) ) {
					$dosen = Dosen::where('aktif',1)
									->where(function($q)use($param){
										$q->where('nm_dosen','like','%'.$param.'%')
										->orWhere('nidn','like','%'.$param.'%');
									})->orderBy('nm_dosen','asc')->get();
				} else {
					$dosen = Dosen::where('aktif',1)->orderBy('nm_dosen','asc')->get();
				}
				$data = [];
				foreach( $dosen as $r ) {
					$data[] = ['data' => $r->id, 'value' => Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang)];
				}
				$response = ['query' => 'Unit', 'suggestions' => $data];
				return Response::json($response,200);
	    }

	    public function store(Request $r)
	    {
	    	$id_smt = Sia::sessionPeriode('id');

		    	$this->validate($r, [
		    		'id_jam' => 'required',
		    		'hari' => 'required',
		    		'prodi' => 'required',
		    		'matakuliah' => 'required',
		    		'kelas' => 'required|max:5',
		    		'ruangan' => 'required',
		    		'kapasitas' => 'numeric',
		    	]);

		    	/* Rule */

			    	// Pada bagian dosen 1 dan dosen 2 tidak boleh dosen yang sama
			    	if ( count($r->dosen) > 0 ) {

		    			$dosen = '';
			    		foreach( $r->dosen as $val ) {
			    			if ( empty($val) ) continue;

			    			if ( $dosen == $val ) {
			    				
			    				return Response::json(['error' => 1, 'msg' => 'Terjadi kerangkapan dosen mengajar pada jadwal yang anda masukkan'],200);
			    			}
			    			$dosen = $val;

			    			// Dosen mengajar pada hari & jam yang sama pada semester yg sama
		    				// Hanya untuk s1
		    				if ( $r->prodi != '61101' ) {
			    				$rule_3 = DB::table('jadwal_kuliah as jdk')
			    						->leftJoin('dosen_mengajar as dm', 'jdk.id','=', 'dm.id_jdk')
					    				->where('jdk.id_smt', $id_smt)
					    				->where('jdk.hari', $r->hari)
					    				->where('jdk.id_jam', $r->jam)
					    				->where('jdk.jenis', 2)
					    				->where('dm.id_dosen', $dosen)->count();
					    		if ( $rule_3 > 0 ) {
					    			$dos = Dosen::find($dosen);
						    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' mengajar pada hari dan jam ini'],200);
						    	}
						    }
			    		}
			    	}

			    	// Ketersediaan ruangan
			    	$rule_4 = DB::table('jadwal_kuliah')
			    				->where('id_smt', $id_smt)
			    				->where('hari', $r->hari)
			    				->where('id_jam', $r->jam)
			    				->where('jenis', 2)
			    				->where('ruangan', $r->ruangan)->count();

			    	if ( $rule_4 > 0 ) {
			    		return Response::json(['error' => 1, 'msg' => 'Ruangan yang anda masukkan terpakai'],200);
			    	}

			    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
			    	$rule_5 = $this->rule5($r, $id_smt);

			    	if ( $rule_5 > 0 ) {
			    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .' telah ada pada Kelas '. $r->kelas],200);
			    	}
			    /* end rule */

	    	try {

	    		DB::transaction(function()use($r,$id_smt,&$id_jdk){
	    			
	    			$id_jdk = Rmt::uuid();
	    			
	    			$jdk = new JadwalKuliah;

	    			$jdk->id = $id_jdk;
	    			$jdk->id_mkur = $r->id_mkur;
	    			$jdk->id_prodi = $r->prodi;
	    			$jdk->id_mk = $r->matakuliah;
	    			$jdk->id_smt = $id_smt;
		            $jdk->kode_kls = $r->kelas;
		            $jdk->ruangan = empty($r->ruangan) ? NULL : $r->ruangan;
		            $jdk->id_jam = empty($r->id_jam) ? NULL : $r->id_jam;
		            $jdk->hari = empty($r->hari) ? NULL : $r->hari;
		            $jdk->kapasitas_kls = empty($r->kapasitas) ? NULL : $r->kapasitas;
		            $jdk->jenis = 2;

		            $jdk->save();

		            if ( count($r->dosen) > 0 ) {

		            	$dosen = [];
		            	foreach( $r->dosen as $key => $val ) {
		            		if ( empty($val) ) continue;

		            		$dosen[] = [
		            			'id_jdk' => $id_jdk,
		            			'id_dosen' => $val,
		            			'jml_tm' => $r->tatap_muka[$key],
		            			'jml_real' => $r->real_tm[$key]
		            		];
		            	}

		            	if ( count($dosen) > 0 ) {
			            	DB::table('dosen_mengajar')->insert($dosen);
			            }

		            }
			        
	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	Rmt::success('Berhasil menyimpan data');

	    	return Response::json(['error' => 0,'msg' => $id_jdk], 200);
	    }

	    protected function rule5($r,$id_smt)
	    {
	    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
	    	$rule = DB::table('jadwal_kuliah')
		    				->where('id_smt', $id_smt)
		    				->where('kode_kls', $r->kelas)
		    				->where('id_mk', $r->matakuliah)
		    				->where('jenis', 2)
		    				->where('id_prodi', $r->prodi)->count();
		    return $rule;
	    }

	    public function edit($id)
	    {
			$data['jdk'] = Sia::jadwalAntara('first')
	    						->where('jdk.id', $id)->first();

	    	return view('jadwal-antara.edit', $data);
	    }

	    public function update(Request $r)
	    {
	    	$id_smt = Sia::sessionPeriode('id');

	    	if ( $r->jenis_jadwal == 'normal' ) {

		    	$this->validate($r, [
		    		'id_jam' => 'required',
		    		'hari' => 'required',
		    		'prodi' => 'required',
		    		'matakuliah' => 'required',
		    		'kelas' => 'required|max:5',
		    		'ruangan' => 'required',
		    		'kapasitas' => 'numeric',
		    	]);

		    	/* Rule */

			    	// Ketersediaan ruangan
			    	$rule_4 = DB::table('jadwal_kuliah')
			    				->where('id_smt', $id_smt)
			    				->where('hari', $r->hari)
			    				->where('id_jam', $r->jam)
			    				->where('ruangan', $r->ruangan)
								->where('jenis', 2)
			    				->where('id','<>',$r->id)->count();

			    	if ( $rule_4 > 0 ) {
			    		return Response::json(['error' => 1, 'msg' => 'Ruangan yang anda masukkan terpakai'],200);
			    	}

			    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
			    	$rule_5 = DB::table('jadwal_kuliah')
		    				->where('id_smt', $id_smt)
		    				->where('kode_kls', $r->kelas)
		    				->where('id_mk', $r->matakuliah)
		    				->where('id_prodi', $r->prodi)
							->where('jenis', 2)
		    				->where('id','<>',$r->id)->count();

			    	if ( $rule_5 > 0 ) {
			    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .' telah ada pada Kelas '. $r->kelas],200);
			    	}
			    /* end rule */

			} else {
				$this->validate($r, [
		    		'prodi' => 'required',
		    		'matakuliah' => 'required',
		    		'kelas' => 'required|max:5',
		    	]);

		    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
		    	$rule_5 = DB::table('jadwal_kuliah')
		    				->where('id_smt', $id_smt)
		    				->where('kode_kls', $r->kelas)
		    				->where('id_mk', $r->matakuliah)
		    				->where('id_prodi', $r->prodi)
							->where('jenis', 2)
		    				->where('id','<>',$r->id)->count();

		    	if ( $rule_5 > 0 ) {
		    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .'telah ada pada Kelas'],200);
		    	}
			}

	    	try {

	    		DB::transaction(function()use($r,$id_smt){
	    			
	    			$jdk = JadwalKuliah::find($r->id);

	    			$jdk->id_mkur = $r->id_mkur;
	    			$jdk->id_prodi = $r->prodi;
	    			$jdk->id_mk = $r->matakuliah;
	    			$jdk->id_smt = $id_smt;
		            $jdk->kode_kls = $r->kelas;
		            $jdk->ruangan = empty($r->ruangan) ? NULL : $r->ruangan;
		            $jdk->id_jam = empty($r->id_jam) ? NULL : $r->id_jam;
		            $jdk->hari = empty($r->hari) ? NULL : $r->hari;
		            $jdk->kapasitas_kls = empty($r->kapasitas) ? NULL : $r->kapasitas;

		            $jdk->save();
	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	Rmt::success('Berhasil menyimpan data');

	    	return Response::json(['error' => 0,'msg' => $r->id], 200);
	    }

	    public function delete($id)
	    {
	    	// Rule 1
	    	// Cek peserta
	    	$peserta = DB::table('nilai')
	    					->where('id_jdk', $id)
	    					->count();

	    	$dosen = DB::table('dosen_mengajar')->where('id_jdk', $id)->count();

	    	if ( $peserta + $dosen > 0 ) {
	    		Rmt::error('Gagal Menghapus. Masih ada peserta kelas/dosen pada jadwal ini');
	    		return redirect()->back();
	    	}

	    	JadwalKuliah::find($id)->delete();
	    	Rmt::success('Berhasil Menghapus data');
	    	return redirect()->back();
	    }

	    public function cetakAbsenMhs(Request $r)
	    {
	    	$query = Sia::jadwalKuliah('', 2);

	    	$data['r'] = $query->where('jdk.id',$r->id)->first();

	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.gelar_depan','d.gelar_belakang','d.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();

	    	$data['mahasiswa'] = Sia::pesertaKelas($data['r']->id);

	    	return view('jadwal-antara.cetak-absen-mhs', $data);
	    }

	    public function cetakAbsenDosen(Request $r)
	    {
	    	$query = Sia::jadwalKuliah('', 2);

	    	$data['r'] = $query->where('jdk.id',$r->id)->first();
	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.gelar_depan','d.gelar_belakang','d.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();

	    	return view('jadwal-antara.cetak-absen-dosen', $data);
	    }
	    
	   	public function cetakDaftarNilai(Request $r)
	    {
	    	$query = Sia::jadwalKuliah('', 2);

	    	$data['r'] = $query->where('jdk.id',$r->id)->first();

	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.gelar_depan','d.gelar_belakang','d.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();

	    	$data['mahasiswa'] = Sia::pesertaKelas($data['r']->id);

	    	return view('jadwal-antara.cetak-daftar-nilai', $data);
	    }

	/* end jadwal */

	/* Dosen */

		public function dosenStore(Request $r)
		{
			$this->validate($r, [
				'dosen' => 'required'
			]);

			// Dosen mengajar pada hari & jam yang sama pada semester yg sama
			// Hanya untuk s1
			if ( $r->prodi != '61101' ) {
				$rule = DB::table('jadwal_kuliah as jdk')
						->leftJoin('dosen_mengajar as dm', 'jdk.id','=', 'dm.id_jdk')
	    				->where('jdk.id_smt', Sia::sessionPeriode('id'))
	    				->where('jdk.hari', $r->hari)
	    				->where('jdk.id_jam', $r->jam)
	    				->where('jdk.jenis', 2)
	    				->where('dm.id_dosen', $r->dosen)->count();
	    		if ( $rule > 0 ) {
	    			$dos = Dosen::find($r->dosen);
		    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' mengajar pada hari dan jam ini'],200);
		    	}
		    }

		    // Dosen telah ada
		    $rule_2 = DB::table('dosen_mengajar')
		    			->where('id_dosen', $r->dosen)
		    			->where('id_jdk', $r->id_jdk)
		    			->count();
    		if ( $rule_2 > 0 ) {
    			$dos = Dosen::find($r->dosen);
	    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' telah ada'],200);
	    	}

		    try {

	    		DB::transaction(function()use($r){
	    			$data = [
		            			'id_jdk' => $r->id_jdk,
		            			'id_dosen' => $r->dosen,
		            			'jml_tm' => $r->tatap_muka,
		            			'jml_real' => $r->real_tm
		            		];
		            DB::table('dosen_mengajar')->insert($data);
	    			
	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	Rmt::success('Berhasil menyimpan data');

	    	return Response::json(['error' => 0,'msg' => $r->id_jdk], 200);
		}

		public function dosenUpdate(Request $r)
		{
			$this->validate($r, [
				'dosen' => 'required'
			]);

			// Dosen mengajar pada hari & jam yang sama pada semester yg sama
			// Hanya untuk s1
			if ( $r->prodi != '61101' ) {
				$rule = DB::table('jadwal_kuliah as jdk')
						->leftJoin('dosen_mengajar as dm', 'jdk.id','=', 'dm.id_jdk')
	    				->where('jdk.id_smt', Sia::sessionPeriode('id'))
	    				->where('jdk.hari', $r->hari)
	    				->where('jdk.id_jam', $r->jam)
	    				->where('dm.id_dosen', $r->dosen)
	    				->where('jdk.jenis', 2)
	    				->where('jdk.id','<>', $r->id_jdk)->count();
	    		if ( $rule > 0 ) {
	    			$dos = Dosen::find($r->dosen);
		    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' mengajar pada hari dan jam ini'],200);
		    	}
		    }

		    // Dosen telah ada
		    $rule_2 = DB::table('dosen_mengajar')
		    			->where('id_dosen', $r->dosen)
		    			->where('id_jdk', $r->id_jdk)
		    			->where('id','<>', $r->id)
		    			->count();
    		if ( $rule_2 > 0 ) {
    			$dos = Dosen::find($r->dosen);
	    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' telah ada'],200);
	    	}

		    try {

	    		DB::transaction(function()use($r){
	    			$data = [
		            			'id_dosen' => $r->dosen,
		            			'jml_tm' => $r->tatap_muka,
		            			'jml_real' => $r->real_tm
		            		];
		            DB::table('dosen_mengajar')
		            	->where('id',$r->id)->update($data);
	    			
	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	Rmt::success('Berhasil menyimpan data');

	    	return Response::json(['error' => 0,'msg' => $r->id_jdk], 200);
		}

	    public function dosenDelete(Request $r)
	    {
	    	DB::table('dosen_mengajar')
	    		->where('id_jdk', $r->id_jdk)
	    		->where('id_dosen', $r->id_dosen)
	    		->delete();
	    	Rmt::success('Berhasil Menghapus data');
	    	return redirect()->back();
	    }
	/* end dosen */

	/* Mahasiswa */
		public function mahasiswa(Request $r)
		{
			$param = $r->input('query');
			if ( !empty($param) ) {
				$mahasiswa = DB::table('mahasiswa_reg as m2')
								->leftJoin('mahasiswa as m1', 'm2.id_mhs', 'm1.id')
								->where('m2.id_jenis_keluar', 0)
								->where('m2.id_prodi', $r->prodi)
								->where(function($q)use($param){
									$q->where('m2.nim', 'like', '%'.$param.'%')
									->orWhere('m1.nm_mhs', 'like', '%'.$param.'%');
								})->select('m2.id','m2.nim','m1.nm_mhs')->get();

			} else {
				DB::table('mahasiswa_reg as m2')
								->leftJoin('mahasiswa as m1', 'm2.id_mhs', 'm1.id')
								->where('m2.id_jenis_keluar', 0)
								->where('m2.id_prodi', $r->prodi)
								->select('m2.id','m2.nim','m1.nm_mhs')
								->take(20)
								->get();
			}

			$data = [];
			foreach( $mahasiswa as $r ) {
				$data[] = ['data' => $r->id, 'value' => $r->nim.' - '.$r->nm_mhs];
			}
			$response = ['query' => 'Unit', 'suggestions' => $data];
			return Response::json($response,200);
		}

		public function mahasiswaStore(Request $r)
		{
			$this->validate($r, [
				'mahasiswa' => 'required'
			]);

			// Mahasiswa ganda
			$rule = DB::table('nilai as n')
				->where('n.id_mhs_reg', $r->mahasiswa)
				->where('n.id_jdk', $r->id_jdk)
				->count();

			if ( $rule > 0 ) {
	    		return Response::json(['error' => 1, 'msg' => $r->nama_mhs .' telah ada'],200);
	    	}
	    	
	    	// SKS semester > n
	    	$rule_2 = Sia::sksSemester($r->mahasiswa, 2);
	    	if ( ($rule_2 + $r->sks) > Sia::maxTakeSks('sp') ) {
	    		return Response::json(['error' => 1, 'msg' => 'Mahasiswa ini telah melebihi batas SKS Total persemester. Total krs yang bisa diprogram tidak boleh lebih dari '.Sia::maxTakeSks('sp').' SKS'],200);
	    	}

	    	// Matakuliah belum diprogramkan?
	    	$rule_3 = DB::table('nilai as n')
    					->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
    					->leftJoin('mk_kurikulum as mkur', 'mkur.id', 'jdk.id_mkur')
    					->where('n.id_mhs_reg', $r->mahasiswa)
    					->where('mkur.id_mk', $r->id_mk)
	    				->count();

	    	if ( $rule_3 == 0 ) {
	    		return Response::json(['error' => 1, 'msg' => 'Matakuliah ini belum pernah diprogram oleh mahasiswa ini'],200);
	    	}

	    	try {

	    		DB::transaction(function()use($r){

		    		$nilai = new Nilai;
		    		$nilai->id = Rmt::uuid();
		    		$nilai->id_mhs_reg = $r->mahasiswa;
		    		$nilai->id_jdk = $r->id_jdk;
		    		$nilai->semester_mk = $r->semester_mk;
		    		$nilai->save();

		    		$krs_stat = Sia::krsStatus($r->mahasiswa);

		    		if ( $krs_stat->status_krs == 0 ) {
		    			DB::table('krs_status')
		    				->where('id_mhs_reg', $r->mahasiswa)
		    				->where('id_smt', Sia::sessionPeriode())
		    				->update(['status_krs' => '1']);
		    		}

		    	});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

			Session::flash('tab_peserta', 1);
			Rmt::success('Berhasil menyimpan data');
	    	return Response::json(['error'=>0, 'msg'=>1]);
		}

		public function mahasiswaAdd(Request $r)
		{
			$jdk = Sia::jadwalAntara();

	    	$data['r'] = $jdk->where('jdk.id',$r->jdk)->first();

			$mahasiswa = Sia::kolektifPeserta($r->jdk, 'SP');

			if ( !empty($r->pr) ) {
				$mahasiswa->where('m2.id_prodi',$r->pr);
			}

			if ( empty($r->ang) ) {
				$mahasiswa->whereRaw('left('.Sia::prefix().'m2.semester_mulai,4) ='.date('Y') - 1);
			} else {
				$mahasiswa->whereRaw('left('.Sia::prefix().'m2.semester_mulai,4) ='.$r->ang);
			}

			$data['mahasiswa'] = $mahasiswa->orderBy('m2.nim','asc')->get();

			return view('jadwal-antara.mhs-add', $data);
		}

		public function mahasiswaStoreArr(Request $r)
		{
			if ( count($r->mahasiswa) > 0 ) {

				try {
					DB::transaction(function()use($r, &$mhsMaxSks){

			    		$mhsMaxSks = [];
						foreach( $r->mahasiswa as $key => $val ) {

							if ( empty($val) ) continue;
					    	
					    	// SKS semester > n
					    	$rule_2 = Sia::sksSemester($val, 2);

					    	if ( ($rule_2 + $r->sks) > Sia::maxTakeSks('sp') ) {

					    		$mhsMaxSks[] = $r->nama_mhs[$key];

					    	} else {

					    		$data = new Nilai;
					    		$data->id = Rmt::uuid();
					    		$data->id_mhs_reg = $val;
					    		$data->id_jdk = $r->id_jdk;
					    		$data->semester_mk = $r->semester_mk;
					    		$data->save();

					    		$krs_stat = Sia::krsStatus($val);

					    		if ( $krs_stat->status_krs == 0 ) {
					    			DB::table('krs_status')
					    				->where('id', $krs_stat->id)
					    				->update(['status_krs' => 1]);
					    		}
					    	}

						}

					});
				}  catch(\Exception $e) {
		    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
		    	}

			} else {
				return Response::json(['error'=>1,'msg'=>'Anda belum memilih mahasiswa'],200);
			}

			Session::flash('tab_peserta', 1);

			if ( count($mhsMaxSks) > 0 ) {
				Rmt::error('Mahasiswa ini ('.implode(',', $mhsMaxSks).') tidak tersimpan karena telah melebihi batas SKS Total persemester. Total krs yang bisa diprogram tidak boleh lebih dari 24 SKS');
			} else {
				Rmt::success('Berhasil menyimpan semua data');
			}

	    	return Response::json(['error'=>0, 'msg'=>1]);

		}

		public function mahasiswaDelete($id)
		{
			DB::table('nilai')
				->where('id',$id)->delete();

			Rmt::success('Berhasil Menghapus data');
			Session::flash('tab_peserta', 1);

	    	return redirect()->back();
		}
}
