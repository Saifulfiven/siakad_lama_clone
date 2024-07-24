<!DOCTYPE html>
<html>
<head>
	<title>Ekspor Mahasiswa</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

	<table border="1" class="table table-bordered">
		<tr><td colspan="8"><h3>STIE NOBEL INDONESIA MAKASSAR</h3></td></tr>
		<tr><td colspan="8">Jl. Sultan Alauddin No. 212 Makassar</td></tr>
		<tr>
			<td colspan="8">Kelas Mahasiswa</th>
		</tr>

				<tr>
					<th width="10">No</th>
					<th>NIM</th>
					<th>Nama</th>
					<th>Kelas</th>
					<th>Program Studi</th>
				</tr>
				@foreach( $mahasiswa as $r )
				<tr>
					<td align="center">{{ $loop->iteration }}</td>
					<td>{{ $r->nim }}</td>
					<td>{{ $r->nm_mhs }}</td>
					<td>{{ $r->kode_kelas }}</td>
					<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
				</tr>
				@endforeach

	</table>

</body>
</html>