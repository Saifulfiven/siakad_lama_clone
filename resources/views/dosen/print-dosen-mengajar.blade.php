<!DOCTYPE html>
<html>
<head>
	<title>Cetak Dosen Mengajar</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>DAFTAR NAMA-NAMA DOSEN MENGAJAR<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	<h4>T.A {{ $ta->nm_smt }}</h4>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="1" class="table" width="100%">
		<thead>
			<tr>
				<th>No</th>
				<th>Nama</th>
				<th>No. HP</th>
				<th>Alamat</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $dosen as $r )
			<tr>
				<td align="center">{{ $loop->iteration }}</td>
				<td>{{ Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang) }}</td>
				<td>{{ $r->hp }}</td>
				<td>{{ $r->alamat }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>