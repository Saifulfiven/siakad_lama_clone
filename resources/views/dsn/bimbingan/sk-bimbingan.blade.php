<!DOCTYPE html>
<html>
<head>
	<title>Cetak SK Bimbingan</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />

	<style>
    	.print {
        		max-width: 7.5in;
        	}

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
	    		font-weight: bold;
	    	}
        	.print {
        		max-width: 7.5in;
        		/*margin-top: {{ Sia::option('margin_kertas_kop') }}mm;*/
        	}
	        @page {
				margin: 1cm 1.5cm 0.5cm 1.4cm;
			}

			.pagebreak { page-break-before: always; }


		.stempel-box {
			position: relative;
		}

		img.stempel {
			width: 120px;
			position: absolute;
			left: -100px;
			top: -10px;
		}
	</style>
</head>
<body>

<?php

$tgl = Request::get('tgl');

$ta = DB::table('semester')->where('id_smt', Session::get('bim.smt'))->first();

?>

	<div class="print">
		@include('layouts.kop-s1')
        <hr>

		<center>

			<h3><u>SURAT KEPUTUSAN</u></h3>
			Nomor : &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			&nbsp;
			<br>

			<h4 style="margin-top: 10px">TENTANG</h4>

			<p><b>PENETAPAN DOSEN PEMBIMBING DAN MAHASISWA BIMBINGAN<br>
				TUGAS AKHIR/SKRIPSI TAHUN AKADEMIK {{ strtoupper($ta->nm_smt) }}</b></p>
			<p><b>REKTOR {{ strtoupper(config('app.itb_long')) }}</b></p>

			<h4>MENIMBANG</h4>
		</center>

		<ol type="a">
			<li>Bahwa untuk kelancaran proses pelaksanaan Tri Dharma Perguruan Tinggi pada {{ config('app.itb_long') }}, dipandang perlu untuk menetapkan Dosen
				Pembimbing dan mahasiswa bimbingan tugas akhir/skrispi Tahun Akademik {{ $ta->nm_smt }}.
			</li>
			<li>Bahwa Dosen Pembimbing tugas akhir/skripsi yang tercantum namanya dalam Lampiran Keputusan ini, dipandang mampu untuk melaksanakan tugas yang diberikan.
			</li>
			<li>
				Bahwa untuk maksud tersebut di atas, perlu ditetapkan melalui Surat Keputusan.
			</li>
		</ol>
		
		<center><h4>MENGINGAT</h4></center>
		<ol>
			<li>Undang-Undang Nomor 20 Tahun 2003.</li>
			<li>Peraturan Pemerintah Republik Indonesia Nomor 60 Tahun 1999.</li>
			<li>Keputusan menteri Pendidikan Nasional Nomor 222 tahun 1998.</li>
			<li>Keputusan Menteri Pendidikan Nasional Nomor 339 Tahun 1994.</li>
			<li>Statuta {{ config('app.itb_long') }}.</li>
		</ol>

		<center><h4>MEMPERHATIKAN</h4></center>
		<p>Hasil Rapat Pimpinan Institut Teknologi Dan Bisnis Nobel Indonesia pada Tanggal {{ Rmt::tgl_indo($tgl) }}.</p>

		<center><h4>MEMUTUSKAN</h4></center>

		<table border="0">
			<tr><td>Menetapkan</td>
				<td>: </td>
				<td></td>
			</tr>
			<tr>
				<td valign="top" width="100">Pertama</td>
				<td valign="top" width="20">: </td>
				<td> Menetapkan Dosen Pembimbing dan Mahasiswa Bimbingan Tugas Akhir/Skripsi Tahun Akademik {{ $ta->nm_smt }} sebagaimana terlampir dalam surat keputusan ini.</td>
			</tr>

			<tr>
				<td valign="top" width="100">Kedua</td>
				<td valign="top" width="20">: </td>
				<td> Kepada Para Pembimbing akan diberikan honorarium sesuai peraturan yang berlaku pada {{ config('app.itb_long') }}.</td>
			</tr>
			<tr>
				<td valign="top">Ketiga</td>
				<td valign="top">: </td>
				<td> Jika ternyata dikemudian hari terdapat kekeliruan didalam keputusan ini akan diadakan perbaikan sebagaimana mestinya.</td>
			</tr>
			<tr>
				<td valign="top">Keempat</td>
				<td valign="top">: </td>
				<td> Keputusan berlaku sejak tanggal ditetapkan.</td>
			</tr>
		</table>

		<br>

		<table border="0">
			<tr>
				<td width="320"></td>
				<td width="100">Ditetapkan di</td>
				<td width="10"> : </td>
				<td>Makassar</td>
			</tr>
			<tr>
				<td></td>
				<td>Pada Tanggal</td>
				<td> : </td>
				<td><?= Rmt::tgl_indo($tgl) ?></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3"><b>Rektor {{ config('app.itb_long') }}</b></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" class="stempel-box">
					<img class="stempel" src="{{ url('resources') }}/assets/img/stempel-itb2.png">
					<img src="{{ url('resources') }}/assets/img/badar.png" width="200">
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3"><b><u>{{ Sia::option('ketua') }}</u></b></td>
			</tr>

		</table>

		<div style="font-size: 10px">
			<i>Tambusan :</i>
			<ol>
				<li>Ketua Yayasan Pendidikan Nobel Makassar</li>
				<li>Bendahara Yayasan Pendidikan Nobel Makassar</li>
				<li>Para wakil rektor</li>
				<li>Yang bersangkutan untuk diketahui dan dilaksanakan</li>
				<li>Arsip</li>
			</ol>
		</div>

		<div class="pagebreak">
			
			<?php $no = 1 ?>

			<p>Lampiran Surat No :</p>

			<center>
				<h4>DAFTAR DOSEN PEMBIMBING DAN MAHASISWA BIMBINGAN TUGAS AKHIR/SKRIPSI<br>
					TAHUN AKADEMIK {{ strtoupper($ta->nm_smt) }}</h4>
			</center>
			<br>

			<table border="1" style="font-size: 11px;width:7.5in;border:1px solid #000">
				<tr>
					<th>NO</th>
					<th style="min-width: 100px;">MAHASISWA</th>
					<th>PRODI</th>
					<th style="min-width: 140px;">PEMBIMBING</th>
					<th>JUDUL</th>
				</tr>
				@foreach( $bimbingan as $pbb )
					<?php 

						$pembimbing = DB::table('penguji as p')
										->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
										->where('p.id_mhs_reg', $pbb->id_mhs_reg)
										->where('p.jenis', $pbb->jenis)
										->where('p.id_smt', $pbb->id_smt)
										->whereIn('p.jabatan', ['KETUA', 'SEKRETARIS'])
										->select('d.id', 'd.gelar_depan', 'd.gelar_belakang', 'd.nm_dosen')
										->orderBy('p.jabatan')
										->get();

					?>
					<tr>
						<td align="center">{{ $no++ }}</td>
						<td align="center">{{ $pbb->nm_mhs }}<br>{{ $pbb->nim }}</td>
						<td align="center">{{ $pbb->nm_prodi }}</td>
						<?php $urut = 1 ?>
						<?php $id_dosen = '' ?>
						<td>
							@foreach( $pembimbing as $val )
								
								<?php if ( $id_dosen == $val->id ) continue ?>
								
								{{ $urut++ }}. {{ Sia::namaDosen($val->gelar_depan, $val->nm_dosen, $val->gelar_belakang) }}<br>
								<?php $id_dosen = $val->id ?>

							@endforeach
						</td>
						<td>{{ empty($pbb->judul_tmp) ? '-' : $pbb->judul_tmp }}</td>
					</tr>
				@endforeach
			</table>


			<br>
			<br>

			<table border="0" width="100%">
				<tr>
					<td width="350"></td>
					<td colspan="2">Makassar, <?= Rmt::tgl_indo($tgl) ?></td>
				</tr>

				<tr>
					<td></td>
					<td colspan="3"><b>Rektor {{ config('app.itb_long') }}</b></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3" class="stempel-box">
						<img class="stempel" src="{{ url('resources') }}/assets/img/stempel-itb2.png">
						<img src="{{ url('resources') }}/assets/img/badar.png" width="200">
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3"><b><u>{{ Sia::option('ketua') }}</u></b></td>
				</tr>

			</table>


		</div>
	</div>


<script>

	window.print();
	
</script>
</body>
</html>