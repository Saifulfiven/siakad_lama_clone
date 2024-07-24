<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use App\Dosen, App\JadwalKuliah, App\Nilai, App\KrsStatus, App\Jamkuliah;
use DB, Sia, Rmt, Excel, Response, Session, Carbon, Feeder;

class JadwalKuliahController extends Controller
{
	/* Jadwal */
	    public function index(Request $r)
	    {
	    	if ( !Session::has('jdk_smt') ) {
	    		Session::put('jdk_smt', [Sia::sessionPeriode()]);
	    	}

	    	$query = Sia::jadwalKuliah();

	    	$data['jadwal'] = $query->orderBy('created_at','desc')->paginate(10);
//            return $data;
	    	return view('jadwal-kuliah.index', $data);
	    }

	    public function detail($id)
	    {
	    	$query = Sia::jadwalKuliah();

	    	$data['r'] = $query->where('jdk.id',$id)->first();
	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.gelar_depan','d.gelar_belakang','d.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();
	    	$data['peserta'] = Sia::pesertaKelas($data['r']->id);

	    	$data['jdw_akademik'] = DB::table('jadwal_akademik')
									->where('id_fakultas', Sia::getFakultasUser($data['r']->id_prodi))
									->first();

	    	return view('jadwal-kuliah.detail', $data);

	    }

	    public function cetakPeserta($id)
	    {
	    	$query = Sia::jadwalKuliah();

	    	$data['r'] = $query->where('jdk.id',$id)->first();
	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.gelar_depan','d.gelar_belakang','d.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();
	    	$data['peserta'] = Sia::pesertaKelas($data['r']->id);

	    	$data['jdw_akademik'] = DB::table('jadwal_akademik')
									->where('id_fakultas', Sia::getFakultasUser($data['r']->id_prodi))
									->first();

	    	return view('jadwal-kuliah.cetak-peserta', $data);

	    }

	    public function cari(Request $r)
	    {
			if ( !empty($r->q) ) {
				Session::put('jdk_search',trim($r->q));
			} else {
				Session::pull('jdk_search');
			}

			return redirect(route('jdk'));
	    }

	    public function filter(Request $r)
	    {
			if ( $r->ajax() ) {
				Sia::filter($r->value,'jdk_'.$r->modul);
			} else {
				Session::pull('jdk_search');
				Session::pull('jdk_prodi');
				Session::pull('jdk_smt');
				Session::pull('jdk_ket');
			}
			
			return redirect(route('jdk'));
	    }

	    public function eksporPrint(Request $r)
	    {

	    	if ( $r->s2 ) {
	    		$view = 'print-jadwal-s2';
	    		$data[] = '';
	    	} else {
	    		$query = Sia::jadwalKuliah();
	    		$data['jadwal'] = $query->where('jdk.id_jam','<>',0)->get();
	    		$view = 'print';
	    	}

	    	return view('jadwal-kuliah.'.$view, $data);
	    }

	    public function cetakAbsenMhs(Request $r)
	    {
	    	$query = Sia::jadwalKuliah();

	    	$data['r'] = $query->where('jdk.id',$r->id)->first();

	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.gelar_depan','d.gelar_belakang','d.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();

	    	$data['mahasiswa'] = Sia::pesertaKelas($data['r']->id);

	    	return view('jadwal-kuliah.cetak-absen-mhs', $data);
	    }

	    public function cetakAbsenDosen(Request $r)
	    {
	    	$query = Sia::jadwalKuliah();

	    	$data['r'] = $query->where('jdk.id',$r->id)->first();

	    	$data['dosen'] = DB::table('dosen_mengajar as dm')
	    						->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
	    						->select('dm.*','d.nidn','d.gelar_depan','d.gelar_belakang', 'd.nm_dosen')
	    						->where('dm.id_jdk', $data['r']->id)->get();


	    	return view('jadwal-kuliah.cetak-absen-dosen', $data);
	    }

	    public function add(Request $r)
	    {
	    	if ( Session::has('dosen_ke') ) {
	    		Session::pull('dosen_ke');
	    	}

	    	$prodi = Sia::getProdiUser();

	    	if ( in_array('61101', $prodi)) {
	    		$view = 'jadwal-kuliah.add-s2';
	    	} else {
	    		$view = 'jadwal-kuliah.add';
	    	}
	    	return view($view);
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
	    			
		                <select class="form-control mw-3" name="jam">
		                    <option value="">-- Jam --</option>
		                    <?php $jamkul = Sia::jamKuliah($r->prodi, $r->ket); ?>
		                    <?php foreach( $jamkul as $j ) { ?>
		                        <option value="<?= $j->id ?>"><?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?> (<?= $j->ket ?>)</option>
		                    <?php } ?>
		                </select>

	                <?php } ?>

	    		<?php break;

	    		case 'kelas_s1': ?>
                    <?php
                    $kelas = DB::table('kelas')
//                        ->where('id_jenis_keluar', 0)
                        ->where('id_prodi', $r->prodi)
                        ->get();
                    ?>
	    			<select class="form-control mw-2" id="kelas" onchange="kelasChange(this.value)">
                        <option value="">-- Kelas --</option>
                        <?php foreach( $kelas as $r ) { ?>
                        	<option value="<?= $r->nm_kelas ?>"><?= $r->nm_kelas ?></option>
                        <?php } ?>
                    </select>

	    		<?php break;
	    		
