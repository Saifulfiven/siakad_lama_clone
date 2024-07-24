<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Excel, Response, Session, Carbon;

class JadwalUjianController extends Controller
{
	/* Jadwal */
	    public function index(Request $r)
	    {

	    	if ( !empty($r->smt) ) {
	    		Session::set('jdu_semester', $r->smt);
	    	}

	    	if ( !empty($r->prodi) ) {
	    		Session::set('jdu_prodi', $r->prodi);
	    	}

	    	if ( !empty($r->jns) ) {
	    		Session::set('jdu_jenis_ujian', $r->jns);
	    	}

	    	if ( !Session::get('jdu_semester') ) {
	    		$this->setSessionFilter();
	    	}

	    	$query = Sia::jadwalUjian()
	    				->where('jdk.id_prodi', Session::get('jdu_prodi'))
	    				->where('jdk.id_smt', Session::get('jdu_semester'))
	    				->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'));

	    	if ( !empty($r->cari) ) {
	    		$query->where(function($q)use($r){
	    			$q->where('mk.kode_mk', 'like', '%'.$r->cari.'%')
	    				->orWhere('mk.nm_mk', 'like', '%'.$r->cari.'%')
	    				->orWhere('p.nama', 'like', '%'.$r->cari.'%');
	    		});
	    	}

	    	$data['jadwal'] = $query->paginate(10);
	    	// dd($data['jadwal']);
	    	return view('jadwal-ujian.index', $data);
	    }

