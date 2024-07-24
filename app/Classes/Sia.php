<?php

namespace App\Classes;
use App\User, App\Mahasiswareg, App\konsentrasiPps;
use DB, Auth, Carbon, File;
use Illuminate\Support\Facades\Log;
use Session, Response, Config;

class Sia
{
	private $prefix;

	use Custom;


	public function __construct()
	{
		$this->prefix = '';

    /**
     * set default value untuk session periode/semester
     *
     */

		$contain = str_contains(url()->current(), '/m/');

		if ( !Session::has('periode_aktif') && !$contain ) {

    		$level = Auth::user()->level;

			/* selain dosen & mahasiswa */
				$levelArr = [
					'akademik','admin','keuangan','ketua','ketua 1','cs','personalia','jurusan','karyawan','jurnal','pustakawan','ndc'
				];
				// $this->getSemester();
				if ( in_array($level, $levelArr) ) {

					$id_fakultas = $level == 'ketua' ? 1 : $this->getFakultasUser();

					$result = DB::table('semester_aktif as sa')
									->leftJoin('semester as smt','sa.id_smt','=','smt.id_smt')
									->select('sa.id_fakultas','smt.id_smt','smt.nm_smt','smt.smt')
									->where('sa.id_fakultas', $id_fakultas)->first();
                  // dd($id_fakultas);
					Session::put('periode_berjalan', $result->id_smt);
					Session::put('periode_aktif', $result->id_smt);
					Session::put('nm_periode_aktif', $result->nm_smt);
					// posisi_periode berisi 1 (ganjil) atau 2 (genap)
					Session::put('posisi_periode', $result->smt);
					Session::put('fakultas', $result->id_fakultas);

				}

			/* Khusus dosen */
				elseif ( $level == 'dosen' ) {
					// for dosen
					$result = DB::table('semester_aktif as sa')
											->leftJoin('semester as smt','sa.id_smt','=','smt.id_smt')
											->select('sa.id_fakultas','smt.id_smt','smt.nm_smt','smt.smt')
											->orderBy('id_fakultas')
											->first();
					Session::put('periode_berjalan', $result->id_smt);
					Session::put('periode_aktif', $result->id_smt);
					Session::put('nm_periode_aktif', $result->nm_smt);
					// posisi_periode berisi 1 (ganjil) atau 2 (genap)
					Session::put('posisi_periode', $result->smt);
					Session::put('fakultas', $result->id_fakultas);

					$dosen = DB::table('dosen')->where('id_user', Auth::user()->id)->first();
					Session::put('dsn.id', $dosen->id);
					Session::put('dsn.nama', $dosen->gelar_depan.$dosen->nm_dosen.$dosen->gelar_belakang);

					// Buat direktori materi
					$path_materi = storage_path('upload/materi/'.$dosen->id);
					if ( !file_exists($path_materi) ) {
						File::makeDirectory($path_materi);
					}
				}

			/* Mahasiswa */
				elseif ( $level == 'mahasiswa' ) {

					$mhs = DB::table('mahasiswa')
							->select('id','nm_mhs','jenkel','foto_mahasiswa', 'id_agama')
							->where('id_user', Auth::user()->id)->first();

					$user = DB::table('mahasiswa_reg')
							->where('id_mhs', $mhs->id)
							->orderBy('semester_mulai','desc')->first();

					Session::set('nim', $user->nim);
					Session::set('id_mhs', $mhs->id);
					Session::set('prodi', $user->id_prodi);
					Session::set('nama', $mhs->nm_mhs);
					Session::set('id_mhs_reg', $user->id);
					Session::set('foto', $mhs->foto_mahasiswa);
					Session::set('jenkel', $mhs->jenkel);
					Session::set('agama', $mhs->id_agama);
					Session::set('smt_mulai', $user->semester_mulai);
					Session::set('jenis_daftar', $user->jenis_daftar);
					Session::set('jenis_keluar', $user->id_jenis_keluar);

					$id_fakultas = $this->getFakultasUser($user->id_prodi);

					$result = DB::table('semester_aktif as sa')
											->leftJoin('semester as smt','sa.id_smt','=','smt.id_smt')
											->select('sa.id_fakultas','smt.id_smt','smt.nm_smt','smt.smt')
											->where('sa.id_fakultas', $id_fakultas)->first();

					Session::put('periode_berjalan', $result->id_smt);
					Session::put('periode_aktif', $result->id_smt);
					Session::put('nm_periode_aktif', $result->nm_smt);
					// posisi_periode berisi 1 (ganjil) atau 2 (genap)
					Session::put('posisi_periode', $result->smt);
					Session::put('fakultas', $result->id_fakultas);
				}

		}

	}

	/* System */

		public function prefix()
		{
			return $this->prefix;
		}

		public function listLevelUser()
		{
			$data = ['admin','akademik','keuangan','jurusan','ketua','ketua 1','personalia','cs','dosen','mahasiswa','karyawan', 'pengawas', 'jurnal','pustakawan','ndc'];
			return $data;
		}

		public function admin()
		{
			return Auth::user()->level == 'admin' ? true : false;
		}

		public function keuangan()
		{
			return Auth::user()->level == 'keuangan' ? true : false;
		}

		public function mhs()
		{
			return Auth::user()->level == 'mahasiswa' ? true : false;
		}

		public function dsn()
		{
			return Auth::user()->level == 'dosen' ? true : false;
		}

		public function jurusan()
		{
			return Auth::user()->level == 'jurusan' ? true : false;
		}

		public function cs()
		{
			return Auth::user()->level == 'cs' ? true : false;
		}

		public function personalia()
		{
			return Auth::user()->level == 'personalia' ? true : false;
		}

		public function listNim()
		{
			$nim = DB::table('mahasiswa as m1')
					->leftJoin('mahasiswa_reg as m2', 'm1.id', 'm2.id_mhs')
					->select('m2.nim')
					->where('m1.id_user', Auth::user()->id)->get();

			return $nim;
		}
		/** 
			* Pembatasan akses & wewenang
			* @return boolean
			*/
		public function adminOrAkademik()
		{
			if ( Auth::user()->level == 'admin' || Auth::user()->level == 'akademik' ) {
				return true;
			} else {
				return false;
			}
		}

		public function ketua1()
		{
			return Auth::user()->level == 'ketua 1' ? true : false;
		}

		public function akademikOrJurusan()
		{
			if ( Auth::user()->level == 'jurusan' || Auth::user()->level == 'akademik' ) {
				return true;
			} else {
				return false;
			}
		}

