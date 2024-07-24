<table border="0">
	<tr>
		<th>Program Studi</th>
		<td> : {{ Sia::prodiEkspor('lk_prodi') }}</td>
	</tr>
	<tr>
		<th>Tahun Akademik</th>
		<td> : {{ Sia::taEkspor('lk_ta') }}
	<tr>
		<th>Angkatan</th>
		<td> : {{ Sia::angkatanEkspor('lk_angkatan') }}</td>
	</tr>
	<tr>
		<th>Status</th>
		<td> : {{ empty(Sia::statusEkspor('lk_status')) ? 'Semua':Sia::statusEkspor('lk_status') }}</td>
	</tr>
</table>

<table border="1" class="table table-bordered">
	<thead>
		<tr>
			<th>No</th>
			<th>NIM</th>
			<th>Nama</th>
			<th>Tempat, Tgl Lahir</th>
			<th>Jenis Kelamin</th>
			<th>Prodi</th>
			<th>Konsentrasi</th>
			<th>Alamat</th>
			<th>No Hp</th>
			<th>Nama Ibu</th>
			<th>Nama Bapak</th>
			<th>Smt lulus/keluar</th>
			<th>Jenis Keluar</th>
			<th>Tgl keluar</th>
			<th>Judul</th>
			<th>IPK</th>
			<th>Predikat</th>
			<th>Seri Ijazah</th>
			<th>PIN</th>
			<th>KODE BATCH</th>
	</thead>
	<tbody>
		@foreach( $mahasiswa as $r )
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $r->nim }}</td>
			<td>{{ $r->gelar_depan }} {{ trim($r->nm_mhs) }}{{ !empty($r->gelar_belakang) ? ', '.$r->gelar_belakang : '' }}., {{ $r->singkatan_gelar }}</td>
			<td>{{ ucwords(strtolower(trim($r->tempat_lahir))) }}, {{ Carbon::parse($r->tgl_lahir)->format('d-m-Y') }}</td>
			<td>{{ $r->jenkel }}</td>
			<td>{{ $r->nm_prodi }} ({{ $r->jenjang }})</td>
			<td>{{ $r->nm_konsentrasi }}</td>
			<td>{{ $r->alamat }}</td>
			<td>{{ $r->hp }}</td>
			<td>{{ $r->nm_ibu }}</td>
			<td>{{ $r->nm_ayah }}</td>
			<td>{{ $r->semester_keluar }}</td>
			<td>{{ $r->ket_keluar }}</td>
			<td>{{ Carbon::parse($r->tgl_keluar)->format('d-m-Y') }}</td>
			<td>{{ $r->judul_skripsi }}</td>
			<td>{{ $r->ipk }}</td>
			<td>{{ ucfirst(Sia::predikat($r->ipk)) }}</td>
			<td>{{ $r->seri_ijazah }}</td>
			<td>{{ $r->pin }}</td>
			<td>{{ $r->kode_batch_pin }}</td>
		</tr>
		@endforeach
	</tbody>
</table>