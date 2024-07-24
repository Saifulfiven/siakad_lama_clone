<!DOCTYPE html>
<html>
<head>
	<title>Ekspor Dosen</title>

</head>
<body>

	<table border="1" class="table" width="100%">
				<tr>
					<td colspan="12"><h4>DAFTAR NAMA-NAMA DOSEN<br>
						SEKOLAH TINGGI ILMU EKONOMI (STIE) NOBEL INDONESIA</h4>
					</td>
				</tr>
				<tr><td colspan="12"></td></tr>
				<tr>
					<th>NIDN</th>
					<th>Nama Dosen</th>
					<th>Jabatan</th>
					<th>Golongan</th>
					<th>Pendidikan terakhir</th>
					<th>Aktivitas</th>
					<th>Jenis Dosen</th>
					<th>Tempat, tgl lahir</th>
					<th>Alamat</th>
					<th>No. HP</th>
					<th>Kode Prodi</th>
					<th>Jenis Dosen</th>
				</tr>
				@foreach( $dosen as $r )
				<tr>
					<td>{{ $r->nidn }}</td>
					<td>{{ Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang) }}</td>
					<td>{{ Sia::jabatanFungsional($r->jabatan_fungsional) }}</td>
					<td align="center">{{ $r->golongan }}</td>
					<td align="center">{{ $r->pendidikan_tertinggi }}</td>
					<td align="center">{{ Sia::aktivitasDosen($r->aktivitas) }}</td>
					<td>{{ Sia::jenisDosen($r->jenis_dosen) }}</td>
					<td>{{ $r->tempat_lahir }}, {{ Rmt::formatTgl($r->tgl_lahir) }}</td>
					<td>{{ $r->alamat }}</td>
					<td>{{ $r->hp }}</td>
					<td>{{ $r->id_prodi }}</td>
					<td>{{ $r->jenis_dosen }}</td>
				</tr>
				@endforeach
	</table>

</body>
</html>