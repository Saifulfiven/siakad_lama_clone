<!DOCTYPE html>
<html>
<head>
	<title>Cetak SK Mengajar</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />

	<style>
    	.print {
        		max-width: 7.5in;
        	}
        @media print {
        	body {
        		font-family: Arial;
        	}
        	.cl {
    			font-family: 'Calligraphic' !important;
	    	}
	    	h3 {
	    		font-size: 17px;
	    		margin-bottom: 1px;
	    		font-weight: 700;
	    	}
	    	h4 {
	    		font-size: 14px;
	    		font-weight: 600;
	    	}
        	.print {
        		max-width: 7.5in;
        		/*margin-top: {{ Sia::option('margin_kertas_kop') }}mm;*/
        	}
	        @page {
				margin: 1cm 1.5cm 0.5cm 1.4cm;
			}

		    footer {page-break-after: always;}
		}

		.stempel-box {
			position: relative;
		}

		img.stempel {
			width: 120px;
			position: absolute;
			left: -75px;
			top: -10px;
		}
	</style>
</head>
<body>

<?php

$today = Carbon::today()->format('Y-m-d');
$bulan = Carbon::today()->format('m');;
$tahun_sk = Carbon::today()->format('Y');
$jenis = Request::get('jenis') == 1 ? '' : 'Pendek';
?>

