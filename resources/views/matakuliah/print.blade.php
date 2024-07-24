<!DOCTYPE html>
<html>
<head>
	<title>Cetak Matakuliah</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>DAFTAR MATAKULIAH<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>
	<table border="1" class="table" width="100%">
			<thead>
				<tr>
					<th>No</th>
					<th>Kode MK</th>
					<th>Nama Matakuliah</th>
					<th>SKS</th>
					<th>Program Studi</th>
					<th>Jenis MK</th>
			</thead>
			<tbody>
				@foreach( $matakuliah as $r )
					<tr>
						<td align="center">{{ $loop->iteration }}</td>
						<td>{{ $r->kode_mk }}</td>
						<td align="left">{{ $r->nm_mk }}</td>
						<td align="center">{{ $r->sks_mk }}</td>
						<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
						<td align="center">{{ Sia::jenisMatakuliah($r->jenis_mk) }}</td>
					</tr>
				@endforeach
			</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>