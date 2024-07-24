<!DOCTYPE html>
<html>
<head>
	<title>Cetak Hasil Pemilihan konsentrasi</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>HASIL PEMILIHAN KONSENTRASI MAHASISWA PROGRAM PASCASARJANA<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="1" class="table" width="100%">
		<thead>
			<tr>
				<th>NO</th>
				<th>NIM</th>
				<th>Nama</th>
				<th>Kelas</th>
				<th>Konsentrasi</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $mhs as $r )
			<tr>
				<td align="center">{{ $loop->iteration }}</td>
				<td align="center">{{ $r->nim }}</td>
				<td>{{ $r->nm_mhs }}</td>
				<td align="center">{{ $r->kelas }}</td>
				<td>{{ $r->nm_konsentrasi }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>