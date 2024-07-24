<!DOCTYPE html>
<html>
<head>
	<title>Cetak Absensi Dosen</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>
		.hidden{
	    display:none;
		}
		@media print  
        {
            tr{
                page-break-inside: avoid;
            }
            .footer{
                page-break-inside: avoid;
            }
        }
	</style>
</head>
<body onload="window.print()">

	<center>
	<!-- <h4>BERITA ACARA PERKULIAHAN TAHUN {{ Sia::sessionPeriode('nama') }}<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4> -->
	<h4>BERITA ACARA PERKULIAHAN TAHUN {{ Sia::sessionPeriode('nama') }}<br>
		FAKULTAS TEKNOLOGI DAN BISNIS</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

@foreach( $dosen as $val )

	<table border="0">
		<tr>
			<td width="120">Semester Akademik</td>
			<td>: {{ Sia::sessionPeriode('nama') }}</td>
		</tr>
		<tr>
			<td>Kelas</td>
			<td>: {{ $r->kode_kls }}</td>
		</tr>
		<tr>
			<td>Matakuliah</td>
			<td>: {{ $r->nm_mk }}</td>
		</tr>
		<tr>
			<td>Dosen</td>
			<td>: {{ $val->gelar_depan.' '.$val->nm_dosen.', '.$val->gelar_belakang }}</td>
		</tr>

	</table>
	
	<br>
	
	<table border="1" class="table" width="100%" id="tbl">
		<thead>
			<tr>
				<th rowspan="2" width="10">No</th>
				<th rowspan="2" width="100">Hari/Tanggal</th>
				<th rowspan="2">Waktu</th>
				<th rowspan="2" width="300">Pokok Bahasan</th>
				<th colspan="2">Jml Mahasiswa</th>
				<th colspan="2">Tanda Tangan</th>
			</tr>
			<tr>
				<th>Hadir</th>
				<th>Absen</th>
				<th>Dosen</th>
				<th>MHS</th>
			</tr>
		</thead>
		<tbody>

			<?php
			$absen = DB::table('absen_dosen')
                    ->where('id_jdk', $r->id)
                    ->where('id_dosen', $val->id_dosen)
                    ->orderBy('pertemuan')
                    ->get();

            $hadir = DB::select("
            		SELECT SUM(a_1) as a_1, SUM(a_2) as a_2, SUM(a_3) as a_3, SUM(a_4) as a_4, SUM(a_5) as a_5, SUM(a_6) as a_6, SUM(a_7) as a_7, SUM(a_8) as a_8, SUM(a_9) as a_9, SUM(a_10) as a_10, SUM(a_11) as a_11, SUM(a_12) as a_12, SUM(a_13) as a_13, SUM(a_14) as a_14
            		FROM nilai where id_jdk = '$r->id'
            	");
            $hadir = json_decode(json_encode($hadir[0]), true);

			$no = 1; ?>

            @foreach( $absen as $ab )
                <?php if ( $r->jenis == 2 && $no++ > 10 ) continue; ?>
                <?php $nomor = $loop->iteration ?>
                
                <?php if ( $nomor > 14 ) continue ?>

                <tr>
                    <td align="center">{{ $nomor }}</td>
                    <td align="center">{{ !empty($ab->tgl) ? Carbon::parse($ab->tgl)->format('d/m/Y') : '' }}</td>
                    <td align="center">{{ !empty($ab->tgl) ? $ab->jam_masuk.' - '.$ab->jam_keluar : '' }}</td>
                    <td><?= nl2br($ab->pokok_bahasan) ?></td>
                    <td align="center">{{ $hadir['a_'.$nomor] }}</td>
                    <td align="center">{{ Request::get('pst') - $hadir['a_'.$nomor] }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
		</tbody>
	</table>

@endforeach
</body>
</html>