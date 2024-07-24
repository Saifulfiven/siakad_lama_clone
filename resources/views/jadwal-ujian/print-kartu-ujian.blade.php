<!DOCTYPE html>
<html>
<head>
	<title>Cetak Kartu Ujian</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>
		@media print  
        {
            footer{
                page-break-after: always;
            }
        }
	</style>
</head>
<body onload="window.print()">


@if ( !empty(Request::get('mhs')) )

	<div>
		<center>
		<h4>KARTU UJIAN<br>
			SEKOLAH TINGGI ILMU EKONOMI (STIE)<br>

		<b>NOBEL INDONESIA</b></h4>
		</center>

		<div class="garis-1"></div>
		<div class="garis-2"></div>

		<table border="0" class="table" width="100%" id="tbl">
			<tr>
				<td width="150">NIM</td>
				<td>: {{ $mhs->nim }}</td>
				<td width="150">TA. Akademik</td>
				<td>: {{ substr($mhs->id_smt,0,4) }}</td>
			</tr>
			<tr>
				<td>Nama Mahasiswa</td>
				<td>: {{ $mhs->nm_mhs }}</td>
				<td>Semester</td>
				<td>: {{ substr($mhs->id_smt, 4, 1) == 1 ? 'GANJIL' : 'GENAP' }}</td>
			</tr>
			<tr>
				<td>Jurusan</td>
				<td>: {{ $mhs->nm_prodi }}</td>
				<td>Jenis Ujian</td>
				<td>: {{ Session::get('jdu_jenis_ujian') }}</td>
			</tr>
		</table>

		<br>

		<table border="1" class="table" width="100%" id="tbl">
			<thead>
				<tr>
					<th width="15">No</th>
					<th>Matakuliah</th>
					<th>Hari</th>
					<th>Jam</th>
					<th>Ruangan</th>
					<th>Paraf Pengawas</th>
				</tr>
			</thead>
			<tbody>
				@foreach( $matakuliah as $mk )
					<tr>
						<td align="center">{{ $loop->iteration }}</td>
						<td>{{ $mk->nm_mk }}</td>
						<td>{{ Rmt::hari($mk->hari) }}, {{ Carbon::parse($mk->tgl_ujian)->format('d-m-Y') }}</td>
						<td align="center">{{ substr($mk->jam_masuk,0,5) }} - {{ substr($mk->jam_selesai,0,5) }}</td>
						<td align="center">{{ $mk->nm_ruangan }}</td>
						<td></td>
					</tr>
				@endforeach

			</tbody>
		</table>

		<br>
		<br>

		<table border="0" width="100%">
			<tr>
				<td width="65%"></td>
				<td>
					Makassar, {{ Rmt::tgl_indo(Carbon::today()->format('Y-m-d')) }}<br>
					{{ Config::get('pt') }}
					<br>
					<br>
					<br>
					<br>
					<br>
					@if ( in_array(61101, Sia::getProdiUser()) )
                        <b>{{ Sia::option('kabag_akademik_s2') }}</b>
                    @else
                        <b>{{ Sia::option('kabag_akademik') }}</b>
                    @endif
					<p>Kabag. Adm. Akademik</p>
				</td>
			</tr>
		</table>

		<footer></footer>
	</div>

@else
	
	<?php $no = 0 ?>

	@foreach( $mahasiswa as $mhs )
		<?php $no++  ?>
		<div style="height: 15.5cm">
			<center>
			<h4>KARTU UJIAN<br>
				SEKOLAH TINGGI ILMU EKONOMI (STIE)<br>

			<b>NOBEL INDONESIA</b></h4>
			</center>

			<div class="garis-1"></div>
			<div class="garis-2"></div>

			<table border="0" class="table" width="100%" id="tbl">
				<tr>
					<td width="150">NIM</td>
					<td>: {{ $mhs->nim }}</td>
					<td width="150">TA. Akademik</td>
					<td>: {{ substr($mhs->id_smt,0,4) }}</td>
				</tr>
				<tr>
					<td>Nama Mahasiswa</td>
					<td>: {{ $mhs->nm_mhs }}</td>
					<td>Semester</td>
					<td>: {{ substr($mhs->id_smt, 4, 1) == 1 ? 'GANJIL' : 'GENAP' }}</td>
				</tr>
				<tr>
					<td>Jurusan</td>
					<td>: {{ $mhs->nm_prodi }}</td>
					<td>Jenis Ujian</td>
					<td>: {{ Session::get('jdu_jenis_ujian') }}</td>
				</tr>
			</table>

			<br>

			<table border="1" class="table" width="100%" id="tbl">
				<thead>
					<tr>
						<th width="15">No</th>
						<th>Matakuliah</th>
						<th>Hari</th>
						<th>Jam</th>
						<th>Ruangan</th>
						<th>Paraf Pengawas</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$matakuliah = Sia::ujianMhs(Session::get('jdu_semester'), Session::get('jdu_jenis_ujian'))
	    								->where('pu.id_mhs_reg', $mhs->id_mhs_reg)->get(); ?>
					@foreach( $matakuliah as $mk )
						<tr>
							<td align="center">{{ $loop->iteration }}</td>
							<td>{{ $mk->nm_mk }}</td>
							<td>{{ Rmt::hari($mk->hari) }}, {{ Carbon::parse($mk->tgl_ujian)->format('d-m-Y') }}</td>
							<td align="center">{{ substr($mk->jam_masuk,0,5) }} - {{ substr($mk->jam_selesai,0,5) }}</td>
							<td align="center">{{ $mk->nm_ruangan }}</td>
							<td></td>
						</tr>
					@endforeach

				</tbody>
			</table>

			<br>
			<br>

			<table border="0" width="100%">
				<tr>
					<td width="65%"></td>
					<td>
						Makassar, {{ Rmt::tgl_indo(Carbon::today()->format('Y-m-d')) }}<br>
						{{ Config::get('pt') }}
						<br>
						<br>
						<br>
						<br>
						<br>
						@if ( in_array(61101, Sia::getProdiUser()) )
	                        <b>{{ Sia::option('kabag_akademik_s2') }}</b>
	                    @else
	                        <b>{{ Sia::option('kabag_akademik') }}</b>
	                    @endif
						<p>Kabag. Adm. Akademik</p>
					</td>
				</tr>
			</table>

		</div>
		@if ( $no % 2 == 0 )
			<footer></footer>
		@endif
	@endforeach
@endif

</body>
</html>