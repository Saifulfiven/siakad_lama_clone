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
	<h4>BERITA ACARA PERKULIAHAN TAHUN {{ Sia::sessionPeriode('nama') }}<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

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
			@for( $i = 1; $i <= 10; $i++ )
				<tr>
					<td align="center">{{ $i }}</td>
					<td><br><br><br></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			@endfor
		</tbody>
	</table>

@endforeach
</body>
</html>