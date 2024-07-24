<!DOCTYPE html>
<html>
<head>
	<title>Cetak Absensi Mahasiswa</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>

		@media print  
        {
            @page {

                margin: 10mm 10mm 10mm 10mm;
                width: 33cm;
            }
            tr{
                page-break-inside: avoid;
            }
            .footer{
                page-break-inside: avoid;
            }
            footer {
            	page-break-after: always;
            }
        }
	</style>
</head>
<body onload="window.print()">

@if ( count($dosen) == 0 )
	<center>Belum ada dosen mengajar pada kelas ini</center>
@endif

<?php $no = 1 ?>

@foreach( $dosen as $val )

	<center>
	<!-- <h4>ABSENSI MAHASISWA TAHUN {{ Sia::sessionPeriode('nama') }}<br>SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4> -->
	<h4>ABSENSI MAHASISWA TAHUN {{ Sia::sessionPeriode('nama') }}<br>INSTITUT TEKNOLOGI DAN BISNIS</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0">
		<tr>
			<td width="100">Program Studi</td>
			<td width="300">: {{ $r->jenjang.' '.$r->nm_prodi }}</td>

			<td width="100">Kelas</td>
			<td>: {{ $r->kode_kls }}</td>
		</tr>
		<tr>
			<td>Dosen</td>
			<td>: {{ $val->gelar_depan.' '.$val->nm_dosen.', '.$val->gelar_belakang }}</td>

			<td>Ruangan</td>
			<td>: {{ $r->ruangan }}</td>
		</tr>
		<tr>
			<td>Hari/Jam</td>
			<td>: {{ Rmt::hari($r->hari) }}, {{ substr($r->jam_masuk,0,5).' - '.substr($r->jam_keluar,0,5) }}</td>

			<td>Semester</td>
			<td>: {{ $r->smt }}</td>
		</tr>
		<tr>
			<td>Matakuliah</td>
			<td>: {{ $r->nm_mk }}</td>

			<td>Jumlah Peserta</td>
			<td>: {{ empty($r->terisi) ? '':$r->terisi }}</td>
		</tr>

	</table>
	
	<br>
	<!-- dosen pertama -->
	<!-- hanya untuk s2 -->
	@if ( $no == 1 )
		<?php 
			$start = 1;
			$jml_pertemuan = 6;
		?>
	@else
		<?php 
			$start = 7;
			$jml_pertemuan = 12;
		?>
	@endif

	<table border="1" class="table" width="100%" id="tbl">
		<thead>
			<tr>
				<th rowspan="2" width="10">NO</th>
				<th rowspan="2" width="80">NIM</th>
				<th rowspan="2">NAMA</th>
				@if ( $r->id_prodi == '61101' )
					<th colspan="6">PERTEMUAN</th>
				@else
					<th colspan="16">PERTEMUAN</th>
				@endif
			</tr>

			@if ( $r->id_prodi == '61101' )

				<tr>
	                <?php for ( $per = $start; $per <= $jml_pertemuan; $per++ ) { ?>
	                    <th style="padding:1px;font-size:11px" class="text-center" width="40">
	                        <?= $per ?>
	                    </th>
	                <?php } ?>
				</tr>

			@else
				<tr>
	                <?php for ( $per = 1; $per <= 7; $per++ ) { ?>
	                    <th style="padding:1px;font-size:11px" class="text-center" width="40">
	                        <?= $per ?>
	                    </th>
	                <?php } ?>
	                <th width="40">%</th>
	                <?php for ( $per = 8; $per <= 14; $per++ ) { ?>
	                    <th style="padding:1px;font-size:11px" class="text-center" width="40">
	                        <?= $per ?>
	                    </th>
	                <?php } ?>
	                <th width="40">%</th>
				</tr>
			@endif
		</thead>
		<tbody>
			@foreach( $mahasiswa as $res )
				<tr>
					<td align="center">{{ $loop->iteration }}</td>
					<td align="center">{{ $res->nim }}</td>
					<td>{{ $res->nm_mhs }}</td>
					@if ( $r->id_prodi == '61101' )
						@for( $kol = 1; $kol <= 6; $kol++ )
							<td></td>
						@endfor
					@else
						@for( $kol = 1; $kol <= 16; $kol++ )
							<td></td>
						@endfor
					@endif
				</tr>
			@endforeach
		</tbody>
	</table>

	<br>

	<div class="footer">
		<table width="100%" border="0">
			<tr>
				<td></td>
				<td width="300" align="center">Dosen Matakuliah</td>
			</tr>
			<tr>
				<td colspan="2"><br><br><br><br><br></td>
			</tr>
			<tr><td></td><td align="center">{{ $val->gelar_depan.' '.$val->nm_dosen.', '.$val->gelar_belakang }}</td></tr>
		</table>
	</div>

	<footer></footer>

	<?php $no++ ?>
@endforeach
</body>
</html>