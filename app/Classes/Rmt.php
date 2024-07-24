<?php

namespace App\Classes;
use DB, Session, Carbon, Storage;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Rmt
{
	/* Siakad */
		public function smtMulaiOnTunggakan()
		{
			return 20181;
		}

		public function listSmtTunggakan($smt_mulai, $jml_smt)
		{
			$smt = "$smt_mulai";
			$listSmt[] = $smt;
			if ( $jml_smt > 1 ) {
				for ( $i = 1; $i < $jml_smt; $i++ ) {
					$end = substr($smt, 4, 1);
					$thn = substr($smt, 0, 4);
					if ( $end == 1 ) {
						$smt = $thn.$end+1;
					} else {
						$smt = $thn+1 . '1';
					}

					$listSmt[] = $smt;
				}
			}

			return $listSmt;
		}

		public function periodeBerjalan($id_prodi)
		{
			$data = DB::table('semester_aktif as sa')
					->leftJoin('fakultas as fk', 'fk.id', 'sa.id_fakultas')
					->leftJoin('prodi as pr', 'pr.id_fakultas', 'fk.id')
					->select('sa.id_smt')
					->where('id_prodi', $id_prodi)
					->first();

			return $data->id_smt;
		}

		public function jenisBayar($prodi = null)
		{
			$data = DB::table('jenis_pembayaran as jp')
					->leftJoin('fakultas as fk', 'fk.id', 'jp.id_fakultas')
					->leftJoin('prodi as pr', 'pr.id_fakultas', 'fk.id')
					->select('jp.*')
					->where('pr.id_prodi', $prodi)
					->get();

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

		public function biayaPerMhs($id_mhs_reg, $smt_mulai, $id_smt, $id_prodi)
		{
			$smt = $smt_mulai - $id_smt;

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

		public function totalBayar($id_mhs_reg,$smt)
		{
			$data = DB::table('pembayaran')->where('id_mhs_reg', $id_mhs_reg)
					->where('id_smt', $smt)->sum('jml_bayar');

			return $data;
		}

		public function posisiSemesterMhs($smt_mulai, $smt_max = NULL) {
	        /**
	         * semester sekarang
	         * @value => 20171 dst
	         */
	        $id_smt_akhir = $smt_max;

	        /**
	         * posisi semester sekarang, ganjil/genap
	         * @value => 1: ganjil atau 2: genap
	         */
	        $jenis_smt_akhir = substr($smt_max,4,1);
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

	/* end siakad */

	public function getSaturdayInRange($dateFromString, $dateToString)
	{
	    $dateFrom = new \DateTime($dateFromString);
	    $dateTo = new \DateTime($dateToString);
	    $dates = [];

	    if ($dateFrom > $dateTo) {
	        return $dates;
	    }

	    if (1 != $dateFrom->format('N')) {
	        $dateFrom->modify('next saturday');
	    }

	    while ($dateFrom <= $dateTo) {
	        $dates[] = $dateFrom->format('Y-m-d');
	        $dateFrom->modify('+1 week');
	    }

	    return $dates;
	}

    public function changeEnv($data = array()){
        if(count($data) > 0){

	            // Read .env-file
	            $env = file_get_contents(base_path() . '/.env');

	            // Split string on every " " and write into array
	            $env = preg_split('/\s+/', $env);;

	            // Loop through given data
	            foreach((array)$data as $key => $value){

	                // Loop through .env-data
	                foreach($env as $env_key => $env_value){

	                    // Turn the value into an array and stop after the first split
	                    // So it's not possible to split e.g. the App-Key by accident
	                    $entry = explode("=", $env_value, 2);

	                    // Check, if new key fits the actual .env-key
	                    if($entry[0] == $key){
	                        // If yes, overwrite it with the new one
	                        $env[$env_key] = $key . "=" . $value;
	                    } else {
	                        // If not, keep the old one
	                        $env[$env_key] = $env_value;
	                    }
	                }
	            }

	            // Turn the array back to an String
	            $env = implode("\n", $env);

	            // And overwrite the .env with the new data
	            file_put_contents(base_path() . '/.env', $env);
	            
	            return true;
        } else {
            return false;
        }
	}

	public function uuid()
	{
		try {
			$uuid = Uuid::uuid4();
			return $uuid->toString();
		} catch(UnsatisfiedDependencyException $e){
			return $e->getMessage();
		}
	}

	public function generateUuid($jumlah)
	{
		for( $i = 1; $i <= $jumlah; $i++ ) {
			echo $this->uuid().'<br>';
		}
	}

	public function prodi($id)
	{
		$data = DB::table('prodi')->where('id_prodi',$id)->first();;
		return $data->jenjang.' '.$data->nm_prodi;
	}

	public function hari($index = 0)
	{
		$index = empty($index) ? 0 : $index;
		$hari = ['', 'SENIN', 'SELASA', 'RABU','KAMIS', 'JUM\'AT', 'SABTU', 'MINGGU'];
		return $hari[$index];
	}

	public function formatTgl($tgl, $format = 'd-m-Y')
	{
		if ( !empty($tgl) ) {
			return Carbon::parse($tgl)->format($format);
		}
	}

	public function tgl_indo($tgl) {
		$tanggal = substr($tgl,8,2);
		$bulan = $this->getBulan(substr($tgl,5,2));
		$tahun = substr($tgl,0,4);
		return $tanggal.' '.$bulan.' '.$tahun;		 
	}

	public function getBulan($bln){
		switch ($bln){
			case 1: 
				return "Januari";
				break;
			case 2:
				return "Februari";
				break;
			case 3:
				return "Maret";
				break;
			case 4:
				return "April";
				break;
			case 5:
				return "Mei";
				break;
			case 6:
				return "Juni";
				break;
			case 7:
				return "Juli";
				break;
			case 8:
				return "Agustus";
				break;
			case 9:
				return "September";
				break;
			case 10:
				return "Oktober";
				break;
			case 11:
				return "November";
				break;
			case 12:
				return "Desember";
				break;
		}
	}

	public function hari_indo($day){
		switch ($day){
			case "Sunday": 
				return "Minggu";
				break;
			case "Monday":
				return "Senin";
				break;
			case "Thuesday":
				return "Selasa";
				break;
			case "Wednesday":
				return "Rabu";
				break;
			case "Thursday":
				return "Kamis";
				break;
			case "Friday":
				return "Jumat";
				break;
			case "Saturday":
				return "Sabtu";
				break;
		}
	}

	public function Romawi($nomor)
	{
	    $data = ['M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1]; 
	    $return = ''; 
	    while( $nomor > 0 ) 
	    { 
	        foreach($data as $rom=>$latin) 
	        { 
	            if($nomor >= $latin) 
	            { 
	                $nomor -= $latin;
	                $return .= $rom;
	                break;
	            }
	        } 
	    } 

	    return $return; 
	}

	public function WaktuLalu($time_ago){
		$waktu_sekarang = strtotime(date('Y-m-d H:i:s'));
		$waktu_lalu 	= $waktu_sekarang - strtotime($time_ago);
		$detik 			= $waktu_lalu ;
		$menit 			= round($waktu_lalu / 60 );
		$jam 			= round($waktu_lalu / 3600);
		$hari 			= round($waktu_lalu / 86400 );
		$minggu 		= round($waktu_lalu / 604800);
		$bulan 			= round($waktu_lalu / 2600640 );
		$tahun 			= round($waktu_lalu / 31207680 );

		// detik
		if($detik <= 60){
			return "Beberapa detik yang lalu";
		}
		//menit
		else if($menit <=60){
			if($menit==1){
				return "1 menit yang lalu";
			}
			else{
				return "$menit menit yang lalu";
			}
		}
		//jam
		else if($jam <=24){
			if($jam==1){
				return "1 jam yang lalu";
			}else{
				return "$jam jam yang lalu";
			}
		}
		//hari
		else if($hari <= 7){
			if($hari==1){
				return "kemarin";
			}else{
				return "$hari hari yang lalu";
			}
		}
		//minggu
		else if($minggu <= 4.3){
			if($minggu==1){
				return "satu minggu yang lalu";
			}else{
				return "$minggu minggu yang lalu";
			}
		}
		//bulan
		else if($bulan <=12){
			if($bulan==1){
				return "satu bulan yang lalu";
			}else{
				return "$bulan bulan yang lalu";
			}
		}
		//tahun
		else{
			if($tahun==1){
				return "satu tahun yang lalu";
			}else{
				return "$tahun tahun yang lalu";
			}
		}
	}

	public function dateBetween($tgl_mulai,$tgl_akhir)
	{
		$now = strtotime(Carbon::now()->format('Y-m-d'));

		$tgl_mulai = strtotime(Carbon::parse($tgl_mulai)->format('Y-m-d'));
		$tgl_akhir = strtotime(Carbon::parse($tgl_akhir)->format('Y-m-d'));

		if ( ($now >= $tgl_mulai) && ($now <= $tgl_akhir) ) {
			return true;
		} else {
			return false;
		}
		// return true;
	}

    public function yearPlus($tgl, $jml_thn = 1)
    {
        return date('Y-m-d',strtotime("$tgl +$jml_thn year"));
    }

	public function yesNo($val)
	{
		return $val == 0 ? 'Tidak' : 'Ya';
	}

	public function AlertError()
	{
		if ( Session::has('error') ) { ?>
			<div class="alert alert-danger"><?= Session::get('error') ?></div>
		<?php }
	}

	public function AlertErrors($errors)
	{
		if ( count($errors) > 0 ) { ?>

		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert">
				<span aria-hidden="true">&times;</span>
				<span class="sr-only">Close</span>
			</button>

			<?php foreach( $errors->all() as $error ) { ?>
		    	<p><?= $error ?></p>
			<?php } ?>

		</div>

		<?php }

	}

	public function AlertSuccess()
	{
		if ( Session::has('success') ) { ?>
			<div class="alert bg-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<?= Session::get('success') ?>
			</div>
		<?php }
	}

	public function Success($pesan)
	{
		Session::flash('success', $pesan);
	}

	public function Error($pesan)
	{
		Session::flash('error', $pesan);
	}

	public function rupiah($angka){

	  $rupiah=number_format($angka,0,',','.');

	  return $rupiah;

	}

	public function link($aksi,$text,$class='btn btn-primary btn-sm')
	{
		$data = '<a href="'.$aksi.'" class="'.$class.'" title="'.$text.'">
				'.$text.'</a>';
		return $data;
	}

	function buatFile($isi,$file=null)
	{
		if ( empty($file) ) {
			$file = storage_path('/logs/Gagal Login.txt');
		}

		$fp=fopen($file,'w');
		if(!$fp)return 0;
		fwrite($fp, $isi);
		fclose($fp);return 1;
	}

	function logGagalLogin($event)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
        $komputer = php_uname();
        $date_time = Carbon::now()->format('d/m/Y H:m');
        $cred = 'username: '.$event->credentials['username'].', password: '.$event->credentials['password'];
        $konten = '['.$date_time.'] IP: '.$ip.', Komputer: '.$komputer.'. '.$cred;
        Storage::disk('logs')->append('GagalLogin.txt', $konten);
	}

	function logSuksesLogin($event)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
        $komputer = php_uname();
        $date_time = Carbon::now()->format('d/m/Y H:m');
        $akun = 'ID: '.$event->user['id'].', Nama: '.$event->user['nama'];
        $konten = '['.$date_time.'] IP: '.$ip.', Komputer: '.$komputer.'. '.$akun;
        Storage::disk('logs')->append('SuksesLogin.txt', $konten);

        \LogAktivitas::add('Login');
	}

	public function format_tgl($tanggal, $format='d-m-Y')
	{
		return \Carbon::parse($tanggal)->format($format);
	}
	
	public function kategoriKalender()
	{
		// return ['1' => 'Penerimaan Maba', '2' => 'Semester Ganjil', '3' => 'Semester Genap'];
		return ['1' => 'S2', '2' => 'S1'];
	}

    public function auth($api_token, $token='') // For API request
    {
    	if ( empty($token) ) {
    		$data = ['error' => 1, 'msg' => 'Token kosong'];
    		echo json_encode($data);
    		exit;
    	}

    	if ( $token != $api_token ) {
    		$data = ['error' => 1, 'msg' => 'Token yang anda masukkan salah'];
    		echo json_encode($data);
    		exit;
    	}

    }

    public function impor($file,$table)
    {
		$data = DB::statement("
			LOAD DATA LOCAL INFILE '".$file."'
			INTO TABLE ".$table."
			FIELDS TERMINATED BY ';'
			ESCAPED BY ''
			LINES TERMINATED BY 'n'
			IGNORE 1 LINES
	     ");

		// dd($data);
    }

    public function kategoriInformasi()
    {
    	$data = ['mahasiswa', 'dosen'];

    	return $data;
    }

    public function get_web_page( $url )
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,     
            CURLOPT_ENCODING       => "",
            CURLOPT_USERAGENT      => "spider",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        // $err     = curl_errno( $ch );
        // $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['content'] = $content;
        return $header;
    }

	public function konversi($x){
  
	  $x = abs($x);
	  $angka = array ("","satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	  $temp = "";
	  
	  if($x < 12){
	   $temp = " ".$angka[$x];
	  }else if($x<20){
	   $temp = konversi($x - 10)." belas";
	  }else if ($x<100){
	   $temp = konversi($x/10)." puluh". konversi($x%10);
	  }else if($x<200){
	   $temp = " seratus".konversi($x-100);
	  }else if($x<1000){
	   $temp = konversi($x/100)." ratus".konversi($x%100);   
	  }else if($x<2000){
	   $temp = " seribu".konversi($x-1000);
	  }else if($x<1000000){
	   $temp = konversi($x/1000)." ribu".konversi($x%1000);   
	  }else if($x<1000000000){
	   $temp = konversi($x/1000000)." juta".konversi($x%1000000);
	  }else if($x<1000000000000){
	   $temp = konversi($x/1000000000)." milyar".konversi($x%1000000000);
	  }
	  
	  return $temp;
 	}
  
	public function tkoma($x){
		$str = stristr($x,".");
		$ex = explode('.',$x);
		if ( count($ex) == 1 ) {
			return false;
		}
		$a = 0;
		
		if(($ex[1]/10) >= 1){
		$a = abs($ex[1]);
		}
		$string = array("nol", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan",   "sembilan","sepuluh", "sebelas");
		$temp = "";

		$a2 = $ex[1]/10;
		$pjg = strlen($str);
		$i =1;


		if($a>=1 && $a< 12){   
		$temp .= " ".$string[$a];
		}else if($a>12 && $a < 20){   
		$temp .= $this->konversi($a - 10)." belas";
		}else if ($a>20 && $a<100){   
		$temp .= $this->konversi($a / 10)." puluh". $this->konversi($a % 10);
		}else{
		if($a2<1){

		while ($i<$pjg){     
		 $char = substr($str,$i,1);     
		 $i++;
		 $temp .= " ".$string[$char];
		}
		}
		}  
		return $temp;
 	}
 
	public function terbilang($x){
	  if($x<0){
	   $hasil = "minus ".trim($this->konversi($x));
	  }else{
	   $poin = trim($this->tkoma($x));
	   $hasil = trim($this->konversi($x));
	  }
  
	if($poin){
	   $hasil = $hasil." koma ".$poin;
	  }else{
	   $hasil = $hasil;
	  }
	  return $hasil;  
	}

	public function random($digits)
	{
		return rand(pow(10, $digits-1), pow(10, $digits)-1);
	}

	public function removeExtensi($file)
	{
		$withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
		return $withoutExt;
	}

	public function get_file_extension($file_name) {
		return substr(strrchr($file_name,'.'),1);
	}

	public function icon($file) {
		$ekstensi = $this->get_file_extension($file);

		switch ($ekstensi) {
			case 'docx':
			case 'doc':
				$gambar = 'doc.svg';
			break;
			case 'xls':
			case 'xlsx':
			case 'csv':
				$gambar = 'excel.svg';
			break;
			case 'pdf':
				$gambar = 'pdf.svg';
			break;
			case 'jpg':
			case 'jpeg':
				$gambar = 'jpg.svg';
			break;
			case 'png':
				$gambar = 'png.svg';
			break;
			case 'zip':
			case 'rar':
			case 'iso':
				$gambar = 'zip.svg';
			break;
			case 'ppt':
			case 'pptx':
				$gambar = 'ppt.svg';
			break;
			case 'txt':
				$gambar = 'txt.svg';
			break;
			
			default:
				$gambar = 'file.png';
			break;
		}

		return $gambar;
	}

	public function badge($nilai)
	{
		if ( $nilai == 100 ) {
			$badge = 'bg-primary';
		} elseif ( $nilai > 80 ) {
			$badge = 'bg-success';
		} elseif ( $nilai > 60 ) {
			$badge = 'bg-theme-inverse';
		} elseif ( $nilai > 50 ) {
			$badge = 'bg-warning';
		} else {
			$badge = 'bg-danger';
		}

		return $badge;
	}

	public function jmlPertemuan($id_jdk)
	{
		$data = DB::table('dosen_mengajar')
				->where('id_jdk', $id_jdk)
				->sum('jml_tm');

		return $data;
	}

	public function getResources($indeks, $id_jadwal, $id_dosen)
	{

		$materi = DB::table('lms_resources as rs')
            ->leftJoin('lms_materi as m', 'rs.id_resource', 'm.id')
            ->leftJoin('lms_bank_materi as bm', 'bm.id', 'm.id_bank_materi')
            ->where('rs.jenis', 'materi')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->where('m.id_dosen', $id_dosen)
            ->select('rs.*', 'm.judul', 'm.deskripsi', 'bm.file', DB::raw('\'- \' as jenis2'));

        $catatan = DB::table('lms_resources as rs')
        	->leftJoin('lms_catatan as ct', 'rs.id_resource', 'ct.id')
        	->where('rs.jenis', 'catatan')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->where('ct.id_dosen', $id_dosen)
        	->select('rs.*', DB::raw('\'catatan\' as judul'), 'ct.konten as deskripsi', DB::raw('\'catatan.png\' as file'), DB::raw('\'-\' as jenis2'));

        $kuis = DB::table('lms_resources as rs')
        	->leftJoin('lmsk_kuis as k', 'rs.id_resource', 'k.id')
        	->where('rs.jenis', 'kuis')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->where('k.id_dosen', $id_dosen)
        	->select('rs.*', 'k.judul', 'k.ket as deskripsi', DB::raw('\'kuis.png\' as file'), 'k.jenis as jenis2');

        $video = DB::table('lms_resources as rs')
        	->leftJoin('lms_video as v', 'rs.id_resource', 'v.id')
        	->where('rs.jenis', 'video')
        	->where('rs.pertemuan_ke', $indeks)
        	->where('rs.id_jadwal', $id_jadwal)
        	->where('v.id_dosen', $id_dosen)
        	->select('rs.*', 'v.judul', 'v.ket as deskripsi', DB::raw('\'video.png\' as file'), DB::raw('\'-\' as jenis2'));

        $tugas = DB::table('lms_resources as rs')
            ->leftJoin('lms_tugas as t', 'rs.id_resource', 't.id')
            ->where('rs.jenis', 'tugas')
            ->where('rs.pertemuan_ke', $indeks)
            ->where('rs.id_jadwal', $id_jadwal)
            ->where('t.id_dosen', $id_dosen)
            ->select('rs.*', 't.judul', 't.deskripsi', DB::raw('\'tugas.png\' as file'), 't.jenis as jenis2')
            ->union($materi)
            ->union($catatan)
            ->union($kuis)
            ->union($video)
            ->orderBy('urutan')
            ->get();

        return $tugas;
    }

   	public function semesterBerjalan()
   	{
		$periode = DB::table('semester_aktif as sa')
                ->leftJoin('semester as smt','sa.id_smt','=','smt.id_smt')
                ->select('smt.id_smt','smt.nm_smt','smt.smt')
                ->orderBy('sa.id_smt', 'desc')
                ->first();

        return $periode;
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

    public function jenisPengiriman($indeks)
    {
    	$data = [
    		'all' => 'Online teks & Upload file',
    		'file' => 'Hanya File',
    		'text' => 'Hanya Online teks'
    	];
    	return $data[$indeks];
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

    public function jumlahPengirimTugas($id_tugas)
    {
    	$data = DB::table('lms_jawaban_tugas')->where('id_tugas', $id_tugas)->count();
    	return $data;
    }

    public function pukul($index = 'x')
    {
    	$pukul = ['08.00','08.30','09.00','09.30','10.00','10.30','11.00','11.30','12.00','12.30','13.00','13.30','14.00','14.30','15.00','15.30','16.00','16.30','17.00','17.30','18.00','18.30','19.00','19.30','20.00','20.30','21.00'];
    	if ( $index != 'x' && $index > count($pukul) - 1 ) {
    		return '21.00';
    	}

    	return $index == 'x' ? $pukul : $pukul[$index];
    }

    public function engkripAngka($angka)
    {
    	$data = (($angka * 999999) / 5 ) * 150;

    	return $data;
    }

    public function dekripAngka($angka)
    {
    	$data = (($angka / 150 ) * 5) / 999999;

    	return $data;
    }

    public function sisaMenit($mulai, $akhir)
    {
    	$mulai = Carbon::parse($mulai)->format('Y-m-d H:i:s');
    	$akhir = Carbon::parse($akhir)->format('Y-m-d H:i:s');

    	$to_time 	= strtotime($mulai);
		$from_time 	= strtotime($akhir);
		$remaining = round(abs($to_time - $from_time) / 60);
		return $remaining;
    }

    public function katKegiatanDosen($index = null)
    {
    	$data = [
			1 => 'Pengabdian Masyarakat',
			2 => 'Kegiatan Penunjang',
			3 => 'Pendidikan dan pengajaran',
			4 => 'Kegiatan Penelitian'
    	];

    	return empty($index) ? $data : $data[$index];
    }

    // Jenis = P,H,S
    public function bimbinganMhs($id_smt, $id_mhs_reg, $jenis)
    {
    	$data = DB::table('penguji as p')
    			->leftJoin('ujian_akhir as ua', function($join){
    				$join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
    				$join->on('ua.id_smt', '=', 'p.id_smt');
    				$join->on('ua.jenis', '=', 'p.jenis');
    			})
    			->join('dosen as d', 'd.id', 'p.id_dosen')
    			->select('ua.*','p.id_dosen','p.jabatan','d.nm_dosen','d.gelar_depan','d.gelar_belakang')
    			->where('p.jenis', $jenis)
    			->where('p.id_smt', $id_smt)
    			->where('p.id_mhs_reg', $id_mhs_reg)
    			->whereIn('p.jabatan', ['KETUA','SEKRETARIS'])
    			->take(2)
    			->get();

    	return $data;
    }

    public function jnsSeminar($key)
    {
    	$data = ['P' => 'Proposal Penelitian', 'H' => 'Hasil Penelitian', 'S' => 'Skripsi/Tesis'];

    	return $data[$key] ? $data[$key] : '-';
    }

    public function bimbinganDosen($id_smt, $id_dosen)
    {
    	// $data = DB::table('penguji as p')
    	// 		->join('ujian_akhir as ua', function($join){
    	// 			$join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
    	// 			$join->on('ua.id_smt', '=', 'p.id_smt');
    	// 			$join->on('ua.jenis', '=', 'p.jenis');
    	// 		})
    	// 		->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
    	// 		->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
    	// 		->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
    	// 		->select('p.jenis','m1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi')
    	// 		->where('p.id_dosen', $id_dosen)
    	// 		->where('p.id_smt', $id_smt)
    	// 		->where('p.jenis', $jenis)
    	// 		->whereIn('p.jabatan', ['KETUA','SEKRETARIS']);

    	$data = DB::table('penguji as p')
    			->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
    			->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
    			->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
    			->select('p.id_mhs_reg','p.id_smt','p.jenis','m1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi')
    			->where('p.id_dosen', $id_dosen)
    			->where('p.id_smt', $id_smt)
    			->whereIn('p.jabatan', ['KETUA','SEKRETARIS'])
    			->groupBy('p.id_mhs_reg');

    	return $data;
    }

    public function pengujianDosen($id_smt, $id_dosen)
    {
      $data = DB::table('penguji as p')
          ->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
          ->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
          ->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
          ->select('p.id_mhs_reg', 'p.id_smt', 'p.jenis', 'm1.nim', 'm2.nm_mhs', 'pr.jenjang', 'pr.nm_prodi')
          ->where('p.id_dosen', $id_dosen)
          ->where('p.id_smt', $id_smt)
          ->whereIn('p.jabatan', ['ANGGOTA', 'ANGGOTA2'])
          ->groupBy('p.id_mhs_reg');

      return $data;

    }

    public function seminarDosen($id_smt, $id_dosen)
    {

    	$data = DB::table('penguji as p')
    			->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
    			->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
    			->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
    			->select('p.id_mhs_reg','p.id_smt','p.jenis','m1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi')
    			->where('p.id_dosen', $id_dosen)
    			->where('p.id_smt', $id_smt)
    			->groupBy('p.id_mhs_reg');

    	return $data;
    }

    public function namaTa($id_smt)
    {
    	$data = DB::table('semester')->where('id_smt', $id_smt)->first();
    	
    	return !empty($data) ? $data->nm_smt : '-';
    }

    public function linkTtd($file)
    {
    	return url('storage').'/ttd-dosen/'.$file;
    }

    public function unlink($file)
    {
    	if ( file_exists($file) ) {
    		unlink($file);
    	}
    }

    public function makeIntro($id_jadwal, $id_dosen, $mk, $jenis = 'catatan')
    {
    	try {
	    	// Cek apakah telah ada catatan di intro
	    	// $cek = DB::table('lms_resources')
	    	// 		->where('id_jadwal', $id_jadwal)
	    	// 		->where('jenis', $jenis)
	    	// 		->where('pertemuan_ke', 0)
	    	// 		->count();

	    	// if ( $cek == 0 ) {

	    		$dsn = DB::table('dosen as d')
	    					->leftJoin('users as u', 'd.id_user', 'u.id')
	    					->select('d.*', 'u.email')
	    					->where('d.id', $id_dosen)
	    					->first();

	    		if ( empty($dsn->foto) ) {
	    			$foto = url('resources/assets/img/avatar.png');
	    		} else {
	    			$foto = config('app.url-foto-dosen').'/'.$dsn->foto;
	    		}

	    		$konten = '
					<p style="text-align:center"><strong>'.$this->namaDosen($dsn->gelar_depan, $dsn->nm_dosen, $dsn->gelar_belakang).'</strong></p>
					<p style="text-align:center;margin-bottom: 20px"><strong>'.$mk.'</strong></p><br>
					<p style="text-align:center;padding-bottom: 20px">
						<img style="max-width:150px" alt="" src="'.$foto.'" /></p>
					<p style="text-align:center">No HP : '.$dsn->hp.'</p>
					<p style="text-align:center">Email : '.$dsn->email.'</p>';
					echo $konten;
                // $data = new \App\Catatan;
                // $data->konten = $konten;
                // $data->id_dosen = $id_dosen;
                // $data->id_jadwal = $id_jadwal;
                // $data->save();
                // $id = $data->id;

                // $res = new \App\Resources;
                // $res->id_jadwal = $id_jadwal;
                // $res->id_resource = $id;
                // $res->jenis = $jenis;
                // $res->pertemuan_ke = 0;
                // $res->urutan = 1;
                // $res->save();
    		// }

        } catch(\Exception $e) {}
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

	public function nmJenjang($jenjang)
	{
		$data = [
			'S1' => 'Strata Satu (S1)',
			'S2' => 'Strata Dua (S2)',
			'S3' => 'Strata Tiga (S3)',
		];

		return $data[$jenjang];
	}

    public function checkPersetujuanSeminar($id_seminar, $id_validasi)
    {
        // Cek apakah telah disetujui oleh bauk
        $bauk = DB::table('seminar_pendaftaran')
        		->where('id', $id_seminar)
                ->where('validasi_bauk', '1')
                ->count();

        // Jika belum disetujui bauk
        if ( $bauk == 0 ) {
            return false;
        }

        // Jika program mengeksekusi kode ini ke bawah artinya bauk telah menyetujui

        // Cek persetujuan dosen pembimbing yang lain
        // Apakah masih ada yang belum setuju
        $pbb = DB::table('seminar_validasi')
                ->where('id_seminar', $id_seminar)
                ->where('id','<>', $id_validasi)
                ->where('disetujui', '0')
                ->count();

        if ( $pbb > 0 ) {
            return false;
        }

        return true;

    }

    public function jenisSeminar($key = null)
    {

    	$jenis = [
    		[
    			'id' => 1,
	    		'name' => 'Seminar Proposal',
	    		'kode' => 'P'
	    	],
	    	[
    			'id' => 2,
	    		'name' => 'Seminar Hasil',
	    		'kode' => 'H'
	    	],
	    	[
    			'id' => 3,
	    		'name' => 'Ujian Skripsi/Tesis',
	    		'kode' => 'S'
	    	]
	    ];

    	return !empty($key) ? $jenis[$key-1] : $jenis;
    }

    public function seminarNumerik($kode) {
    	$data = [
    		'P' => 1,
    		'H' => 2,
    		'S' => 3
    	];

    	return $data[$kode];
    }

    public function status($val)
    {
    	if ( $val == 0 ) {
    		echo '<i class="fa fa-refresh"> Belum divalidasi';
    	} elseif( $val == 1 ) {
    		echo '<i class="fa fa-check" style="color: green"> Valid';
    	} else {
    		echo '<i class="fa fa-ban" style="color: red"> Ditolak';
    	}
    }

    public function status2($val)
    {
    	if ( $val == 0 ) {
    		echo '<i class="fa fa-refresh"> Belum direspon';
    	} elseif( $val == 1 ) {
    		echo '<i class="fa fa-check" style="color: green"> Disetujui';
    	} else {
    		echo '<i class="fa fa-ban" style="color: red"> Ditolak';
    	}
    }

    public function status3($val)
    {
    	if ( $val == 0 ) {
    		echo '<i class="fa fa-ban" style="color: red"> Belum disetujui';
    	} else {
    		echo '<i class="fa fa-check" style="color: green"> Disetujui';
    	}
    }

    public function nmProdi($id_prodi)
    {
    	$prodi = DB::table('prodi')->where('id_prodi', $id_prodi)->first();

    	if ( !empty($prodi) ) {
    		return $prodi->jenjang .' '.$prodi->nm_prodi;
    	} else {
    		return 'Tak diketahui';
    	}
    }

    public function smtPlus1($semester)
    {
    	$periode = substr($semester, 4, 1);
    	$tahun = substr($semester, 0, 4);

    	if ( $periode == 1 ) {
    		$id_smt = $tahun.'2';
    	} else {
    		$tahun_1 = $tahun + 1;
    		$id_smt = $tahun_1.'1';
    	}

    	return $id_smt;
    	
    }

    public function smtMinus($semester, $minus = 1)
    {
    	$smt = DB::table('semester')
    			->where('id_smt', '<', $semester)
    			->orderBy('id_smt', 'desc')
    			->take($minus)
    			->pluck('id_smt');

    	return $smt;
    }

    public function anggotaMbkm($jenis)
    {
    	$data = [
    		0 => 'Personal',
    		1 => 'Kelompok'
    	];

    	if ( array_key_exists($jenis, $data) ) {
    		return $data[$jenis];
    	}
    }

    public function peranAnggota($index)
    {
    	$data = [
    		1 => 'Ketua',
    		2 => 'Anggota',
    		3 => 'Personal'
    	];

    	if ( array_key_exists($index, $data) ) {
    		return $data[$index];
    	}
    }
}