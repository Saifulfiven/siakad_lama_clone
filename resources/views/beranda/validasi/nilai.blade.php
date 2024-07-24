<p>Daftar mahasiswa yang belum masuk nilainya</p>

<div class="table-responsive">
	<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
		<thead class="custom">
				<tr>
					<th>No.</th>
					<th>Nama</th>
					<th>NIM</th>
					<th>Prodi</th>
					<th>Matakuliah</th>
					<th>Dosen</th>
				</tr>
		</thead>
		<tbody align="center">
			@foreach($nilai as $r)
				<tr>
					<td>{{ $loop->iteration - 1 + $nilai->firstItem() }}</td>
					<td align="left">{{ $r->nm_mhs }}</a></td>
					<td>{{ $r->nim }}</td>
					<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
					<td align="left">{{ $r->nm_mk }}</td>
					<td align="left">{{ $r->dosen }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	@if ( $nilai->total() == 0 )
		&nbsp; Tidak ada data
	@endif

	@if ( $nilai->total() > 0 )
		<div class="pull-left">
			Jumlah data : {{ $nilai->total() }}
		</div>
	@endif

	<div class="pull-right"> 
		{{ $nilai->render() }}
	</div>
</div>