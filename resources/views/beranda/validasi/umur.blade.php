<p>Validasi ini menampilkan mahasiswa yang berumur < 15 tahun</p>

<div class="table-responsive">
	<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
		<thead class="custom">
				<tr>
					<th>No.</th>
					<th>Nama</th>
					<th>Tgl Lahir</th>
					<th>Umur</th>
					<th>Ket</th>
				</tr>
		</thead>
		<tbody align="center">
			@foreach($umur as $r)
				<tr>
					<td>{{ $loop->iteration }}</td>
					<td align="left"><a href="{{ route('mahasiswa_detail', ['id' => $r->id])}}">{{ $r->nm_mhs }}</a></td>
					<td>{{ Carbon::parse($r->tgl_lahir)->format('d-m-Y') }}</td>
					<td>{{ number_format($r->umur,1) }}</td>
					<td>Lapor ke IT (Operator)</td>
				</tr>
			@endforeach
		</tbody>
	</table>

</div>