	    private function setSessionFilter()
	    {
	    	Session::set('jdu_semester', Sia::sessionPeriode());
	    	$prodi_user = Sia::getProdiUser();
	    	Session::set('jdu_prodi', @$prodi_user[0]);
	    	Session::set('jdu_jenis_ujian', 'UTS');
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

	    protected function detail($id)
	    {
	    	$jdu = DB::table('jadwal_ujian as jdu')
	    			->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
	    			->leftJoin('ruangan as r', 'jdu.id_ruangan', 'r.id')
	    			->select('jdu.*', 'p.nama', 'r.nm_ruangan')
	    			->where('jdu.id', $id)->first();

	    	$peserta_ujian = Sia::pesertaUjian($id); ?>

	    	<div class="table-responsive">
	            <table border="0" class="table-form" width="100%">
                    <tr>
                        <td width="150">HARI</td>
                        <td> : <?= Rmt::hari($jdu->hari) ?></td>
                        <td>RUANGAN</td>
                        <td> : <?= $jdu->nm_ruangan ?></td>
                    </tr>
                    <tr>
                        <td>TANGGAL UJIAN</td>
                        <td> : <?= Carbon::parse($jdu->tgl_ujian)->format('d-m-Y') ?></td>
                        <td>JUMLAH PESERTA</td>
                        <td> : <?= $jdu->jml_peserta ?></td>
                    </tr>
                    <tr>
                        <td>JAM MASUK</td>
                        <td> : <?= substr($jdu->jam_masuk,0,5) ?> s/d <?= substr($jdu->jam_selesai,0,5) ?></td>
                        <td width="150px">PENGAWAS</td>
                        <td> : <?= $jdu->nama ?></td>
                    </tr>
	            </table>
	            
	            <hr>

	            <p><b>Peserta Ujian</b></p>
            	<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
					<thead class="custom">
	            		<tr>
	            			<th width="10">No</th>
	            			<th>NIM</th>
	            			<th>Nama</th>
	            		</tr>
	            	</thead>

	            	<tbody>
	            		<?php $no = 1 ?>
	            		<?php foreach( $peserta_ujian as $r ) { ?>
	            			<tr>
		            			<td><?= $no++ ?></td>	
		            			<td><?= $r->nim ?></td>	
		            			<td><?= $r->nm_mhs ?></td>
		            		</tr>
		            	<?php } ?>	
	            	</tbody>
            	</table>
            </div>
            <?php
	    }

	    public function getMhs(Request $r)
	    {
			$param = $r->input('query');
			if ( !empty($param) ) {
				$mhs = DB::table('krs_status as kst')
							->leftJoin('mahasiswa_reg as m1', 'kst.id_mhs_reg', 'm1.id')
							->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
								->select('kst.id_mhs_reg', 'm1.nim', 'm2.nm_mhs')
								->where('kst.id_smt', Session::get('jdu_semester'))
								->where('m1.id_prodi', Session::get('jdu_prodi'))
								->where(function($query)use($param){
									$query->where('m2.nm_mhs', 'like', '%'.$param.'%')
											->orWhere('m1.nim', 'like', '%'.$param.'%');
								})->orderBy('nim')->take(10)->get();
			} else {
				$mhs = DB::table('krs_status as kst')
							->leftJoin('mahasiswa_reg as m1', 'kst.id_mhs_reg', 'm1.id')
							->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
								->select('kst.id_mhs_reg', 'm1.nim', 'm2.nm_mhs')
								->where('kst.id_smt', Session::get('jdu_semester'))
								->where('m1.id_prodi', Session::get('jdu_prodi'))
								->where(function($query)use($param){
									$query->where('m2.nm_mhs', 'like', '%'.$param.'%')
											->orWhere('m1.nim', 'like', '%'.$param.'%');
								})->take(5)->get();
			}
			$data = [];
			foreach( $mhs as $r ) {
				$data[] = ['data' => $r->id_mhs_reg, 'value' => $r->nim.' - '.$r->nm_mhs];
			}
			$response = ['query' => 'Unit', 'suggestions' => $data];
			return Response::json($response,200);
	    }

	    public function printKartuUjian(Request $r)
	    {

	   		$mhs = Sia::kartuUjian(Session::get('jdu_semester'), Session::get('jdu_jenis_ujian')); 
	    	
	    	if ( empty($r->mhs) ) {

	    		$data['mahasiswa'] = $mhs->where('jdk.id_prodi', Session::get('jdu_prodi'))
	    								->groupBy('pu.id_mhs_reg')->get();
	    	} else {

	    		$data['mhs'] = $mhs->where('pu.id_mhs_reg', $r->mhs)->first();
	    		$data['matakuliah'] = Sia::ujianMhs(Session::get('jdu_semester'), Session::get('jdu_jenis_ujian'))
	    								->where('pu.id_mhs_reg', $r->mhs)->get();
	    		if ( !$data['mhs'] ) {
	    			echo '<center><h4>Mahasiswa tidak ditemukan. Pastikan mahasiswa ini telah mempunyai jadwal ujian</center>';
	    			exit;
	    		}
	    	}

	    	return view('jadwal-ujian.print-kartu-ujian', $data);
	    }

	    public function printAbsensiUjian(Request $r, $id)
	    {
	    	$data['jdu'] = DB::table('jadwal_ujian as jdu')
			    			->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
			    			->leftJoin('ruangan as r', 'jdu.id_ruangan', 'r.id')
			    			->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'jdu.id_jdk')
			    			->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
			    			->leftJoin('matakuliah as mk', 'mkur.id_mk', 'mk.id')
			    			->leftJoin('prodi as pr', 'jdk.id_prodi', 'pr.id_prodi')
			    			->select('jdu.*', 'p.nama', 'r.nm_ruangan','pr.*', 'mk.kode_mk','mk.nm_mk','jdk.kode_kls',
			    						DB::raw('(select semester_mk from '.Sia::prefix().'nilai
			    							where id_jdk='.Sia::prefix().'jdk.id limit 1) as smt'),
			    						DB::raw('(select group_concat(distinct d.gelar_depan," ", d.nm_dosen,", ", d.gelar_belakang SEPARATOR \'<br>\') from '.Sia::prefix().'dosen_mengajar as dm
			    								left join '.Sia::prefix().'jadwal_kuliah as jdk2 on jdk2.id = dm.id_jdk
			    								left join '.Sia::prefix().'dosen as d on dm.id_dosen = d.id
			    								where dm.id_jdk='.Sia::prefix().'jdk.id) as dosen')
			    					)
			    			->where('jdu.id', $id)->first();

	    	$data['peserta_ujian'] = Sia::pesertaUjian($id);
	    	$data['skala_nilai'] = DB::table('skala_nilai')
	    							->where('id_prodi', $data['jdu']->id_prodi)->get();

	    	return view('jadwal-ujian.print-absensi-ujian', $data);
	    }

