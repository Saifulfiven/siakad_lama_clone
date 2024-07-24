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
			<td colspan="8"><b>Program Studi</b> : {{ Sia::prodiEkspor() }}</th>
		</tr>
		<tr>
			<td colspan="8"><b>Angkatan</b> : {{ Sia::angkatanEkspor() }}</th>
		</tr>
		@if ( Sia::statusEkspor() )
			<tr>
				<td colspan="8"><b>Status Mahasiswa</b> : {{ Sia::statusEkspor() }}</td>
			</tr>
		@endif

		<tr>
			<th width="10">No</th>
			<th>ID</th>
			<th>NIM</th>
			<th>NIK</th>
			<th>Nama</th>
			<th>Kelamin</th>
			<th>Agama</th>
			<th>Tempat</th>
			<th>Tgl lahir</th>
			<th>Alamat</th>
			<th>Kelurahan</th>
			<th>Wilayah</th>
			<th>HP</th>
			<th>Email</th>
			<th>Nama Ayah</th>
			<th>Nama Ibu</th>
			<th>Pekerjaan Ayah</th>
			<th>Pekerjaan Ibu</th>
			<th>No HP Orang Tua</th>
			<th>Alamat Orang Tua</th>
			<th>Prodi</th>
			<th>Kelas</th>
			<th>Info Nobel</th>
			<th>Kabupaten</th>
			<th>Provinsi</th>
			<th>Asal Sekolah</th>
			<th>PT Asal</th>
			<th>Prodi Asal</th>
			<th>Status</th>
			<th>Status Pembayaran</th>
			<th>Created At</th>
		</tr>

		@foreach( $mahasiswa as $r )
			<tr>
				<td align="center">{{ $loop->iteration }}</td>
				<td>{{ $r->id_mhs_reg }}</td>
				<td>{{ $r->nim }}</td>
				<td>{{ $r->nik }}</td>
				<td align="left">{{ $r->nm_mhs }}</td>
				<td>{{ Sia::nmJenisKelamin($r->jenkel) }}</td>
				<td>{{ $r->nm_agama }}</td>
				<td>{{ $r->tempat_lahir }}</td>
				<td>{{ $r->tgl_lahir }}</td>
				<td>{{ $r->alamat }}</td>
				<td>{{ $r->dusun }}</td>
				<td>{{ $r->nm_wil }}</td>
				<td>{{ $r->hp }}</td>
				<td>{{ $r->email }}</td>
				<td>{{ $r->nm_ayah }}</td>
				<td>{{ $r->nm_ibu }}</td>
				<td>{{ $r->pkj_ayah }}</td>
				<td>{{ $r->pkj_ibu }}</td>
				<td>{{ $r->hp_ibu }} {{ !empty($r->hp_ayah) ? ' / '.$r->hp_ayah : '' }}</td>
				<td>{{ $r->alamat_ortu }}</td>
				<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
				<td>{{ $r->kode_kelas }}</td>
				<td>{{ $r->nm_info }}</td>
				<td>{{ Sia::kabupaten($r->id_wil) }}</td>
				<td>{{ Sia::provinsi($r->id_wil) }}</td>
				<td>{{ $r->nm_sekolah }}</td>
				<td>{{ $r->nm_pt_asal }}</td>
				<td>{{ $r->nm_prodi_asal }}</td>
				<td>{{ $r->ket_keluar == '' ? 'AKTIF' : $r->ket_keluar }}</td>
				<td>{{ $r->bebas_pembayaran == '1' ? 'Bebas' : 'Belum Bebas' }}</td>
				<td>{{ Carbon::parse($r->created_at)->format('d F Y') }}</td>
			</tr>
		@endforeach

	</table>

</body>
</html>