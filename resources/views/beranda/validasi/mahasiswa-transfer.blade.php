<p>Validasi ini menampilkan mahasiswa transfer namun jumlah sks konversi masih 0. (Selama masih ada mahasiswa, proses naik semester tidak dapat dilakukan)</p>
<div class="table-responsive">
	<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
		<thead class="custom">
				<tr>
					<th>No.</th>
					<th>Nama</th>
					<th>NIM</th>
					<th>Prodi</th>
					<th>Ket</th>
				</tr>
		</thead>
		<tbody align="center">
			@foreach($transfer as $r)
				<tr>
					<td>{{ $loop->iteration }}</td>
					<td align="left"><a href="{{ route('mahasiswa_detail', ['id' => $r->id])}}">{{ $r->nm_mhs }}</a></td>
					<td>{{ $r->nim }}</td>
					<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
					<td>Wajib diperbaiki</td>
				</tr>
			@endforeach
		</tbody>
	</table>

</div>