@if ( empty(Request::get('all')) )

	<div class="print">
		@include('layouts.kop-s1')
        <!-- <hr> -->

		<!-- <center> -->
		<center style="">

			<h3><u>SURAT KEPUTUSAN</u></h3>
			Nomor: &nbsp; <?= Request::get('nomor') ? Request::get('nomor') : '<span id="nomor">&nbsp; &nbsp; &nbsp; &nbsp;</span>' ?> /SK-PD/<?= Rmt::romawi($bulan) ?>-<?= $tahun_sk ?>
			<br>

			<h4 style="margin-top: 10px">TENTANG</h4>

			<p>Penunjukan/Penugasan Dosen Untuk Mengasuh Mata Kuliah</p>
			<p>Rektor {{ config('app.itb_long') }}</p>

			<h4>MENIMBANG</h4>
		</center>

		<ol type="a">
			<li>Bahwa untuk menjamin kelancaran jalannya perkuliahan serta ujian semester
				pada {{ config('app.itb_long') }}, maka dipandang perlu menetapkan
				Dosen Pengasuh Mata Kuliah</li>
			<li>Bahwa berhubung hal tersebut pada butir (a) di atas, perlu menerbitkan surat keputusannya.</li>
		</ol>
		
		<center><h4>MENGINGAT</h4></center>
		<ol>
			<li>SK. Menteri Pendidikan, Kebudayaan, Riset dan Teknologi No. 313/E/O/2021 , tertanggal 05 Juli 2021, tentang izin {{ config('app.itb_long') }}.</li>
			<li>Peraturan Pemerintah No. 60 tahun 1999.</li>
			<li>Statuta {{ config('app.itb_long') }}.</li>
		</ol>

		<center><h4>MEMUTUSKAN</h4></center>

		<p style="margin:1px">Menetapkan : </p>
		<p>Pertama : Bahwa Saudara/i : <b> <?= Sia::namaDosen($dsn->gelar_depan,$dsn->nm_dosen,$dsn->gelar_belakang) ?></b> ditugaskan sebagai Dosen Pengasuh Mata Kuliah Pada {{ config('app.itb') }} {{ config('app.ni') }}
			untuk Tahun Akademik <b><?= Sia::nmSmt(Request::get('ta'), Request::get('jenis')); ?> <?= $jenis ?> </b>sebagai berikut:
		<table border="1" style="font-size: 11px;width:7.5in;border:1px solid #000">
			<tr>
				<th rowspan="2">NO</th>
				<th rowspan="2">MATA KULIAH</th>
				<th rowspan="2">SKS</th>
				<th colspan="2" style="padding:0px">JADWAL</th>
				<th rowspan="2">KELAS-SMT</th>
				<th rowspan="2">Ruangan</th>
				<th rowspan="2">Progam Studi</th>
			</tr>
			<tr>
				<th style="padding:0px">HARI</th>
				<th style="padding:0px">JAM</th>
			</tr>

			<?php
				$no = 1;
				$jam_masuk='';
				$ruang = '';
				$hari = '';

				$jadwal = DB::table('jadwal_kuliah as jdk')
						->leftJoin('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
						->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
						->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
						->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
						->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
						->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
						->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
						->select('jdk.*','mk.nm_mk','mk.sks_mk',
								'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
								'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl')
						->where('dm.id_dosen', $dsn->id)
						->whereIn('jdk.id_prodi', Sia::getProdiUser())
						->where('jdk.id_smt', Request::get('ta'))
						->where('jdk.jenis', Request::get('jenis'))
						->orderBy('mkur.smt')
						->get();

				foreach( $jadwal as $r ) {
					if ( $jam_masuk == substr($r->jam_masuk,0,5) && $ruang == $r->nm_ruangan && $hari == $r->hari ) {
						?>
						<script>
							$("#kelas-<?= $id ?>").html("<?= $kls ?>/ <?= trim($r->kode_kls) ?>-<?= Rmt::romawi($r->smt) ?>");
							$("#prodi-<?= $id ?>").html("<?= $jenjang ?> <?= $prodi ?> / <?= $r->jenjang.' '.$r->nm_prodi ?>");
						</script>
					<?php
						continue;
					}

					$id = $r->id;
					$jam_masuk = substr($r->jam_masuk,0,5);
					$jam_keluar = substr($r->jam_keluar,0,5);
					$ruang = $r->nm_ruangan;
					$hari = $r->hari;
					$kls = $r->kode_kls."-".Rmt::romawi($r->smt);
					$jenjang = $r->jenjang;
					$prodi =  $r->nm_prodi;

					?>

					<tr>
						<td style="padding:3px;" align="center"><?= $no++ ?></td>
						<td style="padding:3px" ><?= trim(ucwords(strtolower($r->nm_mk))) ?></td>
						<td style="padding:3px;" align="center"><?= $r->sks_mk ?></td>
						
						<td style="padding:3px;" align="center"><?= Rmt::hari($r->hari) ?></td>
						<td style="padding:3px;" align="center"><?= $jam_masuk ?> - <?= $jam_keluar ?></td>
						<td style="padding:3px;" align="center"><span id="kelas-<?= $r->id ?>"><?= $r->kode_kls ?>-<?= Rmt::romawi($r->smt) ?></span></td>
						<td style="padding:3px;" align="center"><?= $ruang ?></td>
						<td style="padding:3px;" align="center"><span id="prodi-<?= $r->id ?>"><?= $jenjang ?> <?= $prodi ?></span></td>
					</tr>

				<?php } ?>
		</table>


		<table border="0">
			<tr>
				<td valign="top" width="100">Kedua</td>
				<td valign="top" width="20">: </td>
				<td> Kepadanya diberikan honorarium sesuai dengan peraturan yang berlaku dalam lingkup Statuta {{ config('app.itb_long') }}.</td>
			</tr>
			<tr>
				<td valign="top">Ketiga</td>
				<td valign="top">: </td>
				<td> Bahwa Segala sesuatunya akan diubah dan diperbaiki sebagaimana mestinya apabila
						dikemudian hari ternyata ada kekeliruan dalam keputusan ini.</td>
			</tr>
			<tr>
				<td valign="top">Keempat</td>
				<td valign="top">: </td>
				<td> Kutipan Surat Keputusan ini disampaikan kepada yang berkepentingan untuk diketahui
						dan dijalankan sebagaimana mestinya, dengan penuh rasa tanggung jawab.</td>
			</tr>
		</table>

		<br>

		<table border="0">
			<tr>
				<td width="430"></td>
				<td width="100">Ditetapkan di</td>
				<td width="10"> : </td>
				<td>Makassar</td>
			</tr>
			<tr>
				<td></td>
				<td>Pada Tanggal</td>
				<td> : </td>
				<td><?= Rmt::tgl_indo($today) ?></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3">{{ config('app.itb_long') }}</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" class="stempel-box">
					<img class="stempel" src="{{ url('resources') }}/assets/img/stempel-itb2.png">
					{{-- <img src="{{ url('resources') }}/assets/img/badar.png" width="200"> --}}
					<img src="{{ url('resources') }}/assets/img/ttd-sylvi.jpg" width="150">
				</td>
			</tr>
			<tr>
				<td></td>
				{{-- <td colspan="3"><b><u>{{ Sia::option('ketua') }}</u></b></td> --}}
				<td colspan="3"><b><u>Dr. Sylvia, S.E., M.Si., Ak., C.A.</u></b></td>
			</tr>
			<tr>
				<td></td>
				{{-- <td colspan="3"><b>NIP : {{ Sia::option('ketua_nip') }}</b></td> --}}
				<td colspan="3"><b>NIP : 197504182006042005</b></td>
			</tr>

		</table>

		<div style="font-size: 10px">
			<i>Tambusan :</i>
			<ol>
				<li>Ketua Yayasan Pendidikan Nobel Makassar</li>
				<li>Bendahara Yayasan Pendidikan Nobel Makassar</li>
				<li>Yang bersangkutan untuk diketahui dan dilaksanakan</li>
				<li>Arsip</li>
			</ol>
		</div>
	</div>
@else
	@foreach( $dosen as $dsn )

		<div class="print">
			@include('layouts.kop-s1')
	        <hr>

			<center>
				<h3><u>SURAT KEPUTUSAN</u></h3>
				Nomor: &nbsp; &nbsp; &nbsp; /SK-PD/<?= Rmt::romawi($bulan) ?>-<?= $tahun_sk ?>
				<br>

				<h4>TENTANG</h4>

				<p>Penunjukan/Penugasan Dosen Untuk Mengasuh Mata Kuliah</p>
				<p>Ketua {{ config('app.itb_long') }}</p>

				<h4>MENIMBANG</h4>
			</center>

			<ol type="a">
				<li>Bahwa untuk menjamin kelancaran jalannya perkuliahan serta ujian semester
					pada {{ config('app.itb_long') }}, maka dipandang perlu menetapkan
					Dosen Pengasuh Mata Kuliah</li>
				<li>Bahwa berhubung hal tersebut pada butir (a) di atas, perlu menerbitkan surat keputusannya.</li>
			</ol>
			
			<center><h4>MENGINGAT</h4></center>
			<ol>
				<li>SK. Menteri Pendidikan, Kebudayaan, Riset dan Teknologi No. 313/E/O/2021 , tertanggal 05 Juli 2021, tentang izin {{ config('app.itb_long') }}.</li>
				<li>Peraturan Pemerintah No. 60 tahun 1999.</li>
				<li>Statuta {{ config('app.itb_long') }}.</li>
			</ol>

			<center><h4>MEMUTUSKAN</h4></center>

			<p style="margin:1px">Menetapkan : </p>
			<p>Pertama : Bahwa Saudara/i : <b> <?= Sia::namaDosen($dsn->gelar_depan,$dsn->nm_dosen,$dsn->gelar_belakang) ?></b> ditugaskan sebagai Dosen Pengasuh Mata Kuliah Pada {{ config('app.itb') }} {{ config('app.ni') }}
				untuk Tahun Akademik <?= Sia::nmSmt(Request::get('ta')); ?> sebagai berikut:
			<table border="1" style="font-size: 11px;width:7.5in;border:1px solid #000">
				<tr>
					<th rowspan="2">NO</th>
					<th rowspan="2">MATA KULIAH</th>
					<th rowspan="2">SKS</th>
					<th colspan="2" style="padding:0px">JADWAL</th>
					<th rowspan="2">KELAS-SMT</th>
					<th rowspan="2">Ruangan</th>
					<th rowspan="2">Progam Studi</th>
				</tr>
				<tr>
					<th style="padding:0px">HARI</th>
					<th style="padding:0px">JAM</th>
				</tr>

				<?php

					$jadwal = DB::table('jadwal_kuliah as jdk')
	                    ->leftJoin('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
	                    ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
	                    ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
	                    ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
	                    ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
	                    ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
	                    ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
	                    ->select('jdk.*','mk.nm_mk','mk.sks_mk',
	                            'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
	                            'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl')
	                    ->where('dm.id_dosen', $dsn->id)
	                    ->whereIn('jdk.id_prodi', Sia::getProdiUser())
	                    ->where('jdk.id_smt', Request::get('ta'))
	                    ->where('jdk.jenis', Request::get('jenis'))
	                    ->orderBy('mkur.smt')
	                    ->get();
					$no = 1;
					$jam_masuk='';
					$ruang = '';
					$hari = '';

					foreach( $jadwal as $r ) {
						if ( $jam_masuk == substr($r->jam_masuk,0,5) && $ruang == $r->nm_ruangan && $hari == $r->hari ) {
							?>
							<script>
								$("#kelas-<?= $id ?>").html("<?= $kls ?>/ <?= trim($r->kode_kls) ?>-<?= Rmt::romawi($r->smt) ?>");
								$("#prodi-<?= $id ?>").html("<?= $jenjang ?> <?= $prodi ?> / <?= $r->jenjang.' '.$r->nm_prodi ?>");
							</script>
						<?php
							continue;
						}

						$id = $r->id;
						$jam_masuk = substr($r->jam_masuk,0,5);
						$jam_keluar = substr($r->jam_keluar,0,5);
						$ruang = $r->nm_ruangan;
						$hari = $r->hari;
						$kls = $r->kode_kls."-".Rmt::romawi($r->smt);
						$jenjang = $r->jenjang;
						$prodi =  $r->nm_prodi;

						?>

						<tr>
							<td style="padding:3px;" align="center"><?= $no++ ?></td>
							<td style="padding:3px" ><?= trim(ucwords(strtolower($r->nm_mk))) ?></td>
							<td style="padding:3px;" align="center"><?= $r->sks_mk ?></td>
							
							<td style="padding:3px;" align="center"><?= Rmt::hari($r->hari) ?></td>
							<td style="padding:3px;" align="center"><?= $jam_masuk ?> - <?= $jam_keluar ?></td>
							<td style="padding:3px;" align="center"><span id="kelas-<?= $r->id ?>"><?= $r->kode_kls ?>-<?= Rmt::romawi($r->smt) ?></span></td>
							<td style="padding:3px;" align="center"><?= $ruang ?></td>
							<td style="padding:3px;" align="center"><span id="prodi-<?= $r->id ?>"><?= $jenjang ?> <?= $prodi ?></span></td>
						</tr>

					<?php } ?>
			</table>

			<table border="0">
				<tr>
					<td valign="top" width="100">Kedua</td>
					<td valign="top" width="20">: </td>
					<td> Kepadanya diberikan honorarium sesuai dengan peraturan yang berlaku dalam lingkup Statuta {{ config('app.itb_long') }}.</td>
				</tr>
				<tr>
					<td valign="top">Ketiga</td>
					<td valign="top">: </td>
					<td> Bahwa Segala sesuatunya akan diubah dan diperbaiki sebagaimana mestinya apabila
							dikemudian hari ternyata ada kekeliruan dalam keputusan ini.</td>
				</tr>
				<tr>
					<td valign="top">Keempat</td>
					<td valign="top">: </td>
					<td> Kutipan Surat Keputusan ini disampaikan kepada yang berkepentingan untuk diketahui
							dan dijalankan sebagaimana mestinya, dengan penuh rasa tanggung jawab.</td>
				</tr>
			</table>

			<br>

			<table border="0">
				<tr>
					<td width="430"></td>
					<td width="100">Ditetapkan di</td>
					<td width="10"> : </td>
					<td>Makassar</td>
				</tr>
				<tr>
					<td></td>
					<td>Pada Tanggal</td>
					<td> : </td>
					<td><?= Rmt::tgl_indo($today) ?></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3">{{ config('app.itb_long') }}</td>
				</tr>
				<tr>
					<td colspan="4"><br><br><br></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3"><b><u>{{ Sia::option('ketua') }}</u></b></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3"><b>NIP : {{ Sia::option('ketua_nip') }}</b></td>
				</tr>

			</table>

			<div style="font-size: 10px">
				<i>Tambusan :</i>
				<ol>
					<li>Ketua Yayasan Pendidikan Nobel Makassar</li>
					<li>Bendahara Yayasan Pendidikan Nobel Makassar</li>
					<li>Yang bersangkutan untuk diketahui dan dilaksanakan</li>
					<li>Arsip</li>
				</ol>
			</div>
		</div>

		<footer>&nbsp;</footer>

	@endforeach

@endif


<script>
	
	var answer = prompt('Masukkan nomor surat');
	document.getElementById('nomor').innerHTML = answer;
	window.print();
	
</script>
</body>
</html>