	    public function printLabelUjian(Request $r)
	    {
	    	$jadwal = Sia::labelUjian();

	    	$data['jadwal'] = $jadwal->where('jdk.id_smt', Session::get('jdu_semester'))
				    			->where('jdk.id_prodi', Session::get('jdu_prodi'))
				    			->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'))
				    			->get();

	    	return view('jadwal-ujian.print-label-ujian', $data);
	    }

	    public function eksporPrint(Request $r)
	    {
	    	$query = Sia::jadwalUjian()
	    				->where('jdk.id_prodi', Session::get('jdu_prodi'))
	    				->where('jdk.id_smt', Session::get('jdu_semester'))
	    				->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'));

	    	$data['jadwal'] = $query->get();
	    	return view('jadwal-ujian.print', $data);
	    }

	    public function add(Request $r)
	    {
	    	if ( !Session::get('jdu_semester') ) {
	    		return redirect(route('jdu'));
	    	}

	    	$query = Sia::jadwalKuliah('x');

	    	$jadwal = $query->where('jdk.id_smt', Session::get('jdu_semester'))
							->where('jdk.id_prodi', Session::get('jdu_prodi'))
							->where(function($e){
							$e->where('jdk.hari','<>',0)
							->orWhere('jdk.hari','<>','');
							})
							->where('jdk.id_smt', Session::get('jdu_semester'))
							->whereNotIn('jdk.id', function($q){
								$q->select('jdu.id_jdk')->from('jadwal_ujian as jdu')
									->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'jdu.id_jdk')
									->where('jdk.id_smt', Session::get('jdu_semester'))
									->where('jdk.id_prodi', Session::get('jdu_prodi'))
									->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'));
								});

	    	if ( !empty($r->cari) ) {
	    		$jadwal->where(function($query)use($r){
	    			$query->where('mk.kode_mk', 'like', '%'.$r->cari.'%')
	    					->orWhere('mk.nm_mk', 'like', '%'.$r->cari.'%');
	    		});
	    	}

	    	$data['jadwal'] = $jadwal->orderBy('mk.nm_mk','asc')->paginate(10);

			$data['prodi'] = Sia::dataProdi(Session::get('jdu_prodi'));

