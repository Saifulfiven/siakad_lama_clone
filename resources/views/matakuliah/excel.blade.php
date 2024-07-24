<!DOCTYPE html>
<html>
<head>
	<title>Ekspor Dosen</title>
</head>
<body>

	<table border="1" class="table" width="100%">
				<tr>
					<td colspan="12"><h4>DAFTAR MATAKULIAH<br>
						SEKOLAH TINGGI ILMU EKONOMI (STIE) NOBEL INDONESIA</h4>
					</td>
				</tr>
				<tr><td colspan="12"></td></tr>
				<tr>
					<th>Kode MK</th>
					<th>Nama Matakuliah</th>
					<th>SKS Tatap Muka</th>
					<th>SKS Praktikum</th>
					<th>SKS Praktek Lapangan</th>
					<th>SKS Simulasi</th>
					<th>Total SKS</th>
					<th>Program Studi</th>
					<th>Jenis MK</th>
					<th>ID Kelompok MK</th>
					<th>Kelompok MK</th>
					<th>Tgl Mulai Efektif</th>
					<th>Tgl Akhir Efektif</th>
			</thead>
			<tbody>
				@foreach( $matakuliah as $r )
					<tr>
						<td>{{ $r->kode_mk }}</td>
						<td>{{ $r->nm_mk }}</td>
						<td>{{ $r->sks_tm }}</td>
						<td>{{ $r->sks_prak }}</td>
						<td>{{ $r->sks_prak_lap }}</td>
						<td>{{ $r->sks_sim }}</td>
						<td>{{ $r->sks_mk }}</td>
						<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
						<td>{{ Sia::jenisMatakuliah($r->jenis_mk) }}</td>
						<td>{{ $r->kelompok_mk }}</td>
						<td>{{ Sia::kelompokMatakuliah($r->kelompok_mk) }}</td>
						<td>{{ Rmt::formatTgl($r->tgl_mulai_efektif) }}</td>
						<td>{{ Rmt::formatTgl($r->tgl_akhir_efektif) }}</td>
					</tr>
				@endforeach
			</tbody>
	</table>

</body>
</html>