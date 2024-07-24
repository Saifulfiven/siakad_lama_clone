<table>
	<tr>
		<th>NIM</th>
		<th>Nama</th>
		<th>Prodi</th>
		<th>Jenis Keluar</th>
		<th>Tgl Keluar</th>
		<th>Periode Keluar</th>
	</tr>
	@foreach( $mahasiswa->data as $val )
		<tr>
			<td>{{ $val->nim }}</td>
			<td>{{ $val->nama_mahasiswa }}</td>
			<td>{{ Feeder::nmProdi($val->id_prodi) }}</td>
			<td>{{ $val->nama_jenis_keluar }}</td>
			<td>{{ $val->tanggal_keluar }}</td>
			<td>{{ $val->id_periode_keluar }}</td>
		</tr>
    @endforeach
</table>