	    	return view('jadwal-ujian.add', $data);
	    }

	    public function pengawas(Request $r)
	    {
			$param = $r->input('query');
			if ( !empty($param) ) {
				$pengawas = DB::table('pengawas')
								->where('nama', 'like', '%'.$param.'%')
								->get();
			} else {
				$pengawas = DB::table('pengawas')
								->where('nama', 'like', '%'.$param.'%')
								->get();
			}
			$data = [];
			foreach( $pengawas as $r ) {
				$data[] = ['data' => $r->id, 'value' => $r->nama];
			}
			$response = ['query' => 'Unit', 'suggestions' => $data];
			return Response::json($response,200);
	    }

	    public function store(Request $r)
	    {
	    	$errors = [];

	    	if ( $r->jml_kelas == 1 ) {

	    		$this->validate($r, [
		    		'tgl_ujian' => 'required',
		    		'hari' => 'required',
		    		'jam_masuk' => 'required',
		    		'jam_selesai' => 'required',
		    		'ruangan' => 'required',
		    		'pengawas' => 'required',
		    	]);

		    	if ( $r->jam_masuk >= $r->jam_selesai ) {
		    		$errors[] = 'Jam masuk harus lebih kecil dari jam keluar';
		    	}

		    	// Cek pengawas bentrok
		    	$rule1 = DB::table('jadwal_ujian as jdu')
		    			->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
		    			->where('jdu.tgl_ujian', Carbon::parse($r->tgl_ujian)->format('Y-m-d'))
		    			->where('jdu.hari', $r->hari)
		    			->where('jdk.id_prodi', Session::get('jdu_prodi'))
		    			->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'))
		    			->where('jdu.jam_masuk', substr($r->jam_masuk,0,5))
		    			->where('jdu.id_pengawas', $r->pengawas)->count();
		    	if ( $rule1 > 0 ) {
		    		$errors[] = 'Terdapat bentrok pada pengawas';
		    	}

	    	} else {
	    		$this->validate($r, [
		    		'tgl_ujian' => 'required',
		    		'hari' => 'required',
		    		'jam_masuk' => 'required',
		    		'jam_selesai' => 'required',
		    		'ruangan' => 'required',
		    		'pengawas' => 'required',
		    		'tgl_ujian_2' => 'required',
		    		'hari_2' => 'required',
		    		'jam_masuk_2' => 'required',
		    		'jam_selesai_2' => 'required',
		    		'ruangan_2' => 'required',
		    		'pengawas_2' => 'required',
		    	]);

		    	if ( $r->ruangan == $r->ruangan_2 && $r->jam_masuk == $r->jam_masuk_2 ) {
		    		$errors[] = 'Nama Ruangan 1 dan nama ruangan 2 tidak boleh sama';
		    	}

		    	if ( $r->pengawas == $r->pengawas_2 && $r->jam_masuk == $r->jam_masuk_2 ) {
		    		$errors[] = 'Pengawas 1 dan pengawas 2 tidak boleh sama';
		    	}

		    	if ( $r->jam_masuk >= $r->jam_selesai ) {
		    		$errors[] = 'Jam masuk-1 harus lebih kecil dari jam keluar-2';
		    	}

		    	if ( $r->jam_masuk_2 >= $r->jam_selesai_2 ) {
		    		$errors[] = 'Jam masuk-2 harus lebih kecil dari jam keluar-2';
		    	}

		    	// Cek pengawas bentrok
		    	$rule1 = DB::table('jadwal_ujian as jdu')
		    			->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
		    			->where('jdu.tgl_ujian', Carbon::parse($r->tgl_ujian)->format('Y-m-d'))
		    			->where('jdu.hari', $r->hari)
		    			->where('jdk.id_prodi', Session::get('jdu_prodi'))
		    			->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'))
		    			->where('jdu.jam_masuk', substr($r->jam_masuk,0,5))
		    			->where('jdu.id_pengawas', $r->pengawas)->count();
		    	if ( $rule1 > 0 ) {
		    		$errors[] = 'Terdapat bentrok pada pengawas 1';
		    	}

		    	$rule2 = DB::table('jadwal_ujian as jdu')
		    			->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
		    			->where('jdu.tgl_ujian', Carbon::parse($r->tgl_ujian_2)->format('Y-m-d'))
		    			->where('jdu.hari', $r->hari_2)
		    			->where('jdk.id_prodi', Session::get('jdu_prodi'))
		    			->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'))
		    			->where('jdu.jam_masuk', substr($r->jam_masuk_2,0,5))
		    			->where('jdu.id_pengawas', $r->pengawas_2)->count();
		    	if ( $rule1 > 0 ) {
		    		$errors[] = 'Terdapat bentrok pada pengawas 2';
		    	}

	    	}

	    	if ( count($errors) > 0 ) {
	    		return Response::json($errors,402);
	    	}

	    	try {

	    		DB::transaction(function()use($r){

	    			$data = [
	    				'id_jdk' => $r->id_jdk,
	    				'jml_peserta' => $r->jml_peserta, 
	    				'tgl_ujian' => Carbon::parse($r->tgl_ujian)->format('Y-m-d'),
	    				'hari' => $r->hari,
	    				'jam_masuk' => $r->jam_masuk,
	    				'jam_selesai' => $r->jam_selesai,
	    				'id_ruangan' => $r->ruangan,
	    				'id_pengawas' => $r->pengawas,
	    				'jenis_ujian' => Session::get('jdu_jenis_ujian')
	    			];

	    			$id = DB::table('jadwal_ujian')->insertGetId($data);

	    			// Insert Peserta Kelas
	    			$peserta = DB::table('nilai as n')
								->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
								->rightJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
								->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
								->where('n.id_jdk', $r->id_jdk)
								->select('m2.id as id_mhs_reg')
								->orderBy('m2.nim')
								->take($r->jml_peserta)
								->get();

					foreach( $peserta as $ps1 ) {
						$data_ps1 = ['id_mhs_reg' => $ps1->id_mhs_reg, 'id_jdu' => $id];
						DB::table('peserta_ujian')->insert($data_ps1);
					}

	    			if ( $r->jml_kelas == 2 ) {

	    				$data2 = [
		    				'id_jdk' => $r->id_jdk,
		    				'jml_peserta' => $r->jml_peserta_2, 
		    				'tgl_ujian' => Carbon::parse($r->tgl_ujian_2)->format('Y-m-d'),
		    				'hari' => $r->hari_2,
		    				'jam_masuk' => $r->jam_masuk_2,
		    				'jam_selesai' => $r->jam_selesai_2,
		    				'id_ruangan' => $r->ruangan_2,
		    				'id_pengawas' => $r->pengawas_2,
		    				'jenis_ujian' => Session::get('jdu_jenis_ujian')
	    				];

	    				$id2 = DB::table('jadwal_ujian')->insertGetId($data2);

		    			// Insert Peserta Kelas 2
		    			$peserta = DB::table('nilai as n')
									->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
									->rightJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
									->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
									->where('n.id_jdk', $r->id_jdk)
									->select('m2.id as id_mhs_reg')
									->orderBy('m2.nim')
									->skip($r->jml_peserta)
									->take($r->jml_peserta_2)
									->get();

						foreach( $peserta as $ps2 ) {
							$data_ps2 = ['id_mhs_reg' => $ps2->id_mhs_reg, 'id_jdu' => $id2];
							DB::table('peserta_ujian')->insert($data_ps2);
						}

	    			}

	    			DB::table('options')->where('id','jenis_ujian_berlangsung')
	    				->update(['value' => Session::get('jdu_jenis_ujian')]);

		    	});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}
	    	
	    	Rmt::success('Berhasil menyimpan data');

	    	return Response::json(['error' => 0,'msg' => ''], 200);
	    }

	    protected function edit($id)
	    {
	    	$jdu = DB::table('jadwal_ujian as jdu')
	    			->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
	    			->select('jdu.*', 'p.nama')
	    			->where('jdu.id', $id)->first(); ?>

	    	<?= csrf_field() ?>
	    	<input type="hidden" name="id" value="<?= $id ?>">

	    	<div class="table-responsive">
	            <table border="0" class="table-form" width="100%">

	                <!-- Ruang 1  -->
	                    <tr>
	                        <td width="150px">JUMLAH MAHASISWA</td>
	                        <td>
	                            <input type="text" class="form-control mw-1" id="jml-mhs-1" value="<?= $jdu->jml_peserta ?>" disabled="">
	                            <input type="hidden" name="jml_peserta" value="<?= $jdu->jml_peserta ?>">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td>TANGGAL UJIAN</td>
	                        <td>
	                            <input type="date" class="form-control mw-2" name="tgl_ujian" value="<?= $jdu->tgl_ujian ?>">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td>HARI</td>
	                        <td>
	                            <select class="form-control mw-1" name="hari" style="width: 100px">
	                                <option value="">-- Hari --</option>
	                                <?php for( $i = 1; $i <= 7; $i++ ) { ?>
	                                    <option value="<?= $i ?>" <?= $i == $jdu->hari ? 'selected' : '' ?>><?= Rmt::hari($i) ?></option>
	                                <?php } ?>
	                            </select>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td>JAM MASUK</td>
	                        <td>
	                            <input type="time" name="jam_masuk" class="form-custom mw-1" value="<?= $jdu->jam_masuk ?>"> s/d 
	                            <input type="time" name="jam_selesai" class="form-custom mw-1" value="<?= $jdu->jam_selesai ?>">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td>RUANGAN</td>
	                        <td>
	                            <select class="form-control mw-2" name="ruangan">
	                                <option value="">-- Ruangan --</option>
	                                <?php foreach( Sia::ruangan() as $j ) { ?>
	                                    <option value="<?= $j->id ?>" <?= $j->id == $jdu->id_ruangan ? 'selected' : '' ?>><?= $j->nm_ruangan ?></option>
	                                <?php } ?>
	                            </select>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td width="150px">PENGAWAS</td>
	                        <td>
	                            <div style="position: relative;">
	                                <div class="input-icon right"> 
	                                    <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
	                                    <input type="text" class="form-control" id="autocomplete-pengawas" value="<?= $jdu->nama ?>">
	                                    <input type="hidden" value="<?= $jdu->id_pengawas ?>" name="pengawas" id="pengawas">
	                                </div>
	                            </div>
	                        </td>
	                    </tr>
	                <!-- End -->

	            </table>
            </div>
            <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.autocomplete.js"></script>
			<script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.mockjax.js"></script>
            <script>
            	$(function(){
            	// Initialize ajax autocomplete for pengawas 1:
		            $('#autocomplete-pengawas').autocomplete({
		                serviceUrl: '<?= route('jdu_pengawas') ?>',
		                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
		                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
		                    return re.test(suggestion.value);
		                },
		                onSearchStart: function(data) {
		                    $('#spinner-autocomplete').show();
		                },
		                onSearchComplete: function(data) {
		                    $('#spinner-autocomplete').hide();
		                },
		                onSelect: function(suggestion) {
		                    $('#pengawas').val(suggestion.data);
		                },
		                onInvalidateSelection: function() {
		                }
		            });
		        // End ajax autocomplete
		    });
		    </script>
            <?php
	    }

	    public function update(Request $r)
	    {
	    	$errors = [];

    		$this->validate($r, [
	    		'tgl_ujian' => 'required',
	    		'hari' => 'required',
	    		'jam_masuk' => 'required',
	    		'jam_selesai' => 'required',
	    		'ruangan' => 'required',
	    		'pengawas' => 'required',
	    	]);

	    	if ( $r->jam_masuk >= $r->jam_selesai ) {
	    		$errors[] = 'Jam masuk harus lebih kecil dari jam keluar';
	    	}

	    	// Cek pengawas bentrok
	    	$rule1 = DB::table('jadwal_ujian')
	    			->where('tgl_ujian', Carbon::parse($r->tgl_ujian)->format('Y-m-d'))
	    			->where('hari', $r->hari)
	    			->where('jam_masuk', substr($r->jam_masuk,0,5))
	    			->where('id_pengawas', $r->pengawas)
	    			->where('id','<>',$r->id)->count();
	    	if ( $rule1 > 0 ) {
	    		$errors[] = 'Terdapat bentrok pada pengawas';
	    	}

	    	if ( count($errors) > 0 ) {
	    		return Response::json($errors,402);
	    	}

	    	try {

	    		DB::transaction(function()use($r){

	    			$data = [
	    				'tgl_ujian' => Carbon::parse($r->tgl_ujian)->format('Y-m-d'),
	    				'hari' => $r->hari,
	    				'jam_masuk' => $r->jam_masuk,
	    				'jam_selesai' => $r->jam_selesai,
	    				'id_ruangan' => $r->ruangan,
	    				'id_pengawas' => $r->pengawas
	    			];

	    			DB::table('jadwal_ujian')->where('id', $r->id)
	    				->update($data);
		    	});

	    	} catch(\Exception $e) {
	    		return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
	    	}

	    	Rmt::success('Berhasil menyimpan data');

	    	return Response::json(['error' => 0,'msg' => ''], 200);
	    }

	    public function delete($id)
	    {
	    	$data = DB::table('jadwal_ujian')->select('id')->where('id_jdk', $id)->get();

		    // Hapus data peserta ujian
	    	foreach( $data as $r ) {
	    		DB::table('peserta_ujian')->where('id_jdu', $r->id)->delete();
		    }

		    // Hapus data jadwal ujian
		    DB::table('jadwal_ujian')->where('id_jdk', $id)->delete();

	    	Rmt::success('Berhasil Menghapus data');
	    	return redirect()->back();
	    }

	/* end jadwal */
}