	    		case 'kelas': ?>
	    			<select class="form-control mw-2" id="kelas">
                        <option value="">-- Kelas --</option>
                        <?php foreach( Sia::listKelas($r->prodi, $r->waktu) as $r ) { ?>
                        	<option value="<?= $r->nm_kelas.'|'.$r->ket ?>"><?= $r->nm_kelas.' ('.$r->ket.')' ?></option>
                        <?php } ?>
                    </select>
	    		<?php break;

	    		case 'pertemuan':
	    			for( $i = 1; $i <= $r->pertemuan; $i++ ) { ?>
                        <div class="col-md-3 col-lg-3 col-sm-3">
                            <table class="table-hover table-form">
                                <tr>
                                    <td width="100">Pertemuan <?= $i ?> : </td>
                                    <td><input type="date" name="pertemuan[<?= $i ?>]" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td>Jam Pert. <?= $i ?> : </td>
                                    <td>
                                        <select class="form-control mw-3" name="jamper[<?= $i ?>]">
                                            <?php $jamkul = Sia::jamKuliah(61101); ?>
                                            <?php foreach( $jamkul as $j ) { ?>
                                                <option value="<?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?>"><?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php if ( $i % 4 == 0 ) { ?>
                            <div class="clearfix"></div>
                            <hr>
                        <?php } ?>
                    <?php }

	    			break;

	    		case 'jdk-cetak-s2':
	    			$data = DB::table('jadwal_kuliah as jdk')
	    					->leftJoin('jam_kuliah as jk', 'jdk.id_jam', 'jk.id')
	    					->where('jdk.id_smt', Session::get('jdk_smt'))
	    					->whereIn('jdk.id_prodi', Sia::getProdiUser())
	    					->where('jk.ket', $r->waktu_kuliah)
	    					->orderBy('jdk.kode_kls')
	    					->groupBy('jdk.kode_kls')
	    					->get(); 
	    			$no = 1; ?>
	    			<div style="max-height: 500px;overflow-x: scroll">
		    			<table class="table table-bordered table-hover">
		    				<tr>
		    					<th width="20">No</th>
		    					<th>Kelas</th>
		    					<th>Aksi</th>
		    				</tr>
		    				<?php foreach( $data as $res ) { ?>
		    					<tr>
		    						<td align="center"><?= $no++ ?></td>
		    						<td><?= $res->kode_kls ?></td>
		    						<td align="center"><a href="<?= route('jdk_print', ['kelas' => $res->kode_kls, 's2' => true]) ?>" target="_blank" class="btn btn-primary btn-xs">Cetak</a></td>
		    					</tr>
		    				<?php } ?>
		    			</table> 
		    		</div>

	    			<?php break;

	    		case 'cek-kelas':

	    			$id_smt = Sia::sessionPeriode('id');

	    			$krs = Sia::getKrsMhs($r->kelas, $r->id_mkur, $id_smt, true);

	    			if ( $krs != 0 ) {
	    				echo $krs." Mahasiswa akan dimasukkan ke kelas ini.";
	    			} else {
	    				echo "Tidak menemukan mahasiswa untuk kelas dan matakuliah ini";
	    			}

	    		break;
	    		default:
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
		                serviceUrl: '<?= route('jdk_dosen') ?>',
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
								->select('mk.id','mk.kode_mk','mk.nm_mk','mk.sks_mk','kur.nm_kurikulum','mkur.id as id_mkur','mkur.smt')
								->where('kur.aktif',1)
								->where('kur.id_prodi', $r->prodi)
								// ->where('mkur.periode', Sia::sessionPeriode('smt'))
								->where(function($q)use($param){
									$q->where('mk.nm_mk', 'like', '%'.$param.'%')
									->orWhere('mk.kode_mk', 'like', '%'.$param.'%');
								})->orderBy('mk.nm_mk','asc')->get();
			} else {
				$matakuliah = DB::table('mk_kurikulum as mkur')
								->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
								->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
								->select('mk.id','mk.kode_mk','mk.nm_mk','mk.sks_mk','kur.nm_kurikulum','mkur.id as id_mkur','mkur.smt')
								->where('kur.aktif',1)
								->where('kur.id_prodi', $r->prodi)
								// ->where('mkur.periode', Sia::sessionPeriode('smt'))
								->get();
			}
			$data = [];
			foreach( $matakuliah as $res ) {
				$data[] = ['data' => $res->id, 'value' => $res->kode_mk.' - '.trim($res->nm_mk).' ('.$res->sks_mk.' sks) - '.$res->nm_kurikulum.' - smstr '.$res->smt, 'id_mkur' => $res->id_mkur, 'sks' => $res->sks_mk, 'semester_mk' => $res->smt];
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

	    	if ( $r->jenis_jadwal == 'normal' ) {

		    	$this->validate($r, [
		    		'hari' => 'required',
		    		'prodi' => 'required',
		    		'matakuliah' => 'required',
		    		'kelas' => 'required',
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

			    			// Dosen mengajar pada hari & jam yang sama pada semester yg sama dan prodi yang sama
		    				$rule_3 = DB::table('jadwal_kuliah as jdk')
		    						->leftJoin('dosen_mengajar as dm', 'jdk.id','=', 'dm.id_jdk')
				    				->where('jdk.id_smt', $id_smt)
				    				->where('jdk.hari', $r->hari)
				    				->where('jdk.id_jam', $r->jam)
				    				->where('jdk.id_prodi', $r->prodi)
				    				->where('jdk.jenis', 1)
				    				->where('dm.id_dosen', $dosen);

					    	$rule_3 = $rule_3->count();

				    		if ( $rule_3 > 0 ) {
				    			$dos = Dosen::find($dosen);
					    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' mengajar pada hari dan jam ini'],200);
					    	}
			    		}
			    	}