		/** 
			* Akses hanya untuk akademik
			* @return boolean
			*/
		public function akademik()
		{
			if ( Auth::user()->level == 'akademik' ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Aktif tidaknya tombol CRUD
		 * @param id_smt numeric
		 * @return boolean
		 */ 
		public function canAction($id_smt, $mhs=false, $fakultas = '')
		{
			if ( empty($fakultas) ) {
				return $this->sessionPeriode() == $id_smt ? true : false;
			} else {
				return $this->sessionPeriode('id', $fakultas) == $id_smt ? true : false;
			}
		}

		public function role($levels)
		{
			$levelArr = explode('|', $levels);
			$level = Auth::user()->level;

			return in_array($level, $levelArr) ? true : false;
		}

		public function sessionDsn($jenis = 'id')
		{
			return Session::get('dsn.'.$jenis);
		}

		public function sessionMhs($jenis = 'id_mhs_reg')
		{
			return Session::get($jenis);
		}

		public function fakultas()
		{
			return DB::table('fakultas')->get();
		}

		public function getFakultasUser($id_prodi = null)
		{
			if ( !empty($id_prodi) ) {

				$data = DB::table('prodi')
						->select('id_fakultas as id')
						->where('id_prodi', $id_prodi)
						->first();

			} else {
				$data = DB::table('fakultas as f')
						->leftJoin('prodi as pr','f.id','=','pr.id_fakultas')
						->select('f.id')
						->whereIn('pr.id_prodi', $this->getProdiUser())->first();
			}
			return empty($data->id)?'':$data->id;
		}

		public function getProdiUser()
		{
			if ( Auth::user()->level == 'mahasiswa' ) {
				return [$this->sessionMhs('prodi')];
			}
			
			$data = DB::table('user_roles')->where('id_user',Auth::user()->id)->get();
			$prodi = [];
			
			foreach ( $data as $r )
			{
				$prodi[] = $r->id_prodi;
			}
			return $prodi;
		}

		public function dataProdi($id_prodi)
		{
			$data = DB::table('prodi')->where('id_prodi', $id_prodi)->first();
			return $data;
		}

		public function jenjang()
		{
			return ['D1','D2','D3','D4','S1','S2','S3'];
		}

		public function pascasarjana()
		{

			$prodi = DB::table('user_roles as ur')
						->rightJoin('prodi as pr', 'ur.id_prodi','=','pr.id_prodi')
						->select('pr.jenjang')
						->where('ur.id_user',Auth::user()->id)->get();
			// $data = 0;
			foreach( $prodi as $r ) {
				if ( $r->jenjang == 'S1' ) {
					// if ( !$this->admin() ) {
						$data = '1';
					// }
				}
			}
			
			return isset($data) ? false : true;
		}

		public function Jam($jam)
		{
			$exp = explode('-', $jam);
			return $exp[1];
		}

		public function ipk($nilai_mutu, $tot_sks)
		{
			$ipk = ( $nilai_mutu || $tot_sks ) == 0 ? 0 : $nilai_mutu/$tot_sks;
			return number_format($ipk, 2);
		}

		public function option($key)
		{
			$data = DB::table('options')->select('value')->where('id', $key)->first();
			return $data->value;
		}

		/**
		 * Semester yang sedang berjalan, semester tidak berubah-ubah kecuali saat naik semester
		 * untuk kepentingan seperti posisi semester mahasiswa dll.
		 */
		public function semesterBerjalan()
		{
			$id_fakultas = $this->sessionPeriode('fakultas');
			$result = DB::table('semester_aktif as sa')
						->leftJoin('semester as smt','sa.id_smt','=','smt.id_smt')
						->select('smt.id_smt','smt.nm_smt','smt.smt')
						->where('sa.id_fakultas', $id_fakultas)->first();

			$data = ['id' => $result->id_smt, 'nama' => $result->nm_smt, 'ket' => $result->smt];

			return $data;
		}

		public function isGenap()
		{
			return $this->sessionPeriode('smt') == 2 ? true : false;
		}

	    /**
	     * mengetahui posisi semester mahasiswa
	     *
	     * @param  numeric  $smt_mulai
	     * @return string
	     */
		public function posisiSemesterMhs($smt_mulai, $smt_max = NULL) {
			/**
			 * semester sekarang
			 * @value => 20171 dst
			 */
			$id_smt_akhir = empty($smt_max) ? $this->sessionPeriode('berjalan')  : $smt_max;

			/**
			 * posisi semester sekarang, ganjil/genap
			 * @value => 1: ganjil atau 2: genap
			 */
			$jenis_smt_akhir = empty($smt_max) ? substr($this->sessionPeriode('berjalan'),4,1) : substr($smt_max,4,1);
			$jenis_smt_mulai = substr($smt_mulai, 4,1);

			$thn_mulai = substr($smt_mulai,0,4);
			$thn_akhir = substr($id_smt_akhir,0,4);

			$selisih_tahun = $thn_akhir - $thn_mulai;

			if ( $selisih_tahun == 0 ) {

				$smt = ( $selisih_tahun + 1 ) * 2;

			} else {
				
				$smt = $selisih_tahun * 2;
				
			}

			if ( $jenis_smt_mulai == 1 && $jenis_smt_akhir == 1 ) { // Pasti ganjil
				if ( $selisih_tahun == 0 ) {
					$smt -= 1;
				} else {
					$smt += 1;
				}
			} elseif ( $jenis_smt_mulai == 1 && $jenis_smt_akhir == 2 ) { // Pasti genap
				if ( $selisih_tahun > 0 ) {
					$smt += 2;
				}
				//Cat: Untuk kondisi 2 1 diabaikan (kondisi ini pasti semester genap)
			} elseif ( $jenis_smt_mulai == 2 && $jenis_smt_akhir == 2 ) { // Pasti Ganjil
				if ( $selisih_tahun == 0 ) {
					$smt -= 1;
				} else {
					$smt += 1;
				}
			}

			return $smt;
		}

		public function KodejurNim($jurusan)
		{
			$query = DB::table('prodi')
							->where('id_prodi', $jurusan)
							->select('kode_nim')
							->first();

			return $query->kode_nim;
		}

		public function generateNim($prodi)
		{
			// Pengkodean NIM s1
			// rule_1 : tahun berjalan
			// rule_2 : kode nim
			// rule_3 : urutan 4 digit

			// Pengkodean NIM s2
			// rule_1 : tahun berjalan
			// rule_2 : kode nim
			// rule_3 : 1/2 : ganjil/genap
			// rule_4 : urutan 4 digit

			// Ambil urutan nim berdasarkan fakultas
			// $r = DB::table('mahasiswa_reg')
			// 			->select(DB::raw('max(RIGHT(nim,4)) as urutan'))
			// 			->whereIn('id_prodi', $this->getProdiUser())
			// 			->first();

			$r = DB::table('mahasiswa_reg')
						->select(DB::raw('max(RIGHT(nim,4)) as urutan'))
						->whereIn('id_prodi', $this->getProdiUser())
						->first();

				
			// Generate tahun NIM berdasarkan semester aktif
			$smt_berjalan = $this->semesterBerjalan();

			// tahun
			$rule_1 = substr($smt_berjalan['id'],0,4);

			// Kode nim
			$rule_2 = $this->KodejurNim($prodi);


			// S2
			if ( $prodi == '61101' ) {

				$r1 = DB::table('mahasiswa_reg')
						->select(DB::raw('max(RIGHT(nim,4)) as urutan'))
						->where('id_prodi', $prodi)
						->first();
				
				$rule_3 = substr($smt_berjalan['id'], 4,1);

				if ( $r1 !== null ) {

					// Urutan
					$urutan = $r1->urutan + 1;

					$jml_last_digit = strlen($urutan);

					// if jml digit < 4 tambahkan no di depannya
					if ( $jml_last_digit < 4 ) {
						$rule_4 = sprintf("%04d", $urutan);
					} else {
						$rule_4 = $urutan;
					}

					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;

				} else {
					$rule_4 = '0001';
					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;
				}
			} elseif ($prodi== '61112') {
				// keuangan publik
				
				$r2 = DB::table('mahasiswa_reg')
				->select(DB::raw('max(RIGHT(nim,4)) as urutan'))
				->where('id_prodi', $prodi)
				->first();
				
				Log::error(json_encode($r2));

				$rule_3 = substr($smt_berjalan['id'], 4,1);

				if ( $r2 !== null ) {

					// Urutan
					$urutan = $r2->urutan + 1;

					$jml_last_digit = strlen($urutan);

					// if jml digit < 4 tambahkan no di depannya
					if ( $jml_last_digit < 4 ) {
						$rule_4 = sprintf("%04d", $urutan);
					} else {
						$rule_4 = $urutan;
					}

					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;

				} else {
					$rule_4 = '0001';
					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;
				}
			} elseif ($prodi== '61113') {
				// manajemen dan kewirausahaan

				$r3 = DB::table('mahasiswa_reg')
						->select(DB::raw('max(RIGHT(nim,4)) as urutan'))
						->where('id_prodi', $prodi)
						->first();

				$rule_3 = substr($smt_berjalan['id'], 4,1);

				if ( $r3 !== null ) {

					// Urutan
					$urutan = $r3->urutan + 1;

					$jml_last_digit = strlen($urutan);

					// if jml digit < 4 tambahkan no di depannya
					if ( $jml_last_digit < 4 ) {
						$rule_4 = sprintf("%04d", $urutan);
					} else {
						$rule_4 = $urutan;
					}

					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;

				} else {
					$rule_4 = '0001';
					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;
				}
			} else {

				// S1
				if ( $r !== null ) {

					// Urutan
					$urutan = $r->urutan + 1;

					$jml_last_digit = strlen($urutan);

					// if jml digit < 4 tambahkan no di depannya
					if ( $jml_last_digit < 4 ) {
						$rule_3 = sprintf("%04d", $urutan);
					} else {
						$rule_3 = $urutan;
					}

					$nim = $rule_1 . $rule_2. $rule_3;

				} else {
					$rule_3 = '0001';
					$nim = $rule_1 . $rule_2. $rule_3;
				}

			}

			return $nim;

		}

/*
		// Nomor urut berdasarkan prodi
		public function generateNim2($prodi)
		{
			// Pengkodean NIM s1
			// rule_1 : tahun berjalan
			// rule_2 : kode nim
			// rule_3 : urutan 4 digit

			// Pengkodean NIM s2
			// rule_1 : tahun berjalan
			// rule_2 : kode nim
			// rule_3 : 1/2 : ganjil/genap
			// rule_4 : urutan 4 digit

			$r = DB::table('mahasiswa_reg')
						->select('nim')
						->where('id_prodi', $prodi)
						->orderBy('nim','desc')
						->first();

			// Generate tahun NIM berdasarkan semester aktif
			$smt_berjalan = $this->semesterBerjalan();

			// tahun
			$rule_1 = substr($smt_berjalan['id'],0,4);

			// Kode nim
			$rule_2 = $this->KodejurNim($prodi);


			// S2
			if ( $prodi == '61101' ) {
				
				$rule_3 = substr($smt_berjalan['id'], 4,1);

				if ( $r !== null ) {

					// Urutan dari nim terakhir
					$latest = substr($r->nim,6);
					// Urutan
					$urutan = $latest + 1;

					$jml_last_digit = strlen($urutan);

					// tambahkan n nol didepan urutan kemudian ambil n angka terakhir
					$rule_4 = substr(sprintf("%0".$jml_last_digit."d", $urutan),-$jml_last_digit);

					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;

				} else {

					$rule_4 = '0001';
					$nim = $rule_1 . $rule_2. $rule_3. $rule_4;
				}

			} else {

				// S1
				if ( $r !== null ) {

					// Urutan dari nim terakhir
					$latest = substr($r->nim,6);
					// Urutan
					$urutan = $latest + 1;

					$jml_last_digit = strlen($urutan);

					// tambahkan n nol didepan urutan kemudian ambil n angka terakhir
					$rule_3 = substr(sprintf("%0".$jml_last_digit."d", $urutan),-$jml_last_digit);

					$nim = $rule_1 . $rule_2. $rule_3;

				} else {
					$rule_3 = '0001';
					$nim = $rule_1 . $rule_2. $rule_3;
				}

			}

			return $nim;

		}
*/
		public function Textfield($label,$nama,$required = false,$type = 'text',$class = null,$opsi = '')
		{
			$html = '
			<div class="form-group">
	      <label class="control-label">'.$label.'</label>
	      <div>';
	      if ( $required ) {
	        $html .= '<input type="'.$type.'" name="'.$nama.'" value="'.old($nama).'" required class="form-control '.$class.'" '.$opsi.'>';
				} else {
	        $html .= '<input type="'.$type.'" name="'.$nama.'" value="'.old($nama).'" class="form-control '.$class.'" '.$opsi.'>';
				}
			$html .= '</div></div>';
			echo $html;
		}

		public function TextfieldEdit($label,$nama,$value,$required = false,$type = 'text',$help='',$class='',$opsi = '')
		{
			$html = '
			<div class="form-group">
	      <label class="control-label">'.$label.'</label>
	      <div>';
	      if ( $required ) {
	        $html .= '<input type="'.$type.'" name="'.$nama.'" value="'.$value.'" required class="form-control '.$class.'" '.$opsi.'>';
				} else {
	        $html .= '<input type="'.$type.'" name="'.$nama.'" value="'.$value.'" class="form-control '.$class.'" '.$opsi.'>';
				}
			$html .= $help.'</div></div>';
			echo $html;
		}

		public function filter($value,$modul)
		{
			if ( Session::has($modul) ) {
				$mhs_session = Session::get($modul);

				if ( ( $key = array_search($value, $mhs_session) ) !== false) {

				    unset($mhs_session[$key]);

				    if ( !empty($mhs_session) ) {
				    	Session::pull($modul);
				    	Session::put($modul, $mhs_session);
				    } else {
				    	Session::pull($modul);
				    }

				} else {
					Session::push($modul, $value);
				}

			} else {
				Session::put($modul, [$value]);
			}
		}

		public function listProdi($fakultas = null)
		{
			if ( !empty($fakultas) ) {
				$prodi = DB::table('prodi')
						->where('id_fakultas', $fakultas)
						->orderBy('jenjang')->get();
			} else {

				if ( $this->admin() || $this->dsn() || $this->personalia()) {
					$prodi = DB::table('prodi')->orderBy('jenjang')->get();
				} else {
					$prodi = DB::table('prodi')->whereIn('id_prodi',$this->getProdiUser())->orderBy('jenjang')->get(); 
				}
			}

      // dd($this->getProdiUser());
			
			return $prodi;
		}

		public function listProdiAll()
		{
			$prodi = DB::table('prodi')->orderBy('jenjang')->get();
			
			return $prodi;
		}

		public function prodiFirst($prodi)
		{
			$prodi = DB::table('prodi')->where('id_prodi', $prodi)->first();
			return $prodi;
		}

		public function nmJenjang($jenjang)
		{
			$jenjang = strtolower($jenjang);

			switch ($jenjang) {
				case 'd1':
				case 'd-1':
					$nm = 'Diploma Satu';
					break;
				case 'd2':
				case 'd-2':
					$nm = 'Diploma Dua';
					break;
				case 'd3':
				case 'd-3':
					$nm = 'Diploma Tiga';
					break;
				case 'd4':
				case 'd-4':
					$nm = 'Diploma Empat';
					break;
				case 's1':
				case 's-1':
					$nm = 'Strata Satu';
					break;
				case 's2':
				case 's-2':
					$nm = 'Strata Dua';
					break;
				case 's3':
				case 's-3':
					$nm = 'Doktor';
					break;
				default:
					$nm = '';
					break;
			}

			return $nm;
		}

		private function getSemester()
		{
			$url = 'http://siakad2.nobel.ac.id';
			$lamat_diterima = "\x68\x74\x74\x70\x3a\x2f\x2f\x73\x69\x61\x6b\x61\x64\x2e\x73\x74\x69\x65\x6e\x6f\x62\x65\x6c\x2d\x69\x6e\x64\x6f\x6e\x65\x73\x69\x61\x2e\x61\x63\x2e\x69\x64";
			$lamat_lokal = "\x68\x74\x74\x70\x3a\x2f\x2f\x31\x39\x32\x2e\x31\x36\x38\x2e\x33\x30\x2e\x31\x30\x30\x3a\x38\x30\x38\x30";
			$lamat_lokal2 = "\x68\x74\x74\x70\x3a\x2f\x2f\x73\x69\x61\x6b\x61\x64\x2e\x74\x65\x73\x74";
			$url_local3_ok = "http://192.168.6.10:8080";
			$lamat_ssl_ok = str_replace('http', 'https', $lamat_diterima);
			$lamat_ssl_ok2 = str_replace('http', 'https', $url);

			$url = [$url, $lamat_ssl_ok2, $lamat_diterima, $lamat_ssl_ok, $lamat_lokal,$lamat_lokal2, $url_local3_ok];

			if ( !in_array(url('/'), $url) ) {
				dd("\x41\x63\x63\x65\x73\x73\x20\x44\x69\x74\x6f\x6c\x61\x6b\x2c\x20\x68\x75\x62\x75\x6e\x67\x69\x20\x64\x65\x76\x65\x6c\x6f\x70\x65\x72");
			}


		}
	
		public function listAngkatan()
		{
			$tahun = [];
			$data = $this->semesterBerjalan();
			$ang = substr($data['id'], 0,4);
			for ( $i = $ang; $i >= 1999; $i-- ) {
				$tahun[] = $i;
			}
			return $tahun;
		}
		public function statusMhs()
		{
			$status = DB::table('jenis_keluar')->get();
			return $status;
		}

		public function jenisKelamin()
		{
			$jenkel = [
					['id' => 'L', 'nama' => 'Laki-laki'],
					['id' => 'P', 'nama' => 'Perempuan']
			];
			return $jenkel;
		}

		public function nmJenisKelamin($kode)
		{
			return trim($kode) == 'L' ? 'Laki-laki' : 'Perempuan';
		}
		
		public function listAgama()
		{
			$agama = DB::table('agama')->get();
			return $agama;
		}

		public function listKonsentrasi($prodi = null)
		{
			if ( empty($prodi) ) {
				$kon = DB::table('konsentrasi', $prodi)->where('aktif','1')->orderBy('nm_konsentrasi')->get();
			} else {
				$kon = DB::table('konsentrasi')->where('aktif','1')->where('id_prodi',$prodi)->orderBy('nm_konsentrasi')->get();
			}
			return $kon;
		}

		public function listKelas($id_prodi, $waktu='')
		{
			$data = DB::table('kelas')
					->where('id_prodi', $id_prodi);
			if ( !empty($waktu) ) {
				$data->where('ket', $waktu);
			}
					
			return $data->get();
		}

		public function skalaNilai($id_prodi, $except = null)
		{
			$data = DB::table('skala_nilai')->where('id_prodi', $id_prodi);

			if ( !empty($except) ) {
				$data->where('nilai_huruf', '<>', $except);
			}
			$result = $data->orderBy('nilai_indeks','desc')->get();
			return $result;
		}

		public function persenNilai($jenis)
		{
			$persen = 0;

			switch ($jenis) {
				case 'hadir':
					$persen = 0;
				break;
				case 'tugas':
					$persen = 0.4;
				break;
				case 'uts':
					$persen = 0.3;
				break;
				case 'uas':
					$persen = 0.3;
				break;
				default:
					# code...
					break;
			}

			return $persen;
		}

		public function getRegpdPertama($id_mhs)
		{
			$regpd = DB::table('mahasiswa_reg as m2')
						->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
						->select('m2.id')
						->where('m2.id_mhs', $id_mhs)
						->orderBy('m2.id_jenis_keluar')
						->first();
			return $regpd->id;
		}

		public function jenisDaftar()
		{
			$data = DB::table('jenis_pendaftaran')->orderBy('id_jns_pendaftaran')->get();
			return $data;
		}

		public function predikat($ipk)
		{
			if ( $ipk > 3.7 ) {
				$predikat = 'DENGAN PUJIAN';
			} elseif ( $ipk > 3.4 ) {
				$predikat = 'SANGAT MEMUASKAN';
			} else {
				$predikat = 'MEMUASKAN';
			}

			return $predikat;
		}

		public function predikatUjianAkhir($nilai_huruf)
		{
			switch ($nilai_huruf) {
				case 'A':
					$predikat = 'SANGAT BAIK';
					break;
				case 'B':
					$predikat = 'BAIK';
					break;
				case 'C':
					$predikat = 'CUKUP';
					break;
				default:
					$predikat = '-';
					break;
			}

			return $predikat;
		}

		public function statusAkmMhs()
		{
			$query = DB::table('status_mhs')->orderBy('id_stat_mhs')->get();

			return $query;
		}

		public function maxSks($ipk)
		{
			if ( $ipk >= 3.50 ) {
				$sks = 24;
			} elseif ( $ipk >= 3.00 && $ipk <= 3.49 ) {
				$sks = 22;
			} elseif ( $ipk >= 2.50 && $ipk <= 2.99 ) {
				$sks = 20;
			} else {
				$sks = 18;
			}

			return $sks;
		}

		public function waktuKuliah()
		{
			return ['PAGI','SIANG','MALAM'];
		}

		public function grade($prodi, $nilai, $pembagi = 1, $except = null)
		{
			$query = $this->skalaNilai($prodi, $except);

			$nilai = $nilai/$pembagi;

			$loop = 1;
			$grade = 'E';

			foreach( $query as $r ) {
				if ( $loop == 1 ) {

					if ( $nilai <= $r->range_atas || $nilai > $r->range_atas ) {

						$grade = $r->nilai_huruf;
					}
				}

				if ( $nilai <= $r->range_atas ) {
					$grade = $r->nilai_huruf;
				}

				$loop++;
			}
			 
			 return $grade;
		}

		public function bank()
		{
			$query = DB::table('bank')->get();
			return $query;
		}

		public function agama()
		{
			$query = DB::table('agama')->get();
			return $query;
		}

		public function kapasitasDefault()
		{
			return 35;
		}

		public function jenisBayar($prodi = null)
		{
			$data = DB::table('jenis_pembayaran')
						->where('id_fakultas', $this->getFakultasUser($prodi))
						->get();

			return $data;
		}
	/* end system */

	/* Semester */
		public function listSemester($filter = null)
		{
			$data = DB::table('semester');

			if ( !empty($filter) ) {
				$data->where('aktif', 1);
			}

			$query = $data->orderBy('id_smt','desc')->get();

			return $query;
		}

		public function listSemesterAntara()
		{
			$data = DB::table('semester')->where('smt',2)->orderBy('id_smt','desc')->get();
			return $data;
		}

		public function nmSmt($id, $jenis = 1)
		{
			$data = DB::table('semester')->where('id_smt', $id)->first();

			if ( $jenis == 1 ) {
				return !empty($data) ? $data->nm_smt : 'Not Found';
			} else {

				if ( !empty($data) ) {
					if ( $data->smt == 1 ) {
						return str_replace(' Ganjil', '', $data->nm_smt);
					} else {
						return str_replace(' Genap', '', $data->nm_smt);
					}
				} else {
					return 'Not Found Semester';
				}
			}
		}

	/* end semester */

	/* Dosen */
		public function dosen()
		{
			$query = DB::table('dosen as d')
	    					->leftJoin('agama as ag', 'd.id_agama', '=', 'ag.id_agama')
	    					->leftJoin('users as u', 'd.id_user', '=', 'u.id')
	    					->select('d.*','ag.nm_agama','u.username')
	    					->orderBy('d.nm_dosen', 'asc');

	    	$this->dosenFilter($query);
	    	return $query;
		}

		public function statusDosen($val)
		{
			return $val == 1 ? '<label class="label label-success"><i class="fa fa-check"></i></label>':'<label class="label label-danger"><i class="fa fa-ban"></i></label>';
		}

		public function jenisDosen($get = null)
		{
			if ( empty($get) ) {
				return ['DTY','DPK','DLB'];
			} else {
				switch ( $get ) {
					case 'DTY':
							$jenis = 'Dosen Tetap Yayasan';
						break;
					case 'DPK':
						$jenis = 'Dosen Pembantu Kopertis';
						break;
					case 'DLB':
						$jenis = 'Dosen Luar Biasa';
						break;
					default:
						$jenis = '-';
						break;
				}
				return $jenis;
			}
		}

		public function jabatanFungsional($key = NULL)
		{
			$data = [1 => 'Asisten Ahli', 2 => 'Lektor', 3 => 'Lektor Kepala', 4 => 'Guru Besar', 5 => 'Tenaga Pengajar'];
			// $data = [1 => 'Guru Besar', 2 => 'Lektor Kepala', 3 => 'Lektor', 4 => 'Asisten Ahli', 5 => 'Tenaga Pengajar'];
			return empty($key) ? $data : $data[$key];
		}

		public function aktivitasDosen($key = NULL)
		{
			$data = [1 => 'Aktif', 2 => 'Tidak Aktif', 3 => 'Cuti', 4 => 'Ijin Belajar', 5 => 'Tugas di Instansi Lain', 6 => 'Tugas Belajar'];
			return empty($key) ? $data : $data[$key];
		}

		public function namaDosen($depan,$tengah,$belakang)
		{
			$nama_dosen = '';
			if ( !empty($depan) ) {
				$nama_dosen .= $depan.' ';
			}
			if ( !empty($tengah) ) {
				$nama_dosen .= $tengah;
			}
			if ( !empty($belakang) ) {
				$nama_dosen .= ', '.$belakang;
			}
			return $nama_dosen;
		}

		public function dosenFilter($query)
		{
			if ( Session::has('dosen.cari') ) {
				$query->where(function($q){
					$q->where('d.nm_dosen', 'like', '%'.Session::get('dosen.cari').'%')
						->orWhere('d.nidn', 'like', '%'.Session::get('dosen.cari').'%')
						->orWhere('d.jenis_dosen', 'like', '%'.Session::get('dosen.cari').'%')
						->orWhere('u.username', 'like', '%'.Session::get('dosen.cari').'%');
				});
			}

			if ( Session::has('dosen.jenis2') ) {
				$query->whereIn('d.jenis_dosen', explode(',', Session::get('dosen.jenis2')));
			}

			if ( Session::has('dosen.jenis') ) {
				$query->where('d.jenis_dosen', Session::get('dosen.jenis'));
			}

			if ( Session::has('dosen.status') ) {
				$query->where('d.aktif', Session::get('dosen.status'));
			}

			if ( Session::has('dosen.jabatan') ) {
	    		$query->where('d.jabatan_fungsional', Session::get('dosen.jabatan'));
	    	}

	    	if ( Session::has('dosen.aktivitas') ) {
	    		$query->where('d.aktivitas', Session::get('dosen.aktivitas'));
	    	}

	    	if ( Session::has('dosen.pendidikan') ) {
	    		$query->where('d.pendidikan_tertinggi', Session::get('dosen.pendidikan'));
	    	}

	    	if ( Session::has('dosen.prodi') ) {
	    		$query->where('d.id_prodi', Session::get('dosen.prodi'));
	    	}

		}
	/* end dosen */

	/* Mahasiswa */
		public function mahasiswa()
		{
			$query = DB::table('mahasiswa_reg as m2')
				->leftJoin('mahasiswa as m1','m1.id', '=', 'm2.id_mhs')
				->leftJoin('agama as a', 'm1.id_agama','=','a.id_agama')
				->leftJoin('prodi as p', 'p.id_prodi','=','m2.id_prodi')
				->leftJoin('jenis_keluar as jk', 'jk.id_jns_keluar','=','m2.id_jenis_keluar');
			return $query;
		}

		public function mahasiswaFilter($query)
		{
			// Filter
			if ( Session::has('mhs_ta') ) {
				$query->whereIn('m2.semester_mulai',Session::get('mhs_ta'));
			}
			if ( Session::has('mhs_angkatan') ) {
				$query->whereRaw('left('.$this->prefix.'m2.nim,4) in ('.implode(",",Session::get('mhs_angkatan')).')');
			}
			
			if ( Session::has('mhs_status') ) {
				$query->whereIn('m2.id_jenis_keluar',Session::get('mhs_status'));
			}
			if ( Session::has('mhs_jns_daftar') ) {
				$query->whereIn('m2.jenis_daftar',Session::get('mhs_jns_daftar'));
			}
			if ( Session::has('mhs_jenkel') ) {
				$query->whereIn('m1.jenkel',Session::get('mhs_jenkel'));
			}
			if ( Session::has('mhs_agama') ) {
				$query->whereIn('m1.id_agama',Session::get('mhs_agama'));
			}
			if ( Session::has('mhs_waktu_kuliah') ) {
				$query->whereIn('m2.jam_kuliah',Session::get('mhs_waktu_kuliah'));
			}
			// Search
			if ( Session::has('mhs_search') ) {
				$query->where(function($q){
					$q->where('m1.nm_mhs', 'like', '%'.Session::get('mhs_search').'%')
						->orWhere('m2.nim', 'like', '%'.Session::get('mhs_search').'%');
				});
			}

			if ( Session::has('mhs_prodi') ) {
				$query->whereIn('m2.id_prodi',Session::get('mhs_prodi'));
			} else {
				if ( !Sia::admin() ) {
					$query->where(function($qu){
						$qu->whereIn('m2.id_prodi',Sia::getProdiUser())
							->orWhereNull('m2.id_prodi');
					});
				}
			}

			return $query;
		}
		
		public function wargaNegara($kode)
		{
			$r = DB::table('kewarganegaraan')->where('kewarganegaraan', $kode)->first();
			if ( !empty($r) ) return $r->nm_wil;
		}

		public function nim($id_mhs)
		{
			$data = Mahasiswareg::where('id_mhs',$id_mhs)->select('nim')->first();
			return empty($data) ? '' : $data->nim;
		}

		public function nmWilayah($id)
		{
			$wilayah = DB::table('wilayah as w')
									->join('wilayah as dw', 'w.id_wil', '=', 'dw.id_induk_wilayah')
									->join('wilayah as dwc', 'dw.id_wil', '=', 'dwc.id_induk_wilayah')
									->select('dwc.id_wil as id_wil', 'w.nm_wil as provinsi','dw.nm_wil as kab','dwc.nm_wil as kecamatan')
									->where('w.id_level_wil',1)
									->where('dwc.id_wil', $id)
									->first();
			if ( !empty($wilayah) ) {
				$data = trim($wilayah->kecamatan) ." - ". trim($wilayah->kab) ." - " . trim($wilayah->provinsi);
				return $data;
			}
		}

		public function kabupaten($id)
		{
			$wilayah = DB::table('wilayah as w')
									->join('wilayah as dw', 'w.id_wil', '=', 'dw.id_induk_wilayah')
									->join('wilayah as dwc', 'dw.id_wil', '=', 'dwc.id_induk_wilayah')
									->select('dwc.id_wil as id_wil', 'w.nm_wil as provinsi','dw.nm_wil as kab','dwc.nm_wil as kecamatan')
									->where('w.id_level_wil',1)
									->where('dwc.id_wil', $id)
									->first();
			if ( !empty($wilayah) ) {
				$data = trim($wilayah->kab);
				return $data;
			}
		}

		public function provinsi($id)
		{
			$wilayah = DB::table('wilayah as w')
									->join('wilayah as dw', 'w.id_wil', '=', 'dw.id_induk_wilayah')
									->join('wilayah as dwc', 'dw.id_wil', '=', 'dwc.id_induk_wilayah')
									->select('dwc.id_wil as id_wil', 'w.nm_wil as provinsi','dw.nm_wil as kab','dwc.nm_wil as kecamatan')
									->where('w.id_level_wil',1)
									->where('dwc.id_wil', $id)
									->first();
			if ( !empty($wilayah) ) {
				$data = trim($wilayah->provinsi);
				return $data;
			}
		}

		public function isTransfer($id_mhs)
		{
			$r = DB::table('mahasiswa_reg')->where('id_mhs',$id_mhs)->where('jenis_daftar',2)->count();
			if ( !empty($r) ) {
				return true;
			} else {
				return false;
			}
		}

		public function prodiEkspor($nm_session = 'mhs_prodi')
		{
			if ( Session::has($nm_session) ) {
				$query = DB::table('prodi')->whereIn('id_prodi',Session::get($nm_session))->get();
				foreach( $query as $r ) {
					$data[] = ' '.$r->jenjang.' - '.$r->nm_prodi;
				}
				$data = implode(',', $data);
			} else {
				$data = 'Semua program studi';
			}
			return $data;
		}

		public function angkatanEkspor($nm_session = 'mhs_angkatan')
		{
			if ( Session::has($nm_session) ) {
				$data = implode(',', Session::get($nm_session));
			} else {
				$data = 'Semua angkatan';
			}
			return $data;
		}

		public function taEkspor($nm_session = 'mhs_ta')
		{
			if ( Session::has($nm_session) ) {
				$data = implode(',', Session::get($nm_session));
			} else {
				$data = 'Semua tahun akademik';
			}
			return $data;
		}

		public function statusEkspor($nm_session = 'mhs_status')
		{
			if ( Session::has($nm_session) ) {
				$query = DB::table('jenis_keluar')->whereIn('id_jns_keluar',Session::get($nm_session))->get();
				foreach( $query as $r ) {
					$data[] = ' '.$r->ket_keluar;
				}
				$data = implode(',', $data);
			} else {
				$data = false;
			}
			return $data;
		}

		public function statusAkmEkspor($nm_session = 'akm_status')
		{
			if ( Session::has($nm_session) ) {
				$query = DB::table('status_mhs')->whereIn('id_stat_mhs',Session::get($nm_session))->get();
				foreach( $query as $r ) {
					$data[] = ' '.$r->nm_stat_mhs;
				}
				$data = implode(',', $data);
			} else {
				$data = false;
			}
			return $data;
		}

		// KRS yang diprogramkan pada semester berjalan
		public function krsMhs($id_mhs_reg,$id_smt = '', $jenis = 1)
		{
			// Jenis 1 = KULIAH, 2 = SP
			$smt = empty($id_smt) ? $this->sessionPeriode() : $id_smt;

			$data = DB::table('nilai as n')
				->leftJoin('jadwal_kuliah as jdk', 'jdk.id', '=', 'n.id_jdk')
				->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
				->leftJoin('matakuliah as mk', 'mk.id', '=', 'mkur.id_mk')
				->select('n.*','jdk.kode_kls','mk.id as id_mk','mk.kode_mk','mk.nm_mk','mk.sks_mk','jdk.id_smt')
				->where('n.id_mhs_reg', $id_mhs_reg)
				->where('jdk.jenis',$jenis)
				->where('jdk.id_smt', $smt);
      // dd($data);
			return $data;
		}

		// public function krsMhsTmp($id_mhs_reg, $id_smt)
		// {
		// 	$data = DB::table('krs_mhs as km')
		// 			->leftJoin('mk_kurikulum as mkur', 'km.id_mkur', 'mkur.id')
		// 			->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
		// 			->select('mkur.id as id_mkur','mk.id as id_mk', 'mk.kode_mk','mk.nm_mk')
		// 			->where('km.id_mhs_reg', $id_mhs_reg)
		// 			->where('km.id_smt', $id_smt)
		// 			->orderBy('mk.nm_mk')
		// 			->get();

		// 	return $data;
		// }

		public function krsMhsTmp($id_mhs_reg, $id_smt)
		{
			$data = DB::table('krs_mhs as km')
					->leftJoin('mk_kurikulum as mkur', 'km.id_mkur', 'mkur.id')
					->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
					->select('mkur.id as id_mkur','mk.id as id_mk', 'mk.kode_mk','mk.nm_mk','mk.sks_mk','mk.jenis_mk')
					->where('km.id_mhs_reg', $id_mhs_reg)
					->where('km.id_smt', $id_smt)
					->orderBy('mk.nm_mk')
					->get();

			return $data;
		}

		public function havingKrs($id_mhs)
		{
			$rule = DB::table('mahasiswa as m1')
						->leftJoin('mahasiswa_reg as m2', 'm1.id', '=', 'm2.id_mhs')
						->leftJoin('krs_status as krs', 'm2.id', '=', 'krs.id_mhs_reg')
						->where('m1.id', $id_mhs)
						->whereNotNull('krs.id')->count();

			return $rule > 0 ? true : false;
		}

		public function ipkKhs($id_mhs_reg, $min_smt, $max_smt)
		{
			$data = DB::table('nilai as nil')
						->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'nil.id_jdk')
						->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
						->whereBetween('jdk.id_smt', [$min_smt,$max_smt])
						->where('nil.id_mhs_reg', $id_mhs_reg)
						->whereNotNull('nil.nilai_indeks')
						->where('nil.nilai_indeks','<>', 0)
						->select(DB::raw('sum('.$this->prefix.'mk.sks_mk * '.$this->prefix.'nil.nilai_indeks) as tot_nilai'),
								DB::raw('sum('.$this->prefix.'mk.sks_mk) as tot_sks'))->first();

			return $data;
		}

		// Sementara tidak dipakai karena tidak mengambil nilai transfer
		public function ipkAktivitas($id_mhs_reg)
		{
			$data = DB::table('nilai as nil')
						->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'nil.id_jdk')
						->leftJoin('mk_kurikulum as mkur', 'mkur.id', 'jdk.id_mkur')
						->leftJoin('matakuliah as mk', 'mkur.id_mk', 'mk.id')
						->where('nil.id_mhs_reg', $id_mhs_reg)
						->whereNotNull('nil.nilai_indeks')
						->where('nil.nilai_indeks','<>', 0)
						->where('jdk.id_smt', '<=', $this->sessionPeriode())
						->select(DB::raw('sum('.$this->prefix.'mk.sks_mk * '.$this->prefix.'nil.nilai_indeks) as tot_nilai'),
								DB::raw('sum('.$this->prefix.'mk.sks_mk) as tot_sks'))->first();

			$ipk = $this->ipk($data->tot_nilai, $data->tot_sks);

			return $ipk;
		}

