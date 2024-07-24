<!DOCTYPE html>
<html>
<head>
	<title>Cetak Dosen</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>DAFTAR NAMA-NAMA DOSEN<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0">
		@if ( Session::has('dosen.prodi') )
			<?php $prodi = DB::table('prodi')->where('id_prodi', Session::get('dosen.prodi'))->first(); ?>
			<tr>
				<td>Program Studi</td>
				<td>: {{ $prodi->jenjang.' '.$prodi->nm_prodi }}</td>
			</tr>
		@endif
		@if ( Session::has('dosen.jenis') )
			<tr>
				<td>Jenis Dosen</td>
				<td>: {{ Sia::jenisDosen(Session::get('dosen.jenis')) }}</td>
			</tr>
		@endif
		@if ( Session::has('dosen.jabatan') )
			<tr>
				<td>Jabatan</td>
				<td>: {{ Sia::jabatanFungsional(Session::get('dosen.jabatan')) }}</td>
			</tr>
		@endif
		@if ( Session::has('dosen.aktivitas') )
			<tr>
				<td>Aktivitas</td>
				<td>: {{ Sia::aktivitasDosen(Session::has('dosen.aktivitas')) }}</td>
			</tr>
		@endif
	</table>
	<br>

	<table border="1" class="table" width="100%">
			<thead>
				<tr>
					<th>No</th>
					<th>NIDN</th>
					<th>Nama Dosen</th>
					<th>Jabatan</th>
					<th>Golongan</th>
					<th>Aktivitas</th>
					<th>Jenis Dosen</th>
					<th>Tempat, tgl lahir</th>
					<th>Alamat</th>
					<th>No. HP</th>
				</tr>
			</thead>
			<tbody>
				@foreach( $dosen as $r )
				<tr>
					<td align="center">{{ $loop->iteration }}</td>
					<td>{{ $r->nidn }}</td>
					<td>{{ Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang) }}</td>
					<td>{{ Sia::jabatanFungsional($r->jabatan_fungsional) }}</td>
					<td align="center">{{ $r->golongan }}</td>
					<td align="center">{{ Sia::aktivitasDosen($r->aktivitas) }}</td>
					<td>{{ Sia::jenisDosen($r->jenis_dosen) }}</td>
					<td>{{ $r->tempat_lahir }}, {{ Rmt::formatTgl($r->tgl_lahir) }}</td>
					<td>{{ $r->alamat }}</td>
					<td>{{ $r->hp }}</td>
				</tr>
				@endforeach
			</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>