<!DOCTYPE html>
<html>
<head>
	<title>Cetak Label Ujian</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>
        @media print {
	        @page {
				margin: 0.2cm 0.5cm 0.5cm 0.5cm;
			}
			footer {page-break-after: always;}
		}

			.border-outside {
				border: 1px dashed #000;
				padding: 20px 30px;
			}
			.container-label-ujian {
				border: 3px solid #000;
				padding: 2px;
				font-size: 20px;
				width: 7.5in;
				margin-bottom: 0.5cm;
			}

			.container-label-ujian h2 {
				font-size: 25px !important;
				line-height: 1.5em;
				text-align: center;
			}

			.cl {
				font-family: 'Calligraphic' !important;
			}
		    
	</style>
</head>

<body 
onload="window.print()">

<div class="col-md-12">
	<br>

	@foreach( $jadwal as $r )

		<div class="container-label-ujian">
			<div class="border-outside">
				<h2>
					<span class="cl">STIE NOBEL INDONESIA</span> MAKASSAR<BR>
					NASKAH UJIAN {{ $r->jenis_ujian == 'UTS' ? 'TENGAH' : 'SEMESTER' }} SEMESTER ({{ $r->jenis_ujian }})<br>
					TAHUN AKADEMIK
				</h2>
				<br>
				<table border="0" padding="5" align="center" style="width:100%;line-height: 1.5em;">
					<tr>
						<td width="200">MATA UJIAN</td>
						<td width="10" style="padding-right: 20px"> : </td>
						<td>{{ strtoupper($r->nm_mk) }}</td>
					</tr>
					<tr>
						<td>NAMA DOSEN</td>
						<td> : </td>
						<td>{{ strtoupper($r->dosen) }}</td>
					</tr>
					<tr>
						<td>KODE KELAS</td>
						<td> : </td>
						<td>{{ strtoupper($r->kode_kls) }}</td>
					</tr>
					<tr>
						<td>TANGGAL/JAM</td>
						<td> : </td>
						<td>{{ Rmt::hari($r->hari) }} {{ strtoupper(Rmt::tgl_indo($r->tgl_ujian)) }} / {{ substr($r->jam_masuk, 0,5) }} - {{ substr($r->jam_selesai, 0, 5) }}</td>
					</tr>
					<tr>
						<td>RUANGAN</td>
						<td> : </td>
						<td>{{ strtoupper($r->nm_ruangan) }}</td>
					</tr>
					<tr>
						<td>JUMLAH PESERTA</td>
						<td> : </td>
						<td>{{ $r->jml_peserta }}</td>
					</tr>
					<tr>
						<td>JUMLAH HADIR</td>
						<td> : </td>
						<td>......... ORANG</td>
					</tr>
					<tr>
						<td>JML TIDAK HADIR</td>
						<td> : </td>
						<td>......... ORANG</td>
					</tr>

					<tr>
						<td>PENGAWAS</td>
						<td> : </td>
						<td>{{ strtoupper($r->nm_pengawas) }} </td>
					</tr>

				</table>
			</div>
		</div>

		<?php if ( $loop->iteration % 2 == 0 ) echo "<footer></footer>"; ?>

	@endforeach

</div>

</body>
</html>