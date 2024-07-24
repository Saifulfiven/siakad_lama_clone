<!DOCTYPE html>
<html>
<head>
	<title>Pemilihan Konsentrasi</title>

</head>
<body>

	<table border="1" class="table" width="100%">
		<tr>
			<td colspan="12"><h4>DAFTAR HASIL PEMILIHAN KONSENTRASI MAHASISWA PASCASARJANA<br>
				SEKOLAH TINGGI ILMU EKONOMI (STIE) NOBEL INDONESIA</h4>
			</td>
		</tr>
		<tr><td colspan="12"></td></tr>
		<tr>
			<th>NIM</th>
			<th>Nama</th>
			<th>Kelas</th>
			<th>Konsentrasi</th>
		</tr>
		@foreach( $mhs as $r )
		<tr>
			<td>{{ $r->nim }}</td>
			<td>{{ $r->nm_mhs }}</td>
			<td>{{ $r->kelas }}</td>
			<td>{{ $r->nm_konsentrasi }}</td>
		</tr>
		@endforeach
	</table>

</body>
</html>