			    	// Ketersediaan ruangan
			    	if ( $r->ruangan != 'EL' ) {

				    	$rule_4 = DB::table('jadwal_kuliah')
				    				->where('id_smt', $id_smt)
				    				->where('hari', $r->hari)
				    				->where('id_jam', $r->jam)
				    				->where('id_prodi', $r->prodi)
				    				->where('jenis', 1)
				    				->where('ruangan', $r->ruangan);

				    	$rule_4 = $rule_4->count();

				    	if ( $rule_4 > 0 ) {
				    		return Response::json(['error' => 1, 'msg' => 'Ruangan yang anda masukkan terpakai'],200);
				    	}

				    }

			    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
			    	$rule_5 = $this->rule5($r, $id_smt);

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
		    	$rule_5 = $this->rule5($r, $id_smt); 

		    	if ( $rule_5 > 0 ) {
		    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .'telah ada pada Kelas'],200);
		    	}
			}

	    	try {

	    		DB::transaction(function()use($r,$id_smt,&$id_jdk, &$store_peserta){
	    			
	    			$id_jdk = Rmt::uuid();
	    			
	    			$jdk = new JadwalKuliah;

	    			$jdk->id = $id_jdk;
	    			$jdk->id_mkur = $r->id_mkur;
	    			$jdk->id_prodi = $r->prodi;
	    			$jdk->id_mk = $r->matakuliah;
	    			$jdk->id_smt = $id_smt;
		            $jdk->kode_kls = $r->kelas;
		            $jdk->ruangan = empty($r->ruangan) ? NULL : $r->ruangan;
		            $jdk->id_jam = empty($r->jam) ? NULL : $r->jam;
		            $jdk->hari = empty($r->hari) ? NULL : $r->hari;
		            $jdk->kapasitas_kls = empty($r->kapasitas) ? NULL : $r->kapasitas;
		            $jdk->kelas_khusus = empty($r->kelas_khusus) ? NULL : $r->kelas_khusus;

		            $jdk->save();

		            if ( $r->jenis_jadwal == 'normal' ) {
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
			        }

			        $store_peserta = $this->storePesertaKelas($r->kelas, $r->id_mkur, $id_smt, $id_jdk, $r->sks_mk, $r->semester_mk);

			       	// if ( $store_peserta['sukses'] == 0 && $store_peserta['gagal'] == 0 ) {
			       	// 	$store_peserta = [];
			       	// }

	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	// Store in feeder
	    	// $this->feederStore($id_jdk);

	    	Rmt::success('Berhasil menyimpan data');

	    	return Response::json(['error' => 0,'msg' => $id_jdk, 'peserta' => $store_peserta], 200);
	    }

	    private function storePesertaKelas($kelas, $id_mkur, $id_smt, $id_jdk, $sks_mk, $semester_mk)
	    {

	    	$mhs_krs = Sia::getKrsMhs($kelas, $id_mkur, $id_smt);
	    	$sukses = 0;
	    	$gagal = 0;
	    	$msg = [];

	    	foreach( $mhs_krs as $mhs ) {

	    		$fakultas_user = Session::get('fakultas');

				$batas_sks = $fakultas_user == 2 ? 18 : 24;

				// Mahasiswa ganda
				$mhs_ganda = DB::table('nilai as n')
					->where('n.id_mhs_reg', $mhs->id_mhs_reg)
					->where('n.id_jdk', $id_jdk)
					->count();

				if ( $mhs_ganda > 0 ) {
		    		$gagal++;
		    		$msg[] = 'Mahasiswa '.$mhs->nim.' telah ada';
		    		continue;
		    	}

		    	// SKS semester > 24
		    	$sks_diambil = Sia::sksSemester($mhs->id_mhs_reg);
		    	if ( ($sks_diambil + $sks_mk) > $batas_sks ) {
		    		$gagal++;
		    		$msg[] = 'Mahasiswa '.$mhs->nim.' melebihi batas sks';
		    		continue;
		    	}

		    	$nilai = new Nilai;
	    		$nilai->id = Rmt::uuid();
	    		$nilai->id_mhs_reg = $mhs->id_mhs_reg;
	    		$nilai->id_jdk = $id_jdk;
	    		$nilai->semester_mk = $semester_mk;
	    		$nilai->save();

	    		DB::table('krs_status')
	    				->where('id_mhs_reg', $mhs->id_mhs_reg)
	    				->where('id_smt', Sia::sessionPeriode())
	    				->update(['status_krs' => '1']);

	    		$sukses++;
	    	
	    	}

	    	$sukses_msg = empty($sukses) ? 0 : 'Sukses memasukkan '.$sukses.' mahasiswa';
	    	$gagal_msg = empty($gagal) ? 0 : 'Gagal memasukkan '.$gagal.' peserta';

	    	$result = [ 'sukses' => $sukses_msg, 'gagal' => $gagal_msg, 'msg' => $msg];

	    	return $result;
	    }

	    public function storeS2(Request $r)
	    {
	    	$id_smt = Sia::sessionPeriode('id');

	    	if ( $r->jenis_jadwal == 'normal' ) {

		    	$this->validate($r, [
		    		'hari' => 'required',
		    		'prodi' => 'required',
		    		'matakuliah' => 'required',
		    		'kelas' => 'required',
		    		'ruangan' => 'required',
		    		'kapasitas' => 'numeric',
		    	]);

		    	/* Rule */

			    	if ( count($r->dosen) > 0 ) {

		    			$dosen = '';
			    		foreach( $r->dosen as $val ) {
			    			if ( empty($val) ) continue;

			    			if ( $dosen == $val ) {
			    				
			    				return Response::json(['error' => 1, 'msg' => 'Terjadi kerangkapan dosen mengajar pada jadwal yang anda masukkan'],200);
			    			}
			    			$dosen = $val;
			    		}
			    	}

			    /* end rule */

			} else {
				$this->validate($r, [
		    		'prodi' => 'required',
		    		'matakuliah' => 'required',
		    		'kelas' => 'required|max:5',
		    	]);

		    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
		    	$rule_5 = $this->rule5($r, $id_smt); 

		    	if ( $rule_5 > 0 ) {
		    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .'telah ada pada Kelas yang anda masukkan'],200);
		    	}
			}

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
		            $jdk->id_jam = empty($r->jam) ? NULL : $r->jam;
		            $jdk->hari = empty($r->hari) ? NULL : $r->hari;
		            $jdk->kapasitas_kls = empty($r->kapasitas) ? NULL : $r->kapasitas;

		            $jdk->save();

		            if ( $r->jenis_jadwal == 'normal' ) {
		            	// Store dosen mengajar
			            if ( count($r->dosen) > 0 ) {

			            	$dosen = [];
			            	foreach( $r->dosen as $key => $val ) {
			            		if ( empty($val) ) continue;

			            		$dosen[] = [
			            			'id_jdk' => $id_jdk,
			            			'id_dosen' => $val,
			            			'jml_tm' => $r->tatap_muka[$key],
			            			'jml_real' => $r->real_tm[$key],
			            			'dosen_ke' => $r->dosen_ke[$key]
			            		];
			            	}

			            	if ( count($dosen) > 0 ) {
				            	DB::table('dosen_mengajar')->insert($dosen);
				            }

			            }

			            // Store tgl pertemuan
			            foreach( $r->pertemuan as $key => $val )
			            {
			            	$data = [
			            		'id_jdk' => $id_jdk,
			            		'tgl' => $val,
			            		'jam' => $r->jamper[$key],
			            		'pertemuan_ke' => $key
			            	];
			            	DB::table('jadwal_pertemuan_s2')->insert($data);
			            }
			        }
	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	// Store in feeder
	    	// $this->feederStore($id_jdk);
	    	
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
		    				->where('jenis', 1)
		    				->where('id_prodi', $r->prodi)->count();
		    return $rule;
	    }

	    public function edit($id)
	    {

	    	$data['jdk'] = Sia::jadwalKuliah('first')
	    						->where('jdk.id', $id)->first();

	    	return view('jadwal-kuliah.edit', $data);
	    }

	    public function update(Request $r)
	    {
	    	$id_smt = Sia::sessionPeriode('id');

	    	$kelas = explode('|',$r->kelas);
		    $kelas = $kelas[0];

	    	if ( $r->jenis_jadwal == 'normal' ) {

		    	$this->validate($r, [
		    		'hari' => 'required',
		    		'kelas' => 'required',
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
			    				->where('jenis', 1)
			    				->where('id','<>',$r->id)->count();

			    	if ( $rule_4 > 0 ) {
			    		return Response::json(['error' => 1, 'msg' => 'Ruangan yang anda masukkan terpakai'],200);
			    	}

			    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama dan prodi yang sama
			    	$rule_5 = DB::table('jadwal_kuliah')
		    				->where('id_smt', $id_smt)
		    				->where('kode_kls', $kelas)
		    				->where('id_mk', $r->matakuliah)
		    				->where('id_prodi', $r->prodi)
		    				->where('jenis', 1)
		    				->where('id','<>',$r->id)->count();

			    	if ( $rule_5 > 0 ) {
			    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .' telah ada pada Kelas '. $kelas],200);
			    	}
			    /* end rule */

			} else {
				$this->validate($r, [
		    		// 'matakuliah' => 'required',
		    		'kelas' => 'required',
		    	]);

		    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
		    	$rule_5 = DB::table('jadwal_kuliah')
		    				->where('id_smt', $id_smt)
		    				->where('kode_kls', $kelas)
		    				->where('id_mk', $r->matakuliah)
		    				->where('id_prodi', $r->prodi)
		    				->where('jenis', 1)
		    				->where('id','<>',$r->id)->count();

		    	if ( $rule_5 > 0 ) {
		    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .'telah ada pada Kelas'],200);
		    	}
			}

	    	try {
	    		$kode_kls_old = '';

	    		DB::transaction(function()use($r,$id_smt, $kelas, &$kode_kls_old){
	    			
	    			$jdk = JadwalKuliah::find($r->id);
	    			$kode_kls_old = $jdk->kode_kls;

	    			// $jdk->id_mkur = $r->id_mkur;
	    			// $jdk->id_mk = $r->matakuliah;
		            $jdk->kode_kls = $kelas;
		            $jdk->ruangan = empty($r->ruangan) ? NULL : $r->ruangan;
		            $jdk->id_jam = empty($r->jam) ? NULL : $r->jam;
		            $jdk->hari = empty($r->hari) ? NULL : $r->hari;
		            $jdk->kapasitas_kls = empty($r->kapasitas) ? NULL : $r->kapasitas;
		            $jdk->kelas_khusus = empty($r->kelas_khusus) ? NULL : $r->kelas_khusus;
		            
		            $jdk->save();
	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	// Update in feeder
	    	// if ( $kelas != $kode_kls_old ) {
	    	// 	$this->feederUpdate($r->id);
	    	// }

	    	return Response::json(['error' => 0,'msg' => $r->id], 200);
	    }

	    public function updateS2(Request $r)
	    {
	    	$id_smt = Sia::sessionPeriode('id');

	    	$kelas = explode('|',$r->kelas);
		    $kelas = $kelas[0];

	    	if ( $r->jenis_jadwal == 'normal' ) {

		    	$this->validate($r, [
		    		'hari' => 'required',
		    		// 'matakuliah' => 'required',
		    		'kelas' => 'required',
		    		'ruangan' => 'required',
		    		'kapasitas' => 'numeric',
		    	]);

			} else {
				$this->validate($r, [
		    		// 'matakuliah' => 'required',
		    		'kelas' => 'required',
		    	]);

		    	// Satu kelas mempunyai Matakuliah yg sama pada semester yang sama
		    	$rule_5 = DB::table('jadwal_kuliah')
		    				->where('id_smt', $id_smt)
		    				->where('kode_kls', $kelas)
		    				->where('id_mk', $r->matakuliah)
		    				->where('id_prodi', $r->prodi)
		    				->where('jenis', 1)
		    				->where('id','<>',$r->id)->count();

		    	if ( $rule_5 > 0 ) {
		    		return Response::json(['error' => 1, 'msg' => $r->matakuliah_value .'telah ada pada Kelas yang anda masukkan'],200);
		    	}
			}

	    	try {

	    		DB::transaction(function()use($r,$id_smt, $kelas){
	    			
	    			$jdk = JadwalKuliah::find($r->id);

	    			// $jdk->id_mkur = $r->id_mkur;
	    			// $jdk->id_mk = $r->matakuliah;
		            $jdk->kode_kls = $kelas;
		            $jdk->ruangan = empty($r->ruangan) ? NULL : $r->ruangan;
		            $jdk->id_jam = empty($r->jam) ? NULL : $r->jam;
		            $jdk->hari = empty($r->hari) ? NULL : $r->hari;
		            $jdk->kapasitas_kls = empty($r->kapasitas) ? NULL : $r->kapasitas;

		            $jdk->save();
	    		});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	// Update in feeder
	    	$this->feederUpdate($r->id);

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

	    	$jadwal_ujian = DB::table('jadwal_ujian')->where('id_jdk', $id)->count();

	    	if ( $peserta + $dosen + $jadwal_ujian != 0 ) {
	    		Rmt::error('Gagal Menghapus. Masih ada peserta kelas/dosen/jadwal ujian pada jadwal ini');
	    		return redirect()->back();
	    	}

	    	JadwalKuliah::find($id)->delete();
	    	// Hanya untuk s2
	    	DB::table('jadwal_pertemuan_s2')->where('id_jdk', $id)->delete();

	    	Rmt::success('Berhasil Menghapus data');
	    	return redirect()->back();
	    }

	    public function storePertemuan(Request $r)
	    {
	    	Session::flash('tab_pertemuan', 1);

	    	$this->validate($r, [
	    		'tanggal' => 'required',
	    		'jam' => 'required'
	    	]);

	    	$cek = DB::table('jadwal_pertemuan_s2')
	    			->where('id_jdk', $r->id_jdk)
	    			->where('pertemuan_ke', $r->pertemuan)->count();

	    	if ( $cek > 0 ) {
	    		Rmt::error('Gagal menyimpan, pertemuan ini telah ada');
		    	return redirect()->back();
	    	}

	    	DB::table('jadwal_pertemuan_s2')->insert([
	    		'id_jdk' => $r->id_jdk,
	    		'tgl' => $r->tanggal,
	    		'jam' => $r->jam,
	    		'pertemuan_ke' => $r->pertemuan
	    	]);

	    	Rmt::success('Berhasil menyimpan data');
	    	return redirect()->back();
	    }

	    public function updatePertemuan(Request $r)
	    {
	    	Session::flash('tab_pertemuan', 1);

	    	$this->validate($r, [
	    		'tanggal' => 'required'
	    	]);

	    	$cek = DB::table('jadwal_pertemuan_s2')
	    			->where('pertemuan_ke', $r->pertemuan)
	    			->where('id_jdk', $r->id_jdk)
	    			->where('id','<>', $r->id)->count();

	    	if ( $cek > 0 ) {
	    		Rmt::error('Gagal menyimpan, pertemuan ini telah ada');
		    	return redirect()->back();
	    	}

	    	DB::table('jadwal_pertemuan_s2')->where('id', $r->id)->update([
	    		'tgl' => $r->tanggal,
	    		'jam' => $r->jam,
	    	]);

	    	Rmt::success('Berhasil menyimpan data');
	    	return redirect()->back();

	    }

	    public function deletePertemuan($id)
	    {
	    	DB::table('jadwal_pertemuan_s2')->where('id', $id)->delete();
	    	Session::flash('tab_pertemuan', 1);
	    	Rmt::success('Berhasil menghapus data');
	    	return redirect()->back();
	    }
	/* end jadwal */

	/* Dosen */

		public function dosenStore(Request $r)
		{
			$this->validate($r, [
				'dosen' => 'required'
			]);

			// Dosen mengajar pada hari & jam yang sama pada semester yg sama
			$rule = DB::table('jadwal_kuliah as jdk')
					->leftJoin('dosen_mengajar as dm', 'jdk.id','=', 'dm.id_jdk')
    				->where('jdk.id_smt', Sia::sessionPeriode('id'))
    				->where('jdk.hari', $r->hari)
    				->where('jdk.id_jam', $r->jam)
    				->where('jdk.jenis', 1)
    				->where('dm.id_dosen', $r->dosen)->count();

    		if ( $rule > 0 ) {
    			$dos = Dosen::find($r->dosen);
	    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' mengajar pada hari dan jam ini'],200);
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
		            			'jml_real' => $r->real_tm,
		            			'dosen_ke' => $r->dosen_ke
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

			// Dosen mengajar pada (tgl jika s2), hari & jam yang sama pada semester yg sama
			$rule = DB::table('jadwal_kuliah as jdk')
						->leftJoin('dosen_mengajar as dm', 'jdk.id','=', 'dm.id_jdk')
	    				->where('jdk.id_smt', Sia::sessionPeriode('id'))
	    				->where('jdk.hari', $r->hari)
	    				->where('jdk.id_jam', $r->jam)
	    				->where('dm.id_dosen', $r->dosen)
	    				->where('jdk.jenis', 1)
	    				->where('jdk.id','<>', $r->id_jdk)->count();

    		if ( $rule > 0 ) {
    			$dos = Dosen::find($r->dosen);
	    		return Response::json(['error' => 1, 'msg' => 'Dosen '.Sia::namaDosen($dos->gelar_depan,$dos->nm_dosen,$dos->gelar_belakang).' mengajar pada hari dan jam ini'],200);
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
		            			'jml_real' => $r->real_tm,
		            			'dosen_ke' => $r->dosen_ke
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
				$mahasiswa = Sia::mhsInJadwal()->where('krs.jenis', 'KULIAH')
								->where(function($q)use($param){
									$q->where('m2.nim', 'like', '%'.$param.'%')
										->orWhere('m1.nm_mhs', 'like', '%'.$param.'%');
								})->select('m2.id','m2.nim','m1.nm_mhs')->take(10)->get();
			} else {
				$mahasiswa = Sia::mhsInJadwal()->where('krs.jenis', 'KULIAH')
								->select('m2.id','m2.nim','m1.nm_mhs')->take(10)->get();
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

			$prodi_user = Sia::getProdiUser();

			$batas_sks = $prodi_user[0] == '61101' ? 18 : 24;

			// Mahasiswa ganda
			$rule = DB::table('nilai as n')
				->where('n.id_mhs_reg', $r->mahasiswa)
				->where('n.id_jdk', $r->id_jdk)
				->count();

			if ( $rule > 0 ) {
	    		return Response::json(['error' => 1, 'msg' => $r->nama_mhs .' telah ada'],200);
	    	}

	    	// Matakuliah Sudah ada
	    	$cek_mk = Sia::krsMhs($r->mahasiswa)->where('jdk.id_mk', $r->id_mk)->count();
	    	if ( $cek_mk > 0 ) {
		    	return Response::json(['error' => 1, 'msg' => 'Matakuliah ini telah diambil'],200);
	    	}
	    	
	    	// SKS semester > 24
	    	$rule_2 = Sia::sksSemester($r->mahasiswa);
	    	if ( ($rule_2 + $r->sks) > $batas_sks ) {
	    		return Response::json(['error' => 1, 'msg' => 'Mahasiswa ini telah melebihi batas SKS Total persemester. Total krs yang bisa diprogram tidak boleh lebih dari '.$batas_sks.' SKS'],200);
	    	}

			$krs_stat = Sia::krsStatus($r->mahasiswa);

	    	// Sudah bayar
	    	if ( !$krs_stat ) {
		    	return Response::json(['error' => 1, 'msg' => 'Mahasiswa ini belum membayar'],200);
	    	}

	    	// Jam kuliah mhs tidak bentrok
		    if ( $r->hari != 0 ) {

		    	$jk_bentrok = Sia::validasiBentrokMK($r->mahasiswa, $r->hari, $r->jam);

		    	if ( !empty($jk_bentrok) ) {
		    		return Response::json(['error' => 1, 'msg' => 'Terdapat tabrakan jam kuliah dengan matakuliah: '.$jk_bentrok->kode_mk.' - '.$jk_bentrok->nm_mk],200);
		    	}

		    }

	    	try {

	    		DB::transaction(function()use($r,$krs_stat){

		    		$nilai = new Nilai;
		    		$nilai->id = Rmt::uuid();
		    		$nilai->id_mhs_reg = $r->mahasiswa;
		    		$nilai->id_jdk = $r->id_jdk;
		    		$nilai->semester_mk = $r->semester_mk;
		    		$nilai->save();

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

			$jdk = Sia::jadwalKuliah();

	    	$data['r'] = $jdk->where('jdk.id',$r->jdk)->first();

	    	$jenis = $data['r']->jenis == 1 ? 'KULIAH' : 'SP';

			$mahasiswa = Sia::kolektifPeserta($r->jdk, $jenis);

			if ( !empty($r->pr) ) {
				$mahasiswa->where('m2.id_prodi',$r->pr);
			}

			if ( empty($r->ang) ) {
				$mahasiswa->whereRaw('left('.Sia::prefix().'m2.nim,4) ='.date('Y'));
			} else {
				$mahasiswa->whereRaw('left('.Sia::prefix().'m2.nim,4) ='.$r->ang);
			}

			$data['mahasiswa'] = $mahasiswa->orderBy('m2.nim','asc')->get();

			return view('jadwal-kuliah.mhs-add', $data);
		}

		public function mahasiswaStoreArr(Request $r)
		{
			$prodi_user = Sia::getProdiUser();

			$batas_sks = $prodi_user[0] == '61101' ? 18 : 24;

			if ( count($r->mahasiswa) > 0 ) {

				try {
					DB::transaction(function()use($r,$batas_sks,&$maxSks, &$bentrok){

			    		$maxSks = [];
			    		$bentrok = [];
						foreach( $r->mahasiswa as $key => $val ) {

							if ( empty($val) ) continue;
					    	
					    	// SKS semester > 24
					    	$rule_2 = Sia::sksSemester($val);

					    	// Jam kuliah mhs tidak bentrok
						    if ( $r->hari != 0 ) {

						    	$jk_bentrok = Sia::validasiBentrokMK($val, $r->hari, $r->jam);

						    	if ( !empty($jk_bentrok) ) {
						    		$bentrok[] = $r->nama_mhs[$key].' - Terdapat tabrakan jam kuliah dengan matakuliah: '.$jk_bentrok->kode_mk.' - '.$jk_bentrok->nm_mk;
						    		continue;
						    	}

						    }

					    	if ( ($rule_2 + $r->sks) > $batas_sks ) {

					    		$maxSks[] = $r->nama_mhs[$key];

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

			if ( count($maxSks) > 0 ) {
				Rmt::error('Mahasiswa ini ('.implode(',', $maxSks).') tidak tersimpan karena telah melebihi batas SKS Total persemester. Total krs yang bisa diprogram tidak boleh lebih dari '.$batas_sks.' SKS');
			} else {
				Rmt::success('Berhasil menyimpan data');
			}

			if ( count($bentrok) > 0 ) {
				Session::flash('bentrok', 'Ada yg bentrok jadwal kuliahanya: <br>'.implode(', ', $bentrok));
			} 

	    	return Response::json(['error'=>0, 'msg'=>1]);

		}

		public function mahasiswaAddFromKrs(Request $r)
		{
			$jdk = Sia::jadwalKuliah()->where('jdk.id',$r->id_jdk)->first();

			$peserta = DB::table('nilai')
						->where('id_jdk', $r->id_jdk)
						->pluck('id_mhs_reg');

			$id_smt = Sia::sessionPeriode();

			$krs_mhs = DB::table('krs_mhs as km')
    			->leftJoin('mahasiswa_reg as m', 'km.id_mhs_reg', 'm.id')
    			->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
    			->select('km.*', 'm.nim', 'm.semester_mulai', 'm2.nm_mhs')
    			->where('m.kode_kelas', $r->kelas)
    			->where('km.id_mkur', $r->id_mkur)
    			->where('km.id_smt', $id_smt)
    			->whereNotIn('km.id_mhs_reg', $peserta)
    			->get(); 

    		if ( count($krs_mhs) > 0 ) { ?>

	    		<?= csrf_field() ?>
	            <input type="hidden" name="id_jdk" value="<?= $jdk->id ?>">
	            <input type="hidden" name="sks" value="<?= $jdk->sks_mk ?>">
	            <input type="hidden" name="semester_mk" value="<?= $jdk->smt ?>">
	            <input type="hidden" name="hari" value="<?= $jdk->hari ?>">
	            <input type="hidden" name="jam" value="<?= $jdk->id_jam ?>">

				<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
	                <thead class="custom">
	                    <tr>
	                        <th width="40px">Pilih</th>
	                        <th>NIM</th>
	                        <th>Nama</th>
	                        <th>Angkatan</th>
	                    </tr>
	                </thead>
	                <tbody align="center">
	                    <?php foreach( $krs_mhs as $mhs ) { ?>
	                        <tr>
	                            <td>
	                                <input type="checkbox" name="mahasiswa[]" checked="" value="<?= $mhs->id_mhs_reg ?>">
	                                <input type="hidden" name="nama_mhs[]" value="<?= $mhs->nm_mhs ?>">
	                            </td>
	                            <td align="left"><?= $mhs->nim ?></td>
	                            <td align="left"><?= $mhs->nm_mhs ?></td>
	                            <td><?= substr($mhs->semester_mulai, 0, 4) ?></td>
	                        </tr>
	                    <?php } ?>
	                </tbody>
	            </table>
	            <center>
	            	<button class="btn btn-sm btn-primary" id="btn-submit-peserta"><i class="fa fa-floppy-o"></i>  SIMPAN</button>
	            </center>

	        <?php } else {
	        	echo "<center>Belum ada data / Semua mahasiswa telah dimasukkan</center>";
	        }


		}

		public function mahasiswaStoreFromKrs(Request $r)
		{
			$prodi_user = Sia::getProdiUser();

			$batas_sks = $prodi_user[0] == '61101' ? 18 : 24;

			if ( count($r->mahasiswa) > 0 ) {

				try {
					DB::transaction(function()use($r,$batas_sks,&$maxSks, &$bentrok){

			    		$maxSks = [];
			    		$bentrok = [];
						foreach( $r->mahasiswa as $key => $val ) {
							
							if ( empty($val) ) continue;
					    	
					    	// SKS semester > 24
					    	$rule_2 = Sia::sksSemester($val);

					    	// Jam kuliah mhs tidak bentrok
						    if ( $r->hari != 0 ) {

						    	$jk_bentrok = Sia::validasiBentrokMK($val, $r->hari, $r->jam);

						    	if ( !empty($jk_bentrok) ) {
						    		$bentrok[] = $r->nama_mhs[$key].' - Terdapat tabrakan jam kuliah dengan matakuliah: '.$jk_bentrok->kode_mk.' - '.$jk_bentrok->nm_mk;
						    		continue;
						    	}

						    }

					    	if ( ($rule_2 + (int)$r->sks) > $batas_sks ) {

					    		$maxSks[] = $r->nama_mhs[$key];

					    	} else {

						    	$nilai = new Nilai;
					    		$nilai->id = Rmt::uuid();
					    		$nilai->id_mhs_reg = $val;
					    		$nilai->id_jdk = $r->id_jdk;
					    		$nilai->semester_mk = $r->semester_mk;
					    		$nilai->save();

					    		DB::table('krs_status')
					    				->where('id_mhs_reg', $val)
					    				->where('id_smt', Sia::sessionPeriode())
						    				->update(['status_krs' => '1']);
					    	}
						}
					});
				} catch( \Exception $e ) {
					return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
				}

			}

			Session::flash('tab_peserta', 1);

			if ( count($maxSks) > 0 ) {
				Rmt::error('Mahasiswa ini ('.implode(',', $maxSks).') tidak tersimpan karena telah melebihi batas SKS Total persemester. Total krs yang bisa diprogram tidak boleh lebih dari '.$batas_sks.' SKS');
			} else {
				Rmt::success('Berhasil menyimpan data');
			}

			if ( count($bentrok) > 0 ) {
				Session::flash('bentrok', 'Ada yg bentrok jadwal kuliahanya: <br>'.implode(', ', $bentrok));
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

		public function cetakLabelAbsen(Request $r)
		{

	    	$query = Sia::jadwalKuliah();

	    	if ( !empty($r->id_jdk) ) {
	    		$query->where('jdk.id', $r->id_jdk);
	    	}
	    	
	    	$data['jadwal'] = $query->where('jdk.hari','<>',0)->orderBy('created_at','desc')->get();

	    	return view('jadwal-kuliah.cetak-label-absen', $data);
		}


	/* FEEDER */
	public function feederStore($id_jdk)
	{

		$jdk = DB::table('jadwal_kuliah as jdk')
				->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
				->select('jdk.*', 'mk.kode_mk','mk.sks_mk','mk.sks_tm','mk.sks_prak','mk.sks_prak_lap','mk.sks_sim')
				->where('jdk.id', $id_jdk)->first();

		// Init soap
		$token = Feeder::getTokenNusoap();
		$proxy = Feeder::initSoap();


        $id_prodi = Feeder::idProdi($jdk->id_prodi);
        $id_pt = config('app.feeder_id_pt');

        $data_mk = [
			'act' => 'GetListMataKuliah',
			'filter' => "kode_mata_kuliah='".$jdk->kode_mk."'",
			'limit' => 1
		];

		$res_mk = Feeder::runWs($data_mk);
		if ( !empty($res_mk) && $res_mk->error_code == 0 ) {
			
			$id_mk = $res_mk->data[0]->id_matkul;
			
			$temp_data = array(
				'id_sms' => $id_prodi,
				'id_smt' => $jdk->id_smt,
				'id_mk' => $id_mk,
				'nm_kls' => $jdk->kode_kls,
				'sks_mk' => $jdk->sks_mk,
				'sks_tm' => $jdk->sks_tm,
			    'sks_prak' => $jdk->sks_prak,
		   		'sks_prak_lap' => $jdk->sks_prak_lap,
				'sks_sim' => $jdk->sks_sim,
				'bahasan_case' => '',
				'tgl_mulai_koas' => '',
				'tgl_selesai_koas' => '' 
			);

			$temp_result = $proxy->InsertRecord($token, 'kelas_kuliah', json_encode($temp_data));

			if ( array_key_exists('error_desc', $temp_result['result']) && !empty($temp_result['result']['error_desc']) ) {
				// gagal
				DB::table('jadwal_kuliah')->where('id', $id_jdk)
					->update(['feeder_status' => '2']);
			}
		}

    }

    public function feederUpdate($id_jdk)
    {
    	$jdk = DB::table('jadwal_kuliah as jdk')
				->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
				->select('jdk.*', 'mk.kode_mk','mk.sks_mk','mk.sks_tm','mk.sks_prak','mk.sks_prak_lap','mk.sks_sim')
				->where('jdk.id', $id_jdk)->first();

		// Init soap
		$token = Feeder::getTokenNusoap();
		$proxy = Feeder::initSoap();


        $id_prodi = Feeder::idProdi($jdk->id_prodi);
        $id_pt = config('app.feeder_id_pt');

			
		$filter_jdk = "p.id_sms='".$id_prodi."' and kode_mk='".$jdk->kode_mk."' and p.id_smt='".$jdk->id_smt."'";

		$res_jdk = $proxy->GetRecord($token, 'kelas_kuliah', $filter_jdk);


		if ( !empty($res_jdk['result']) ) {
			if ( !array_key_exists('error_desc', $res_jdk['result']) ) {

		        $update = [
		        	'key' => [
		        		'id_kls' => $res_jdk['result']['id_kls']
		        	],
		        	'data' => [
		        		'nm_kls' => $jdk->kode_kls
		        	]
		        ];

		        $temp_result = $proxy->UpdateRecord($token, 'kelas_kuliah', json_encode($update));

		    }
	    }

    }
}
