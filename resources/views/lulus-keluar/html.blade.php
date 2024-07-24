<table border="0">
	<tr>
		<th align="left">Program Studi</th>
		<td> : {{ Sia::prodiEkspor('lk_prodi') }}</td>
	</tr>
	<tr>
		<th align="left">Tahun Akademik</th>
		<td> : {{ Sia::taEkspor('lk_ta') }}
	<tr>
		<th align="left">Angkatan</th>
		<td> : {{ Sia::angkatanEkspor('lk_angkatan') }}</td>
	</tr>
	<tr>
		<th align="left">Status</th>
		<td> : {{ empty(Sia::statusEkspor('lk_status')) ? 'Semua':Sia::statusEkspor('lk_status') }}</td>
	</tr>
	<tr>
		<th align="left">Total Wisudawan</th>
		<td>: {{ count($mahasiswa) }}</td>
	</tr>
</table>

<table border="1" class="table table-bordered">
	<thead>
		<tr>
			<th>nim1</th>
			<th>nama1</th>
			<th>ttl1</th>
			<th>alamat1</th>
			<th>hp1</th>
			<th>ayah1</th>
			<th>ibu1</th>
			<th>jurusan1</th>
			<th>konsentrasi1</th>
			<th>skripsi1</th>
			<th>ipk1</th>
			<th>seri_ijazah1</th>

			<th>nim2</th>
			<th>nama2</th>
			<th>ttl2</th>
			<th>alamat2</th>
			<th>hp2</th>
			<th>ayah2</th>
			<th>ibu2</th>
			<th>jurusan2</th>
			<th>konsentrasi2</th>
			<th>skripsi2</th>
			<th>ipk2</th>
			<th>seri_ijazah2</th>

			<th>nim3</th>
			<th>nama3</th>
			<th>ttl3</th>
			<th>alamat3</th>
			<th>hp3</th>
			<th>ayah3</th>
			<th>ibu3</th>
			<th>jurusan3</th>
			<th>konsentrasi3</th>
			<th>skripsi3</th>
			<th>ipk3</th>
			<th>seri_ijazah3</th>

			<th>nim4</th>
			<th>nama4</th>
			<th>ttl4</th>
			<th>alamat4</th>
			<th>hp4</th>
			<th>ayah4</th>
			<th>ibu4</th>
			<th>jurusan4</th>
			<th>konsentrasi4</th>
			<th>skripsi4</th>
			<th>ipk4</th>
			<th>seri_ijazah4</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no = 1;
			$nim = '';
		?>
		@foreach( $mahasiswa as $r )
		
			<?php if ( $r->nim == $nim ) continue; ?>
			
			<?php if ( $no == 1 ) echo '<tr>'; ?>

				<td>{{ $r->nim }}</td>
				<td>
					{{ $r->gelar_depan }} {{ trim($r->nm_mhs) }}{{ !empty($r->gelar_belakang) ? ', '.$r->gelar_belakang.'., ' : ', ' }} {{ $r->singkatan_gelar }}</td>
				<td>{{ ucwords(strtolower(trim($r->tempat_lahir))) }}, {{ Carbon::parse($r->tgl_lahir)->format('d-m-Y') }}</td>
				<td>{{ $r->alamat }}</td>
				<td>{{ $r->hp }}</td>
				<td>{{ $r->nm_ayah }}</td>
				<td>{{ $r->nm_ibu }}</td>
				<td>{{ $r->nm_prodi }}</td>
				<td>{{ $r->nm_konsentrasi }}</td>
				<td>{{ $r->judul_skripsi }}</td>
				<td>{{ $r->ipk }}</td>
				<td>{{ $r->seri_ijazah }}</td>
			<?php 
				if( !($no % 4) ) { 
					echo '</tr>';
					echo '<tr>';
				}

				$nim = $r->nim;
				$no++;
			?>
		@endforeach
	</tbody>
</table>