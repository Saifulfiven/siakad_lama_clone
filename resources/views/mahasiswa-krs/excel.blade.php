<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
	<thead class="custom">
		<tr>
			<th>Nama</th>
			<th>NIM</th>
			<th>Kelas</th>
			<th>SKS Diprogram</th>
			<th>Jalur</th>s
		</tr>
	</thead>
	<tbody align="center">
		@foreach($mahasiswa as $r)
			<tr>
				<td align="left">
					{{ $r->nm_mhs }}
				</td>
				<td>{{ $r->nim }}</td>
				<td>{{ $r->kode_kelas }}</td>
				<td>
					@if ( empty($r->sks_diprogram) && $r->jalur == 'online' )
						<small>KRS Belum di Alokasikan oleh Akademik</small>
					@else 
						{{ $r->sks_diprogram }}
					@endif
				</td>
				<td>{{ strtoupper($r->jalur) }}</td>
			</tr>
		@endforeach
	</tbody>
</table>