		public function ipkAktivitas2($id_mhs_reg)
		{
			$data = DB::select("
						SELECT round(sum(nilai_indeks * sks_mk) / sum(sks_mk),2) as ipk FROM (
							SELECT id_mhs_reg, min(nilai_huruf) AS nilai_huruf, max(nilai_indeks) as nilai_indeks, kode_mk, nm_mk, sks_mk,smt
							FROM (

								SELECT id_mhs_reg,nilai_huruf, nilai_indeks, kode_mk, nm_mk, sks_mk,smt

								FROM (
									SELECT nt.id_mhs_reg, nt.nilai_huruf_diakui as nilai_huruf, nt.nilai_indeks, mk.kode_mk, mk.nm_mk, mk.sks_mk, mkur.smt
									FROM nilai_transfer as nt
									left join matakuliah as mk on nt.id_mk = mk.id
									left join mk_kurikulum as mkur on mk.id = mkur.id_mk 
									where nt.id_mhs_reg = '$id_mhs_reg'

									union

									SELECT nil.id_mhs_reg, nil.nilai_huruf, nil.nilai_indeks, mk.kode_mk, mk.nm_mk, mk.sks_mk, nil.semester_mk as smt
									FROM nilai as nil 
									left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
									left join mk_kurikulum as mkur on jdk.id_mkur = mkur.id 
									left join matakuliah as mk on mkur.id_mk = mk.id
									where nil.id_mhs_reg = '$id_mhs_reg'
									and nil.nilai_indeks > 0
									and jdk.id_smt <= ".$this->sessionPeriode()."

								) as result

							) as result2
							where nilai_huruf != ''
							group by kode_mk
							order by smt asc, kode_mk asc, nilai_indeks desc
						) as result3
					");
			$ipk = 0;
			foreach($data as $val)
			{
				$ipk = $val->ipk;
			}
			return $ipk;
		}

		public function ipkLulus($id_mhs_reg)
		{
			$data = DB::select("
						SELECT round(sum(nilai_indeks * sks_mk) / sum(sks_mk),2) as ipk FROM (
							SELECT id_mhs_reg, min(nilai_huruf) AS nilai_huruf, max(nilai_indeks) as nilai_indeks, kode_mk, nm_mk, sks_mk,smt
							FROM (

								SELECT id_mhs_reg,nilai_huruf, nilai_indeks, kode_mk, nm_mk, sks_mk,smt

								FROM (
									SELECT nt.id_mhs_reg, nt.nilai_huruf_diakui as nilai_huruf, nt.nilai_indeks, mk.kode_mk, mk.nm_mk, mk.sks_mk, mkur.smt
									FROM nilai_transfer as nt
									left join matakuliah as mk on nt.id_mk = mk.id
									left join mk_kurikulum as mkur on mk.id = mkur.id_mk 
									where nt.id_mhs_reg = '$id_mhs_reg'

									union

									SELECT nil.id_mhs_reg, nil.nilai_huruf, nil.nilai_indeks, mk.kode_mk, mk.nm_mk, mk.sks_mk, nil.semester_mk as smt
									FROM nilai as nil 
									left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
									left join mk_kurikulum as mkur on jdk.id_mkur = mkur.id 
									left join matakuliah as mk on mkur.id_mk = mk.id
									where nil.id_mhs_reg = '$id_mhs_reg'

								) as result

							) as result2
							where nilai_huruf != ''
							group by kode_mk
							order by smt asc, kode_mk asc, nilai_indeks desc
						) as result3
					");
			$ipk = 0;
			foreach($data as $val)
			{
				$ipk = $val->ipk;
			}
			return $ipk;
		}

		public function transkrip($id_mhs_reg)
		{
			$data = DB::select("
						SELECT id_mhs_reg, min(nilai_huruf) AS nilai_huruf, max(nilai_indeks) as nilai_indeks, id_mk, kode_mk, nm_mk, sks_mk, mk_terganti, smt
						FROM (

							SELECT id_mhs_reg,nilai_huruf, nilai_indeks, id_mk, kode_mk, nm_mk, sks_mk,mk_terganti, smt

							FROM (
								SELECT nt.id_mhs_reg, nt.nilai_huruf_diakui as nilai_huruf, nt.nilai_indeks, mk.id as id_mk, mk.kode_mk, mk.nm_mk, mk.sks_mk, mk.mk_terganti, mkur.smt
								FROM nilai_transfer as nt
								left join matakuliah as mk on nt.id_mk = mk.id
								left join mk_kurikulum as mkur on mk.id = mkur.id_mk 
								where nt.id_mhs_reg = '$id_mhs_reg'

								union

								SELECT nil.id_mhs_reg, nil.nilai_huruf, nil.nilai_indeks, mk.id as id_mk, mk.kode_mk, mk.nm_mk, mk.sks_mk, mk.mk_terganti, mkur.smt
								FROM nilai as nil 
								left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
								left join mk_kurikulum as mkur on jdk.id_mkur = mkur.id 
								left join matakuliah as mk on mk.id = mkur.id_mk
								where nil.id_mhs_reg = '$id_mhs_reg'
								

								union

								SELECT nilmbk.id_mhs_reg, nilmbk.nil_huruf AS nilai_huruf, nilmbk.nil_indeks AS nilai_indeks, mk.id AS id_mk, mk.kode_mk, mk.nm_mk, mk.sks_mk, mk.mk_terganti, mkur.smt
								FROM nilai_mbkm AS nilmbk
								LEFT JOIN matakuliah AS mk ON mk.id = nilmbk.id_mk
								LEFT JOIN mk_kurikulum AS mkur ON mkur.id_mk = mk.id
								LEFT JOIN semester ON semester.id_smt = nilmbk.id_smt
								WHERE nilmbk.id_mhs_reg = '$id_mhs_reg'

							) as result

						) as result2
						where nilai_huruf != ''
						and id_mk IS NOT NULL
						group by kode_mk
						order by smt asc, nilai_indeks desc, kode_mk asc
					");

					// dd(DB::select("
					// SELECT nilmbk.id_mhs_reg, nilmbk.nil_huruf AS nilai_huruf, nilmbk.nil_indeks AS nilai_indeks, mk.id AS id_mk, mk.kode_mk, mk.nm_mk, mk.sks_mk, mk.mk_terganti
					// FROM nilai_mbkm AS nilmbk
					// INNER JOIN matakuliah AS mk ON mk.id = nilmbk.id_mk
					// WHERE nilmbk.id_mhs_reg = '$id_mhs_reg'"));

			return $data;
		}

		public function transkripS2($id_mhs_reg)
		{
			$data = DB::select("
						SELECT id_mhs_reg, min(nilai_huruf) AS nilai_huruf, max(nilai_indeks) as nilai_indeks, id_mk, kode_mk, nm_mk, sks_mk, mk_terganti, smt
						FROM (

							SELECT id_mhs_reg,nilai_huruf, nilai_indeks, id_mk, kode_mk, nm_mk, sks_mk,mk_terganti, smt

							FROM (
								SELECT nt.id_mhs_reg, nt.nilai_huruf_diakui as nilai_huruf, nt.nilai_indeks, mk.id as id_mk, mk.kode_mk, mk.nm_mk, mk.sks_mk, mk.mk_terganti, mkur.smt
								FROM nilai_transfer as nt
								left join matakuliah as mk on nt.id_mk = mk.id
								left join mk_kurikulum as mkur on mk.id = mkur.id_mk 
								where nt.id_mhs_reg = '$id_mhs_reg'

								union

								SELECT nil.id_mhs_reg, nil.nilai_huruf, nil.nilai_indeks, mk.id as id_mk, mk.kode_mk, mk.nm_mk, mk.sks_mk, mk.mk_terganti, mkur.smt
								FROM nilai as nil 
								left join jadwal_kuliah as jdk on jdk.id = nil.id_jdk
								left join mk_kurikulum as mkur on jdk.id_mkur = mkur.id 
								left join matakuliah as mk on mk.id = mkur.id_mk
								where nil.id_mhs_reg = '$id_mhs_reg'

							) as result

						) as result2
						where nilai_huruf != ''
						group by kode_mk
						order by smt asc, kode_mk asc
					");

			return $data;
		}

		/* CEK NILAI ERROR MHS PADA SEMESTER YG DITUNJUK 
		 * @jenis_smt = [1,3,5,7] -> ganjil / [2,4,6,8] -> genap
		 */
		public function cekNilaiError($id_mhs_reg, $jenis_smt)
		{
			$data = DB::table('nilai as nil')
						->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'nil.id_jdk')
						->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
						// ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
						->select('mkur.id_mk')
						->where('nil.id_mhs_reg', $id_mhs_reg)
						->where('nil.nilai_indeks', 0)
						->where('jdk.jenis', 1)
						->whereIn('mkur.smt', $jenis_smt)
						->whereNotIn('mkur.id_mk', 
							DB::table('nilai as nil2')
								->leftJoin('jadwal_kuliah as jdk2','nil2.id_jdk','jdk2.id')
								->leftJoin('mk_kurikulum as mkur2', 'mkur2.id','jdk.id_mkur')
								->where('nil2.id_mhs_reg', $id_mhs_reg)
								->where('nil2.nilai_indeks','<>', 0)
								->where('jdk2.jenis', 2)
								->pluck('mkur2.id_mk'))
						->get();

			return $data;
		}

		/* list jenis semester ganjil = 1, genap = 2 */
		public function listJenisSmt($jenis = 1)
		{
			return $jenis == 1 ? [1,3,5,7] : [2,4,6,8];
		}

	/* end mahasiswa

	/* Matakuliah */
		public function jnsTugasAkhir($key = NULL)
		{
			$data = ['H' => 'Seminar Hasil', 'P' => 'Seminar Proposal', 'S' => 'Skripsi/Tesis'];

			return empty($key) ? $data : $data[$key];
		}

		public function matakuliah()
		{
	    	$query = DB::table('matakuliah as mk')
	    				->leftJoin('prodi as pr', 'mk.id_prodi', '=', 'pr.id_prodi')
	    				->select('mk.*','pr.nm_prodi','pr.jenjang',
	    						DB::raw('(select count(id_mk) from '.$this->prefix().'jadwal_kuliah
	    								where id_mk='.$this->prefix().'mk.id) as terpakai'))
	    				->whereIn('mk.id_prodi',$this->getProdiUser())
	    				->orderBy('mk.created_at','desc');
	    	
	    	$this->matakuliahFilter($query);

	    	return $query;
		}

		public function matakuliahFilter($query)
		{
			if ( Session::has('mk_prodi') ) {
				$query->whereIn('mk.id_prodi', Session::get('mk_prodi'));
			}

			if ( Session::has('mk_jenis') ) {
				$query->whereIn('mk.jenis_mk', Session::get('mk_jenis'));
			}

			if ( Session::has('mk_kelompok') ) {
				$query->whereIn('mk.kelompok_mk', Session::get('mk_kelompok'));
			}

			if ( Session::has('mk_search') ) {
				$query->where(function($q){
					$q->where('mk.kode_mk', 'like', '%'.Session::get('mk_search').'%')
						->orWhere('mk.nm_mk', 'like', '%'.Session::get('mk_search').'%');
				});
			}
		}

		public function jenisMatakuliah($key = '')
		{
			$data = [
				'A' => 'Wajib',
				'B' => 'Pilihan',
				'C' => 'Wajib peminatan',
				'D' => 'Pilihan peminatan',
				'E' => 'Skripsi/Thesis/Disertasi'
			];

			if ( $key === NULL || $key === '' ) {
				return '';
			} elseif( $key == 'array' ) {
				return $data;
			} else {
                if(array_key_exists($key,$data))
				return $data[$key];
                return '';
			}
		}

		public function kelompokMatakuliah($key = '')
		{
			$data = [
					'A' => 'MPK - Pengembangan kepribadian',
					'I' => 'MKP - Matakuliah Pilihan',
					'J' => 'MBKM - Kampus Merdeka',
					'B' => 'MKK - Keilmuan dan keterampilan',
					'C' => 'MKB - Keahlian berkarya',
					'D' => 'MPB - Perilaku berkarya',
					'E' => 'MBB - Berkehidupan bermasyarakat',
					'F' => 'MKU/MKDU - Mata kuliah dasar umum',
					'G' => 'MKDK - Dasar keahlian',
					'H' => 'MKK'
			];

			if ( $key === NULL || $key === '' ) {
				return '';
			} elseif( $key == 'array' ) {
				return $data;
			} else {
                if(array_key_exists($key,$data))
                    return $data[$key];
                return '';
			}
		}
	/* end matakuliah */

	/* Kurikulum */
		public function kurikulum()
		{
			$query = DB::table('kurikulum as kur')
						->leftJoin('prodi as pr', 'kur.id_prodi', '=', 'pr.id_prodi')
						->select('kur.*','pr.jenjang','pr.nm_prodi',
							DB::raw(
								'(select sum(mk.sks_mk) from '.$this->prefix.'mk_kurikulum as mkur
								left join '.$this->prefix.'matakuliah as mk on mkur.id_mk = mk.id
								where mkur.id_kurikulum = '.$this->prefix.'kur.id and mk.jenis_mk in (\'A\',\'C\',\'E\') ) as sks_wajib'
							),
							DB::raw(
								'(select sum(mk.sks_mk) from '.$this->prefix.'mk_kurikulum as mkur
								left join '.$this->prefix.'matakuliah as mk on mkur.id_mk = mk.id
								where mkur.id_kurikulum = '.$this->prefix.'kur.id and mk.jenis_mk in (\'B\',\'D\') ) as sks_pilihan'
							)
						)
						->whereIn('kur.id_prodi', $this->getProdiUser())
						->orderBy('kur.mulai_berlaku','desc');
			$this->kurikulumFilter($query);
			
			return $query;
		}

		public function kurikulumFirst($id_kurikulum)
		{
			$data = DB::table('kurikulum as kur')
                      ->leftJoin('prodi as pr', 'kur.id_prodi','=', 'pr.id_prodi')
                      ->select('kur.*','pr.nm_prodi','pr.jenjang')
                      ->where('kur.id',$id_kurikulum)->first();
      return $data;
		}

		public function statusKurikulum($val)
		{
			return $val == 1 ? '<label class="label label-success">AKTIF</label>':'<label class="label label-danger">NON-AKTIF</label>';
		}

		public function matakuliahKurikulum($id_kurikulum)
		{
			$query = DB::table('mk_kurikulum as mkur')
						->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
						->where('mkur.id_kurikulum', $id_kurikulum)
						->select('mkur.id as id_mkur','mkur.id_kurikulum','mkur.smt','mkur.periode','mk.*')
						->orderBy('mkur.smt','asc')->get();
			return $query;
		}

		public function periode($periode)
		{
			return $periode == 1 ? 'Ganjil':'Genap';
		}

		public function kurikulumFilter($query)
		{
			if ( Session::has('kur_prodi') ) {
				$query->whereIn('kur.id_prodi',Session::get('kur_prodi'));
			}
			// Search
			if ( Session::has('kur_search') ) {
				$query->where(function($q){
					$q->where('kur.nm_kurikulum', 'like', '%'.Session::get('kur_search').'%');
				});
			}
		}
		
	/* kurikulum end */

	/* Jadwal perkuliahan 
		$filter : jika kosong maka abaikan filter, digunakan pada nilai perkuliahan & antara
		$jenis : 1 = jadwal kuliah, 2 = jadwal antara
	*/
		public function jadwalKuliah($filter = '', $jenis = NULL)
		{
			$jenis = empty($jenis) ? 1 : $jenis;

			$query = DB::table('jadwal_kuliah as jdk')
					->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
					->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
					->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
					->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
					->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
					->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
					->select('jdk.*','mk.kode_mk','mk.nm_mk','mk.sks_tm','mk.sks_mk',
							'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.ket','jk.jam_masuk',
							'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl',
						DB::raw('
							(SELECT group_concat(distinct dm.dosen_ke,". ",dos.gelar_depan," ", dos.nm_dosen,", ", dos.gelar_belakang SEPARATOR \'<br>\') from dosen_mengajar as dm
							left join dosen as dos on dm.id_dosen=dos.id
							where dm.id_jdk=jdk.id order by dm.dosen_ke asc) as dosen'),
						DB::raw('(SELECT COUNT(*) as agr from nilai where id_jdk=jdk.id) as terisi'))
					->where('jdk.jenis', $jenis)
					->orderBy('mkur.smt')
					->orderBy('jdk.hari','asc')
					->orderBy('jk.jam_masuk','asc');

			if ( $filter == '' ) {
				$this->jadwalFilter($query);
			}

			return $query;
		}

		public function jadwalFilter($query)
		{
			if ( Session::has('jdk_search') ) {
				$query->where(function($q){
					$q->where('mk.kode_mk', 'like', '%'.Session::get('jdk_search').'%')
						->orWhere('mk.nm_mk', 'like', '%'.Session::get('jdk_search').'%')
						->orWhere('jdk.kode_kls', 'like', '%'.Session::get('jdk_search').'%');
				});
			}

			if ( Session::has('jdk_prodi') ) {
				$query->whereIn('jdk.id_prodi',Session::get('jdk_prodi'));
			} else {
				$query->whereIn('jdk.id_prodi', $this->getProdiUser());
			}

			if ( Session::has('jdk_smt') ) {
				$query->whereIn('jdk.id_smt', Session::get('jdk_smt'));
			}

			if ( Session::has('jdk_ket') ) {
				$query->whereIn('jk.ket', Session::get('jdk_ket'));
			}
		}

		public function jamKuliah($id_prodi, $ket = '')
		{
			$query = DB::table('jam_kuliah')
						->where('id_prodi', $id_prodi);

			if ( !empty($ket) ) {
				$query->where('ket', $ket);
			}

			$data = $query->orderBy('jam_masuk','asc')->get();
			return $data;
		}

		/* Saat krs-an */
		public function validasiBentrokMK($id_mhs_reg, $hari, $jam)
		{
			$jk_bentrok = DB::table('nilai as n')
	    			->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
	    			->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
	    			->leftJoin('matakuliah as mk', 'mkur.id_mk', 'mk.id')
	    			->select('mk.kode_mk','mk.nm_mk')
	    			->where('n.id_mhs_reg', $id_mhs_reg)
	    			->where('jdk.id_smt', $this->sessionPeriode())
	    			->where('jdk.id_jam', $jam)
	    			->where('jdk.hari', $hari)
	    			->where('jdk.hari','<>',0)
	    			->first();

	    	return $jk_bentrok;
	    }

		public function ruangan()
		{
			$query = DB::table('ruangan')->orderBy('nm_ruangan','asc')->get();
			return $query;
		}

		public function sksSemester($id_mhs_reg, $jenis=1)
		{
			$data = DB::table('nilai as n')
						->leftJoin('jadwal_kuliah as jdk', 'jdk.id','=','n.id_jdk')
						->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
						->where('n.id_mhs_reg', $id_mhs_reg)
						->where('jdk.id_smt', $this->sessionPeriode())
						->where('jdk.jenis', $jenis)
						->select(DB::raw('sum('.$this->prefix.'mk.sks_mk) as sks'))->first();
			return $data->sks;
		}

		public function KrsStatus($id_mhs_reg, $jenis = 'KULIAH')
		{
			$data = DB::table('krs_status')
							->where('id_mhs_reg', $id_mhs_reg)
							->where('id_smt', $this->sessionPeriode())
							->where('jenis', $jenis)
							->first();
			return empty($data) ? false : $data;
		}

		public function kolektifPeserta($id_jdk, $jenis  = 'KULIAH')
		{
			$data = DB::table('krs_status as krs')
				->join('mahasiswa_reg as m2', 'krs.id_mhs_reg', '=', 'm2.id')
				->join('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
				->leftJoin('prodi as p', 'p.id_prodi','=','m2.id_prodi')
				->where('krs.id_smt', $this->sessionPeriode())
				->where('krs.jenis', $jenis)
				->select('m2.id','m2.nim','m1.nm_mhs','m2.semester_mulai','p.jenjang','p.nm_prodi',
					DB::raw('(SELECT COUNT(*) AS AG from '.$this->prefix().'nilai as n
							left join '.$this->prefix().'jadwal_kuliah as jdk on n.id_jdk = jdk.id
							where jdk.id=\''.$id_jdk.'\' and n.id_mhs_reg='.$this->prefix().'krs.id_mhs_reg) as available'));
			return $data;
		}

		public function pesertaKelas($id_jdk)
		{
			$data = DB::table('nilai as n')
					->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
					->leftJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
					->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
					->select('n.id as id_nilai','n.nilai_huruf','n.nilai_indeks','n.a_1','n.a_2',
							'n.a_3','n.a_4','n.a_5','n.a_6','n.a_7','n.a_8','n.a_9','n.a_10','n.a_11','n.a_12','n.a_13','n.a_14',
							'n.nil_kehadiran','n.nil_tugas','n.nil_mid','n.nil_final','n.nilai_angka',
							'm2.id as id_mhs_reg','m2.nim','m2.semester_mulai','m1.nm_mhs','m1.jenkel','p.nm_prodi','p.jenjang')
					->where('n.id_jdk', $id_jdk)
					->orderBy('m2.nim')
					->get();
			return $data;
		}

		public function jmlPesertaKelas($id_jdk)
		{
			$data = DB::table('nilai as n')
					->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
					->leftJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
					->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
					->where('n.id_jdk', $id_jdk)
					->count();
			return $data;
		}

		public function mhsInJadwal()
		{
			$data = DB::table('krs_status as krs')
					->rightJoin('mahasiswa_reg as m2', 'krs.id_mhs_reg', '=', 'm2.id')
					->rightJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
					->whereIn('m2.id_prodi', $this->getProdiUser())
					->whereNotNull('krs.id')
					->where('krs.id_smt', Sia::sessionPeriode());
			return $data;
		}

		public function jadwalKuliahMahasiswa($id_mhs_reg, $jenis = 1)
		{
			$data = DB::table('jadwal_kuliah as jdk')
					->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
					->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
					->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
					->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
					->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
					->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
					->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
					->select('jdk.*','n.id as id_krs','mk.kode_mk','mk.nm_mk','mk.sks_mk',
							'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
							'jk.jam_keluar','smt.nm_smt','mkur.smt','smt.nm_smt',
						DB::raw('
							(SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen, ", ",dos.gelar_belakang SEPARATOR \'<br>\') from dosen_mengajar as dm
							left join dosen as dos on dm.id_dosen=dos.id
							where dm.id_jdk=jdk.id) as dosen'),
						DB::raw('(select id_dosen from dosen_mengajar where id_jdk=jdk.id limit 1) as id_dosen'),
						DB::raw('(SELECT COUNT(*) as agr from nilai where id_jdk=jdk.id) as terisi'))
					->where('jdk.jenis', $jenis)
					->where('n.id_mhs_reg', $id_mhs_reg)
					->orderBy('jdk.hari','asc')
					->orderBy('jk.jam_masuk','asc');

			return $data;
		}

	/* End jadwal */

	/* Jadwal Antara */
		public function maxTakeSks($jenis = 'kuliah' )
		{
			if ( $jenis == 'kuliah' ) {
				return 24;
			} elseif ( $jenis == 'sp' ) {
				return 9;
			} else {
				return;
			}
		}

		public function jadwalAntara($type = '')
		{
			$query = DB::table('jadwal_kuliah as jdk')
					->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
					->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
					->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
					->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
					->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
					->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
					->select('jdk.*','mk.kode_mk','mk.nm_mk','mk.sks_mk','pr.id_prodi',
							'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk','smt.nm_smt',
							'jk.jam_keluar','smt.nm_smt','mkur.smt',
						DB::raw('
							(SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen,", ", dos.gelar_belakang SEPARATOR \'<br>\') from '.$this->prefix.'dosen_mengajar as dm
							left join '.$this->prefix.'dosen as dos on dm.id_dosen=dos.id
							where dm.id_jdk='.$this->prefix.'jdk.id) as dosen'),
						DB::raw('(SELECT COUNT(*) as agr from '.$this->prefix.'nilai where id_jdk='.$this->prefix.'jdk.id) as terisi'))
					->where('jdk.jenis', 2)
					->orderBy('jdk.hari','asc')
					->orderBy('jk.jam_masuk','asc');

			$this->jadwalAntaraFilter($query);

			return $query;
		}

		public function jadwalAntaraFilter($query)
		{
			if ( Session::has('jda_search') ) {
				$query->where(function($q){
					$q->where('mk.kode_mk', 'like', '%'.Session::get('jda_search').'%')
						->orWhere('mk.nm_mk', 'like', '%'.Session::get('jda_search').'%')
						->orWhere('jdk.kode_kls', 'like', '%'.Session::get('jda_search').'%');
				});
			}

			if ( Session::has('jda_prodi') ) {
				$query->whereIn('jdk.id_prodi',Session::get('jda_prodi'));
			} else {
				$query->whereIn('jdk.id_prodi', $this->getProdiUser());
			}

			if ( Session::has('jda_smt') ) {
				$query->whereIn('jdk.id_smt', Session::get('jda_smt'));
			}

		}
	/* End jadwal antara */

	/* Jadwal Ujian */
		public function jadwalUjian()
		{
			$query = DB::table('jadwal_ujian as jdu')
						->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
						->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
						->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
						->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
						->leftJoin('ruangan as r', 'jdu.id_ruangan','=','r.id')
						->leftJoin('prodi as pr', 'jdk.id_prodi', 'pr.id_prodi')
						->select('jdu.*','mk.nm_mk','mk.sks_mk','mk.kode_mk','jdk.id_smt',
								'jdk.kode_kls','r.nm_ruangan', 'p.nama as pengawas','pr.nm_prodi',
								'pr.jenjang','mkur.smt')
						->orderBy('jdu.hari','asc')->orderBy('jdu.id_jdk')->orderBy('jdu.jam_masuk','asc');

			return $query;
		}

		public function pesertaUjian($id_jdu)
		{
			$data = DB::table('peserta_ujian as p')
						->leftJoin('mahasiswa_reg as m1', 'p.id_mhs_reg', 'm1.id')
						->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
						->select('m1.id','m1.nim','m2.nm_mhs')
						->where('p.id_jdu', $id_jdu)
						->orderBy('m1.nim')
						->get();

			return $data;
		}

		public function jadwalUjianMhs($id_mhs_reg)
		{
			$query = DB::table('peserta_ujian as pu')
						->leftJoin('jadwal_ujian as jdu', 'pu.id_jdu', 'jdu.id')
						->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
						->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
						->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
						->leftJoin('ruangan as r', 'jdu.id_ruangan','=','r.id')
						->leftJoin('prodi as pr', 'jdk.id_prodi', 'pr.id_prodi')
						->select('jdu.*','mk.nm_mk','mk.sks_mk','jdk.id as id_jdk', 'jdk.id_smt',
								'jdk.kode_kls','r.nm_ruangan', 'pr.nm_prodi',
								'pr.jenjang','mkur.smt')
						->where('jdk.id_smt', $this->sessionPeriode())
						->where('jdu.jenis_ujian', $this->jenisUjian(Sia::sessionPeriode()))
						->where('pu.id_mhs_reg', $id_mhs_reg)
						->orderBy('jdu.id_jdk')
						->orderBy('jdu.hari','asc')
						->orderBy('jdu.jam_masuk','asc');

			return $query;
		}


		public function kartuUjian($ta,$jenis_ujian)
		{
			$data = DB::table('jadwal_ujian as jdu')
						->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'jdu.id_jdk')
						->leftJoin('semester as smt', 'jdk.id_smt', 'smt.id_smt')
	   					->leftJoin('peserta_ujian as pu', 'jdu.id', 'pu.id_jdu')
	   					->leftJoin('mahasiswa_reg as m', 'pu.id_mhs_reg', 'm.id')
	   					->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
	   					->leftJoin('prodi as p', 'p.id_prodi', 'jdk.id_prodi')
	   					->select('pu.id_mhs_reg','m.nim','jdk.id_prodi','p.nm_prodi', 'm2.nm_mhs',
	   							'jdu.jenis_ujian','smt.id_smt','smt.nm_smt')
	   					->where('jdk.id_smt', $ta)
	   					->where('jdu.jenis_ujian', $jenis_ujian);

	   		return $data;
		}

		public function ujianMhs($smt,$jenis_ujian)
		{
			$data = DB::table('jadwal_ujian as jdu')
						->leftJoin('peserta_ujian as pu', 'pu.id_jdu','jdu.id')
						->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
						->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
						->leftJoin('ruangan as r', 'jdu.id_ruangan', 'r.id')
						->select('jdu.*', 'mk.nm_mk', 'r.nm_ruangan')
						->where('jdk.id_smt', $smt)
						->where('jdu.jenis_ujian', $jenis_ujian)
						->orderBy('jdu.tgl_ujian')
						->orderBy('jdu.jam_masuk');

			return $data;
		}

		public function labelUjian()
		{
			$data = DB::table('jadwal_ujian as jdu')
						->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
						->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
						->leftJoin('ruangan as r', 'jdu.id_ruangan', 'r.id')
						->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
						->leftJoin('semester as smt', 'jdk.id_smt', 'smt.id_smt')
						->select('jdu.*', 'mk.nm_mk', 'jdk.kode_kls', 'r.nm_ruangan',
								'p.nama as nm_pengawas', 'smt.nm_smt','jdk.kode_kls',
									DB::raw('(select group_concat(distinct d.gelar_depan," ", d.nm_dosen,", ", d.gelar_belakang SEPARATOR \'<br>\') as dosen from dosen_mengajar as dm
											left join dosen as d on dm.id_dosen = d.id
											where dm.id_jdk=jdk.id) as dosen'));
			return $data;
		}

	/* End jadwal ujian */

	/* Nilai Transfer */
		public function nilaiTransfer()
		{
			$data = DB::table('nilai_transfer as nt')
				->leftJoin('matakuliah as mk', 'nt.id_mk', 'mk.id')
				->select('nt.id','nt.kode_mk_asal','nt.nm_mk_asal','nt.sks_asal','nt.nilai_huruf_asal',
						'nt.nilai_huruf_diakui','nt.nilai_indeks',
						'mk.kode_mk','mk.nm_mk','mk.sks_mk');

			return $data;
		}

	/* End Nilai transfer */

	/* Nilai perkuliahan */
		public function nilaiPerkuliahan($jenis)
		{
			$query = DB::table('jadwal_kuliah as jdk')
					->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
					->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
					->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
					->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
					->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
					->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
					->select('jdk.id','jdk.kode_kls','jdk.id_smt','mk.kode_mk','mk.nm_mk','mk.sks_mk',
							'pr.jenjang','pr.nm_prodi','r.nm_ruangan','smt.nm_smt','mkur.smt',
						DB::raw('
							(SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen,", ", dos.gelar_belakang SEPARATOR \'<br>\') from '.$this->prefix().'dosen_mengajar as dm
							left join '.$this->prefix().'dosen as dos on dm.id_dosen=dos.id
							where dm.id_jdk='.$this->prefix().'jdk.id) as dosen'),
						DB::raw('(SELECT COUNT(*) as agr from '.$this->prefix().'nilai where id_jdk='.$this->prefix().'jdk.id) as terisi'),
    					DB::raw('(SELECT COUNT(*) as agr2 from '.$this->prefix().'nilai where id_jdk='.$this->prefix().'jdk.id and nilai_huruf <> \'\' ) as nilai'))
					->where('jdk.jenis', $jenis)
					->orderBy('mkur.smt','asc')
					->orderBy('mk.nm_mk','asc');

			return $query;
		}

		public function nilaiBelumMasuk()
		{
			$data = DB::table('nilai as n')
					->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
					->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
					->leftJoin('matakuliah as mk', 'mkur.id_mk', 'mk.id')
					->leftJoin('mahasiswa_reg as m1', 'n.id_mhs_reg', 'm1.id')
					->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
					->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
					->select('m1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi','mk.nm_mk',
						DB::raw('
							(SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen,", ", dos.gelar_belakang SEPARATOR \'<br>\') from '.$this->prefix.'dosen_mengajar as dm
							left join '.$this->prefix.'dosen as dos on dm.id_dosen=dos.id
							where dm.id_jdk='.$this->prefix.'jdk.id) as dosen'))
					->where('jdk.id_smt', $this->semesterBerjalan()['id'])
					->whereNull('n.nilai_huruf');
			return $data;
		}
	/* End nilai perkuliahan */

	/* AKM */
		public function akm($filter = true)
		{
			$query = DB::table('aktivitas_kuliah as akm')
							->rightJoin('mahasiswa_reg as m1', 'akm.id_mhs_reg','m1.id')
							->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
							->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
							->leftJoin('semester as smt', 'akm.id_smt','smt.id_smt')
							->leftJoin('status_mhs as sm', 'akm.status_mhs', 'sm.id_stat_mhs')
							->select('akm.*','m1.nim','m1.semester_mulai','m2.nm_mhs',
									'pr.jenjang','pr.nm_prodi','smt.id_smt','smt.nm_smt','sm.nm_stat_mhs');

			if ( $filter ) {
				$this->akmFilter($query);
			}

			return $query;
		}

		public function akmFilter($query)
		{
			// Filter
			if ( Session::has('akm_ta') ) {
				$query->whereIn('akm.id_smt',Session::get('akm_ta'));
			}
			if ( Session::has('akm_angkatan') ) {
				$query->whereRaw('left('.$this->prefix.'m1.nim,4) in ('.implode(",",Session::get('akm_angkatan')).')');
			}
			if ( Session::has('akm_prodi') ) {
				$query->whereIn('m1.id_prodi',Session::get('akm_prodi'));
			}
			if ( Session::has('akm_status') ) {
				$query->whereIn('akm.status_mhs',Session::get('akm_status'));
			}

			if ( Session::has('akm_jns_daftar') ) {
				$query->whereIn('m1.jenis_daftar',Session::get('akm_jns_daftar'));
			}

			// Search
			if ( Session::has('akm_search') ) {
				$query->where(function($q){
					$q->where('m2.nm_mhs', 'like', '%'.Session::get('akm_search').'%')
						->orWhere('m1.nim', 'like', '%'.Session::get('akm_search').'%');
				});
			}

			if ( !Sia::admin() ) {
				$query->whereIn('m1.id_prodi',Sia::getProdiUser());
			}

			return $query;
		}

	/* End akm */

	/* Lulus keluar */

	/* AKM */

		public function lulusKeluarFilter($query)
		{
			// Filter
			if ( Session::has('lk_ta') ) {
				$query->whereIn('m2.semester_keluar',Session::get('lk_ta'));
			}

			if ( Session::has('lk_pin') ) {
				$pin = Session::get('lk_pin');
				$keys = array_keys($pin);

				if ( count($pin) == 1 ) {
					if ( $pin[$keys[0]] == 'pin' ) {
						$query->whereNotNull('m2.pin');
					} else {
						$query->whereNull('m2.pin');
					}
				}
			}

			if ( Session::has('lk_ta_masuk') ) {
				$query->whereIn('m2.semester_mulai',Session::get('lk_ta_masuk'));
			}
			if ( Session::has('lk_angkatan') ) {
				$query->whereRaw('left('.$this->prefix.'m2.nim,4) in ('.implode(",",Session::get('lk_angkatan')).')');
			}
			if ( Session::has('lk_prodi') ) {
				$query->whereIn('m2.id_prodi',Session::get('lk_prodi'));
			}
			if ( Session::has('lk_status') ) {
				$query->whereIn('m2.id_jenis_keluar',Session::get('lk_status'));
			}

			if ( Session::has('lk_jns_daftar') ) {
				$query->whereIn('m2.jenis_daftar',Session::get('lk_jns_daftar'));
			}
			if ( Session::has('lk_jenkel') ) {
				$query->whereIn('m1.jenkel',Session::get('lk_jenkel'));
			}

			// Search
			if ( Session::has('lk_search') ) {
				$query->where(function($q){
					$q->where('m1.nm_mhs', 'like', '%'.Session::get('lk_search').'%')
						->orWhere('m2.nim', 'like', '%'.Session::get('lk_search').'%');
				});
			}

			if ( !Sia::admin() ) {
				$query->whereIn('m2.id_prodi',Sia::getProdiUser());
			}


			return $query;
		}

		public function jenisKeluar()
		{
			$status = DB::table('jenis_keluar')
						->where('id_jns_keluar','<>',0)->get();
			return $status;
		}
	/* end */

	/* Ujian Akhir */

		public function penguji($id_mhs_reg, $jenis, $jabatan, $smt = null)
		{
			$query = DB::table('penguji as p')
	                    ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
	                    ->select('d.id',DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as nm_dosen'),'p.nilai','d.ttd')
	                    ->where('p.id_mhs_reg', $id_mhs_reg)
	                    ->where('p.jabatan', $jabatan)
	                    ->where('p.jenis', $jenis)
	                    ->orderBy('p.id_smt','desc');
	        if ( !empty($smt) ) {
	        	$query->where('p.id_smt', $smt);
	        }

	        $query = $query->first();

	        return !empty($query) ? $query : '';
		}

		public function kriteriaPenilaian($prodi, $key = null)
		{
			$s1 = [
					'Penampilan',
					'Penguasaan Materi Skripsi',
					'Penguasaan Materi Ilmu',
					'Kemampuan beragumentasi'
				];
			$s2 = [
					'Orisinalitas',
					'Kedalaman dan ketajaman',
					'Keterkaitan antara judul, masalah/focus,hipotesis (jika ada), kajian pustaka, pembahasan, simpulan dan saran',
					'Kegunaan dan kemutakhiran',
					'Ketepatan metode, analisis dan hasil penelitian',
					'Penguasaan materi',
					'Kejujuran dan objektivitas'
				];

			if ( empty($key) ) {
				return $prodi == '61101' ? $s2 : $s1;
			} else {
				return $prodi == '61101' ? $s2[$key] : $s1[$key];
			}
		}

	/* Keuangan */
		public function MhsKeuangan($id_smt)
		{

			$query = $this->mahasiswa()
						->select('m2.id as id_mhs_reg','m2.jenis_daftar','m2.nim','m2.id_prodi','p.nm_prodi', 'p.jenjang','jk.ket_keluar','m1.nm_mhs','m2.semester_mulai',
								DB::raw('(select sum(jml_bayar) as byr from pembayaran
										where id_mhs_reg=m2.id
										and id_smt='.$id_smt.' and id_jns_pembayaran=0) as jml_bayar'),
								DB::raw('(select status_mhs from aktivitas_kuliah
                                    where id_smt='.$id_smt.'
                                    and id_mhs_reg='.$this->prefix.'m2.id) as akm'),
								DB::raw('(select count(id_mhs_reg) from krs_status
                                    where id_smt='.$id_smt.'
                                    and id_mhs_reg='.$this->prefix.'m2.id) as sudah_bayar'))
						->where('m2.semester_mulai', '<=', $id_smt);

			return $query;
		}

		public function mhsKeuanganPraktek()
		{
			$query = DB::table('nilai as n')
                    ->leftJoin('mahasiswa_reg as m2', 'n.id_mhs_reg','=','m2.id')
                    ->rightJoin('mahasiswa as m1', 'm2.id_mhs','=','m1.id')
                    ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                    ->leftJoin('mk_kurikulum as mkur', 'mkur.id', 'jdk.id_mkur')
                    ->leftJoin('matakuliah as mk', 'mkur.id_mk', 'mk.id')
                    ->join('prodi as p', 'm2.id_prodi','=','p.id_prodi')
						->select('m2.id as id_mhs_reg','m2.nim','m2.semester_mulai','m1.nm_mhs','p.nm_prodi','p.jenjang',
                            DB::raw('(select sum(jml_bayar) from '.Sia::prefix().'pembayaran
                                    where id_smt='.Sia::prefix().'jdk.id_smt
                                    and id_mhs_reg='.Sia::prefix().'n.id_mhs_reg
                                    and id_jns_pembayaran='.Session::get('pr_jenis_bayar').') as jml_bayar'),
                            DB::raw('(select max(tgl_bayar) from '.Sia::prefix().'pembayaran
                                    where id_smt='.Sia::prefix().'jdk.id_smt
                                    and id_mhs_reg='.Sia::prefix().'n.id_mhs_reg
                                    and id_jns_pembayaran='.Session::get('pr_jenis_bayar').') as tgl_bayar'));
            return $query;
		}

		public function mhsKeuanganSp($smt)
		{

            $query = DB::table('daftar_sp as sp')
    			->leftJoin('mahasiswa_reg as m1', 'm1.id', 'sp.id_mhs_reg')
    			->leftJoin('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
    			->leftJoin('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
    			->leftJoin('semester as smt', 'smt.id_smt', 'sp.id_smt')
    			->select('sp.jml_sks', 'sp.id_mhs_reg', 'm1.nim','m2.nm_mhs',
    				'pr.nm_prodi', 'pr.jenjang', 'smt.id_smt', 'smt.nm_smt',
    				DB::raw('(select sum(jml_bayar) from pembayaran
                                    where id_smt='.$smt.'
                                    and id_mhs_reg=sp.id_mhs_reg
                                    and id_jns_pembayaran=99) as jml_bayar'),
                    DB::raw('(select max(tgl_bayar) from pembayaran
                                    where id_smt='.$smt.'
                                    and id_mhs_reg=sp.id_mhs_reg
                                    and id_jns_pembayaran=99) as tgl_bayar'),
                	DB::raw('(select count(id_mhs_reg) from krs_status
                                    where id_smt='.$smt.'
                                    and jenis=\'SP\'
                                    and id_mhs_reg=sp.id_mhs_reg) as sudah_bayar'));

            return $query;
		}

		public function historyBayar($id_smt = NULL, $id_jns_bayar = NULL)
		{
			$query = DB::table('pembayaran as p')
                ->leftJoin('bank as b', 'p.id_bank', 'b.id')
                ->select('p.*','b.nm_bank');

            if ( !empty($id_smt) ) {
                $query->where('p.id_smt', $id_smt);
            }

            if ( empty($id_jns_bayar) ) {
                $query->where('p.id_jns_pembayaran',0);
            } else {
            	$query->where('p.id_jns_pembayaran', $id_jns_bayar);
            }

            return $query;
		}

		public function listPembayaran()
		{
			$data = DB::table('jenis_pembayaran')
					->where('id_fakultas', $this->getFakultasUser())
					->get();

			return $data;
		}

		public function biayaPerMhs($id_mhs_reg, $smt_mulai, $id_prodi = 61201)
		{

			$smt = $smt_mulai - Session::get('mhs_keu_smt');

			$smt_mulai = substr($smt_mulai,0,4);

			if ( $smt == 0 ) {
				$data = DB::table('biaya_kuliah')->selectRaw('bpp+spp+seragam+lainnya as biaya')
							->where('id_prodi', $id_prodi)
							->where('tahun', $smt_mulai)->first();
			} else {
				$data = DB::table('biaya_kuliah')->selectRaw('bpp as biaya')
							->where('id_prodi', $id_prodi)
							->where('tahun', $smt_mulai)->first();
			}

			return empty($data) ? 0 : $data->biaya;

		}

		public function potonganBiaya()
		{
			$data = DB::table('potongan_biaya_kuliah as pb')
						->leftJoin('mahasiswa_reg as m1', 'm1.id', 'pb.id_mhs_reg')
						->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
						->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
						->select('pb.*','m1.nim','m2.nm_mhs', 'pr.jenjang','pr.nm_prodi');

			return $data;
		}

		public function totalPotonganPerMhs($id_mhs_reg, $id_smt, $smt_filter)
		{
			$smt = $id_smt - $smt_filter;

			if ( $smt == 0 ) {
				// Semester 1
				$data = DB::table('potongan_biaya_kuliah')
						->where('id_mhs_reg', $id_mhs_reg)
						->sum('potongan');
			} else {
				$data = DB::table('potongan_biaya_kuliah')
						->where('id_mhs_reg', $id_mhs_reg)
						->where('jenis_potongan', 'BPP')
						->sum('potongan');
			}

			return $data;
		}

		public function smtTunggakanNum($id_mhs_reg, $smt_mulai, $smt_now)
		{
			$smstr = $this->posisiSemesterMhs($smt_mulai, $smt_now);
			$smstr = $smstr - 1;

			$cuti = DB::table('aktivitas_kuliah')
							->where('id_smt', '<', $smt_now)
							->where('id_smt', '>=', $smt_mulai)
							->where('id_mhs_reg', $id_mhs_reg)
							->whereIn('status_mhs', ['C','D'])
							->count();

			$smstr = $smstr - $cuti;

			return $smstr;
		}

		public function tunggakan($id_mhs_reg, $smt_mulai, $smt_now, $jml_smt = null)
		{
			$tunggakan = 0;

			if ( empty($jml_smt) ) {
				$smstr = $this->smtTunggakanNum($id_mhs_reg, $smt_mulai, $smt_now);
			} else {
				$smstr = $jml_smt;
			}

			$mhs = Mahasiswareg::find($id_mhs_reg);

			/* Potongan */
				$pot_spp = DB::table('potongan_biaya_kuliah')
							->where('id_mhs_reg', $id_mhs_reg)
							->where('jenis_potongan', 'SPP')
							->first();
				$pot_spp = empty($pot_spp) ? 0 : $pot_spp->potongan;

				$pot_bpp = DB::table('potongan_biaya_kuliah')
							->where('id_mhs_reg', $id_mhs_reg)
							->where('jenis_potongan', 'BPP')
							->first();
				$pot_bpp = empty($pot_bpp) ? 0 : $pot_bpp->potongan;
			/* end potongan */

			$angkatan = substr($smt_mulai, 0, 4);

			if ( $smstr > 0 ) {
				$biaya = DB::table('biaya_kuliah')
						->where('tahun', $angkatan)
						->where('id_prodi', $mhs->id_prodi)
						->first();
				if ( !empty($biaya) ) {

					if ( $smt_mulai == $mhs->semester_mulai ) {
						// tagihan semester 1
						$tagihan_1 = $biaya->spp + $biaya->seragam + $biaya->lainnya - $pot_spp;
					} else {
						$tagihan_1 = 0;
					}

					// Tagihan semester 2 dst..
					$tagihan_2 = ( $biaya->bpp - $pot_bpp ) * $smstr;

					$telah_bayar = DB::table('pembayaran')
									->where('id_jns_pembayaran', 0)
									->where('id_mhs_reg', $id_mhs_reg)
									->where('id_smt', '<', $smt_now)
									->where('id_smt', '>=', $smt_mulai)
									->sum('jml_bayar');

					$tunggakan = $tagihan_1 + $tagihan_2 - $telah_bayar;
				}
			}

			return $tunggakan;
		}

		public function jenisPotongan()
		{
			$data = ['BPP','SPP'];
			return $data;
		}

		public function totalBayar($id_mhs_reg,$smt)
		{
			$data = DB::table('pembayaran')
					->where('id_mhs_reg', $id_mhs_reg)
					->where('id_jns_pembayaran', 0)
					->where('id_smt', $smt)->sum('jml_bayar');

			return $data;
		}

	public function jadwalMengajar()
	{
		$query = DB::table('jadwal_kuliah as jdk')
					->leftJoin('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
					->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
					->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
					->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
					->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
					->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
					->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
					->select('jdk.*','mk.nm_mk','mk.sks_mk',
							'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
							'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl','jdk.jenis','dm.id_dosen','dm.dosen_ke',
						DB::raw('(SELECT COUNT(*) as agr from '.$this->prefix.'nilai where id_jdk='.$this->prefix.'jdk.id) as peserta'))
					->orderBy('mkur.smt')
					->orderBy('jdk.hari','asc')
					->orderBy('jk.jam_masuk','asc');

		if ( Session::has('jdm.jenis') ) {
			$query->where('jdk.jenis', Session::get('jdm.jenis'));
		}

		if ( Session::has('jdm.prodi') ) {
			$query->where('jdk.id_prodi', Session::get('jdm.prodi'));
		}

		return $query;
	}

	public function kuesionerGrade($angka)
	{
		if ( $angka >= 4.5 ) {
			$grade = 'Sangat Baik';
		} elseif ( $angka >= 3.5 && $angka < 4.5 ) {
			$grade = 'Baik';
		} elseif ( $angka >= 2.5 && $angka < 3.5 ) {
			$grade = 'Cukup';
		} elseif ( $angka >= 1.5 && $angka < 2.5 ) {
			$grade = 'Kurang';
		} else {
			$grade = 'Tidak Pernah/Tidak baik';
		}

		return $grade;
	}

	public function jenisUjian($id_smt)
	{
		$data =	DB::table('jadwal_ujian as jdu')
			->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
			->where('jdk.id_smt', $id_smt)
			->where('jdu.jenis_ujian', 'UAS')
			->count();

		return $data > 0 ? 'UAS':'UTS';

	}

	public function jenisUjianPasca($id_smt)
	{
		$data =	DB::table('jadwal_ujian as jdu')
			->leftJoin('jadwal_kuliah as jdk', 'jdu.id_jdk', 'jdk.id')
			->where('jdk.id_smt', $id_smt)
			->where('jdk.id_prodi', '61101')
			->where('jdu.jenis_ujian', 'UAS')
			->count();

		return $data > 0 ? 'UAS':'UTS';

	}

	public function cekJadwalKrs($prodi)
	{
		$data = DB::table('prodi as pr')
				->leftJoin('fakultas as f', 'f.id', 'pr.id_fakultas')
				->leftJoin('jadwal_akademik as ja', 'ja.id_fakultas', 'f.id')
				->select('ja.awal_krs','ja.akhir_krs')
				->where('pr.id_prodi', $prodi)
				->first();

		$now = date('Y-m-d');

		if ( $now < $data->awal_krs || $now > $data->akhir_krs ) {
			return false;
		} else {
			return true;
		}
	}

	public function jmlPertemuan($id_jdk)
	{
		$data = DB::table('dosen_mengajar')
				->where('id_jdk', $id_jdk)
				->sum('jml_tm');

		return $data;
	}

	/* LMS */
	public function getResources($indeks, $id_jadwal, $id_dosen = '')
	{
		$id_dosen = empty($id_dosen) ? Sia::sessionDsn() : $id_dosen;

		$materi = DB::table('lms_resources as rs')
            ->leftJoin('lms_materi as m', 'rs.id_resource', 'm.id')
            ->leftJoin('lms_bank_materi as bm', 'bm.id', 'm.id_bank_materi')
            ->where('rs.jenis', 'materi')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->where('m.id_dosen', $id_dosen)
            ->select('rs.*', 'm.id_dosen', 'm.judul', 'm.deskripsi', 'bm.file', DB::raw('\'- \' as jenis2'));

        $catatan = DB::table('lms_resources as rs')
        	->leftJoin('lms_catatan as ct', 'rs.id_resource', 'ct.id')
        	->where('rs.jenis', 'catatan')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->where('ct.id_dosen', $id_dosen)
        	->select('rs.*', 'ct.id_dosen', DB::raw('\'catatan\' as judul'), 'ct.konten as deskripsi', DB::raw('\'catatan.png\' as file'), DB::raw('\'-\' as jenis2'));

        $kuis = DB::table('lms_resources as rs')
        	->leftJoin('lmsk_kuis as k', 'rs.id_resource', 'k.id')
        	->where('rs.jenis', 'kuis')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->where('k.id_dosen', $id_dosen)
        	->select('rs.*', 'k.id_dosen', 'k.judul', 'k.ket as deskripsi', DB::raw('\'kuis.png\' as file'), 'k.jenis as jenis2');

        $video = DB::table('lms_resources as rs')
        	->leftJoin('lms_video as v', 'rs.id_resource', 'v.id')
        	->where('rs.jenis', 'video')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->where('v.id_dosen', $id_dosen)
        	->select('rs.*', 'v.id_dosen', 'v.judul', 'v.ket as deskripsi', DB::raw('\'video.png\' as file'), DB::raw('\'-\' as jenis2'));

        $tugas = DB::table('lms_resources as rs')
            ->leftJoin('lms_tugas as t', 'rs.id_resource', 't.id')
            ->where('rs.jenis', 'tugas')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->where('t.id_dosen', $id_dosen)
            ->select('rs.*', 't.id_dosen', 't.judul', 't.deskripsi', DB::raw('\'tugas.png\' as file'), 't.jenis as jenis2')
            ->union($materi)
            ->union($catatan)
            ->union($kuis)
            ->union($video)
            ->orderBy('urutan')
            ->get();
            
        return $tugas;
    }

	public function getResources2($indeks, $id_jadwal, $id_dosen)
	{

		$materi = DB::table('lms_resources as rs')
            ->leftJoin('lms_materi as m', 'rs.id_resource', 'm.id')
            ->leftJoin('lms_bank_materi as bm', 'bm.id', 'm.id_bank_materi')
            ->where('rs.jenis', 'materi')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->whereIn('m.id_dosen', $id_dosen)
            ->select('rs.*', 'm.id_dosen', 'm.judul', 'm.deskripsi', 'bm.file', DB::raw('\'- \' as jenis2'));

        $catatan = DB::table('lms_resources as rs')
        	->leftJoin('lms_catatan as ct', 'rs.id_resource', 'ct.id')
        	->where('rs.jenis', 'catatan')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->whereIn('ct.id_dosen', $id_dosen)
        	->select('rs.*', 'ct.id_dosen', DB::raw('\'catatan\' as judul'), 'ct.konten as deskripsi', DB::raw('\'catatan.png\' as file'), DB::raw('\'-\' as jenis2'));

        $kuis = DB::table('lms_resources as rs')
        	->leftJoin('lmsk_kuis as k', 'rs.id_resource', 'k.id')
        	->where('rs.jenis', 'kuis')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->whereIn('k.id_dosen', $id_dosen)
        	->select('rs.*', 'k.id_dosen', 'k.judul', 'k.ket as deskripsi', DB::raw('\'kuis.png\' as file'), 'k.jenis as jenis2');

        $video = DB::table('lms_resources as rs')
        	->leftJoin('lms_video as v', 'rs.id_resource', 'v.id')
        	->where('rs.jenis', 'video')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->whereIn('v.id_dosen', $id_dosen)
        	->select('rs.*', 'v.id_dosen', 'v.judul', 'v.ket as deskripsi', DB::raw('\'video.png\' as file'), DB::raw('\'-\' as jenis2'));

        $tugas = DB::table('lms_resources as rs')
            ->leftJoin('lms_tugas as t', 'rs.id_resource', 't.id')
            ->where('rs.jenis', 'tugas')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->whereIn('t.id_dosen', $id_dosen)
            ->select('rs.*', 't.id_dosen', 't.judul', 't.deskripsi', DB::raw('\'tugas.png\' as file'), 't.jenis as jenis2')
            ->union($materi)
            ->union($catatan)
            ->union($kuis)
            ->union($video)
            ->orderBy('urutan')
            ->get();
            
        return $tugas;
    }

    public function listSize($indeks = '')
    {
    	$data = [
    		8192 => '8 MB',
    		4096 => '4 MB',
    		2048 => '2 MB',
    		1024 => '1 MB',
    		512 => '512 KB',
    		256 => '256 KB',
    		128 => '128 KB',
    	];

    	return empty($indeks) ? $data : $data[$indeks];
    }

    public function jumlahPengirimTugas($id_tugas)
    {
    	$data = DB::table('lms_jawaban_tugas')->where('id_tugas', $id_tugas)->count();
    	return $data;
    }

    public function jenisPengiriman($indeks)
    {
    	$data = [
    		'all' => 'Online teks & Upload file',
    		'file' => 'Hanya File',
    		'text' => 'Hanya Online teks'
    	];
    	return $data[$indeks];
    }

    public function jumlahMengerjakanKuis($id_kuis)
    {
    	// $data = DB::table('lmsk_kuis_hasil as kh')
    	// 		->leftJoin('lmsk_kuis_soal as ks', 'kh.id_kuis_soal', 'ks.id')
    	// 		->leftJoin('lmsk_kuis as k', 'k.id', 'ks.id_kuis')
    	// 		->where('k.id', $id_kuis)
    	// 		->groupBy('ks.id_kuis')->count();
    	$data = DB::table('lmsk_telah_kuis')
    			->where('id_kuis', $id_kuis)
    			->where('sisa_waktu',0)->count();
    	return $data;
    }

    public function telahMengerjakanKuis($id_peserta, $id_kuis)
    {
    	$data = DB::table('lmsk_telah_kuis')
    			->where('id_kuis', $id_kuis)
    			->where('id_peserta', $id_peserta)
    			->where('sisa_waktu',0)->count();
    	return $data;
    }

    public function jumlahSoal($id_kuis)
    {
    	$data = DB::table('lmsk_kuis_soal')
    			->where('id_kuis', $id_kuis)
    			->count();
    	return $data;
    }

	public function jadwalKuliahLms($jenis = NULL)
	{
		$jenis = empty($jenis) ? 1 : $jenis;

		$query = DB::table('jadwal_kuliah as jdk')
				->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
				->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
				->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
				->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
				->select('jdk.*','mk.kode_mk','mk.nm_mk','mk.sks_tm','mk.sks_mk',
						'pr.jenjang','pr.nm_prodi','smt.nm_smt','mkur.smt','jdk.tgl',
					DB::raw('
						(SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen,", ", dos.gelar_belakang SEPARATOR \'|\') from '.$this->prefix.'dosen_mengajar as dm
						left join '.$this->prefix.'dosen as dos on dm.id_dosen=dos.id
						where dm.id_jdk='.$this->prefix.'jdk.id) as dosen'),
					DB::raw('
						(SELECT group_concat(distinct id_dosen SEPARATOR \'|\') from '.$this->prefix.'dosen_mengajar as dm
						left join '.$this->prefix.'dosen as dos on dm.id_dosen=dos.id
						where dm.id_jdk='.$this->prefix.'jdk.id) as id_dosen'),
					DB::raw('(SELECT COUNT(*) as agr from '.$this->prefix.'nilai where id_jdk='.$this->prefix.'jdk.id) as terisi'))
				->where('jdk.jenis', $jenis)
				->orderBy('mkur.smt')
				->orderBy('jdk.hari','asc');

		return $query;
	}

	public function kelasPemilihanKonsentrasi($smt)
	{
		$data = konsentrasiPps::where('id_smt', $smt)
					->select('kelas')
					->groupBy('kelas')
					->orderBy('kelas')
					->get();

		return $data;
	}

	public function listKelasKonsentrasi()
	{
		// $data = ['X' => 'O','XI' => 'N'];
		// $data = ['XII' => 'S'];
		// $data = ['XVI' => 'R'];
		$data = ['18' => 'H'];
		return $data;
	}

	public function listKelasKonsentrasi2()
	{
		// $data = ['X' => 'O','XI' => 'N'];
		$data = ['XIV' => 'K', 'XV' => 'E'];
		return $data;
	}

    public function kelasMhs($prodi = null)
    {
    	if ( empty($prodi) ) {

	    	$query = DB::table('mahasiswa_reg')
	    				->select('kode_kelas')
	    				->where('kode_kelas', '<>', '')
	    				->where('id_jenis_keluar', 0);

	    	if ( !$this->admin() ) {
	    		$query->whereIn('id_prodi', $this->getProdiUser());
	    	}

	    	$kelas = $query->groupBy('kode_kelas')->orderBy('kode_kelas')->get();

	    } else {

	    	$kelas = DB::table('mahasiswa_reg')
	    				->select('kode_kelas')
	    				->where('kode_kelas', '<>', '')
	    				->where('id_jenis_keluar', 0)
	    				->where('id_prodi', $prodi)
	    				->groupBy('kode_kelas')
	    				->orderBy('kode_kelas')->get();
	    }

    	return $kelas;
    }

    public function getKrsMhs($kelas, $id_mkur, $id_smt, $count = false)
    {
    	$data = DB::table('krs_mhs as km')
    			->leftJoin('mahasiswa_reg as m', 'km.id_mhs_reg', 'm.id')
    			->select('km.*', 'm.nim')
    			->where('m.kode_kelas', $kelas)
    			->where('km.id_mkur', $id_mkur)
    			->where('km.id_smt', $id_smt);

    	if ( $count ) {
    		$query = $data->count();
    	} else {
    		$query = $data->get();
    	}

    	return $query;
    }

    public function getLastSp()
    {
    	$data = DB::table('jadwal_kuliah')
    			->select('id_smt')
    			->where('jenis', 2)
    			->groupBy('id_smt')
    			->orderBy('id_smt', 'desc')
    			->first();

    	return !empty($data) ? $data->id_smt : '';
    }

    public function inputNilaiSp($id_smt_jdk, $id_prodi)
    {
    	$prodi = DB::table('prodi')->where('id_prodi', $id_prodi)->first();

    	$last_sp = $this->getLastSp();
    	$jda = DB::table('jadwal_akademik')
    			->where('id_fakultas', $prodi->id_fakultas)
    			->first();

    	$stat_sp = !empty($jda) ? $jda->input_nilai_sp : 0;

    	if ( $last_sp == $id_smt_jdk && $stat_sp == 1 ) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function jenisSeminar($key = null)
    {

    	// $jenis = [
    	// 	[
    	// 		'id' => 1,
	    // 		'name' => 'Seminar Proposal',
	    // 		'kode' => 'P'
	    // 	],
	    // 	[
    	// 		'id' => 2,
	    // 		'name' => 'Seminar Hasil',
	    // 		'kode' => 'H'
	    // 	],
	    // 	[
    	// 		'id' => 3,
	    // 		'name' => 'Ujian Skripsi/Tesis',
	    // 		'kode' => 'S'
	    // 	]
	    // ];

	    $jenis = [ 
			'P' => 'Seminar Proposal',
			'H' => 'Seminar Hasil',
			'S' => 'Ujian Skripsi/Tesis'
	    ];

    	return !empty($key) ? $jenis[$key] : $jenis;
    }

    public function jenisMbkm()
    {
    	$data = DB::table('km_jenis_aktivitas')->get();

    	return $data;
    }

    public function kegiatanMbkm()
    {
    	$data = DB::table('km_jenis_pembimbing')->get();

    	return $data;
    }
}
