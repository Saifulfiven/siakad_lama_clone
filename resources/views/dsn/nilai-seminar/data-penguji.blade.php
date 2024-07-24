<table class="table table-bordered">

	<thead class="custom">
		<tr>
			<th width="30">No</th>
			<th style="text-align: left;">Nama Dosen</th>
			<th>Jabatan</th>
			<th>Nilai Rata-rata</th>
		</tr>
	</thead>

	@foreach( $penguji as $p )
	    <tr>
	        <td align="center">{{ $loop->iteration }}</td>
	        <td><?= $p->nm_dosen ?></td>
	        <td align="center">
	            {{ $p->jabatan == 'ANGGOTA2' ? 'ANGGOTA' : $p->jabatan }}
	        </td>
	        <td align="center">
	        	{{ empty($p->nilai) ? '-': $p->nilai }}
	        </td>
	    </tr>
    @endforeach
</table>