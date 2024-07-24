<!DOCTYPE html>
<html>
<head>
	<title>Cetak Label Absen</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style type="text/css">
    body {
    	background-color: #fff;
    	color: #000;
    }
    table, tr, td, th {
    	color: #000 !important;
    	border-color: #000 !important;
    }
    .kontainer {
    	border: 2px solid #000;
    	padding: 5px;
    	float: left;
    	width: 400px;
    	margin: 1px;
    }
		@media print {
			 @page {
			  	/*width: 32.5cm;*/
				margin: 0.5cm 0.5cm 0.5cm 0.5cm;
			}
			footer {page-break-after: always;}
		}
    </style>
</head>
<body onload="window.print()">

@foreach( $jadwal as $r )

	<div class="kontainer">
		<center>
			<h4><b><u>ABSENSI DOSEN DAN MAHASISWA</u></b></h4>
		</center>
		<table border="0" width="100%">
			<tr>
				<td width="80px">NAMA DOSEN</td>
				<td>: {{ $r->dosen }}</td>
			</tr>
			<tr>
				<td>MATAKULIAH</td>
				<td>: {{ ucwords(strtolower($r->nm_mk)) }}</td>
			</tr>
			<tr>
				<td>HARI/JAM</td>
				<td>: {{ Rmt::hari($r->hari) }} / {{ substr($r->jam_masuk, 0, 5) .' - '.substr($r->jam_keluar, 0, 5) }}</td>
			</tr>
			<tr>
				<td>KELAS/RUANG</td>
				<td>: {{ $r->kode_kls }} / {{ $r->nm_ruangan }}</td>
			</tr>
		</table>
	</div>

	@if ( $loop->iteration % 16 == 0 )
		<footer></footer>
	@endif

@endforeach

</body>
</html>