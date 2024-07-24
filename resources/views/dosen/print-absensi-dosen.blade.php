<!DOCTYPE html>
<html>
<head>
	<title>Cetak Absensi Dosen</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap.min.css" />

    <style type="text/css">
    	body {
    		font-size: 11px;
    	}
        @media print {
	        @page {
	        	/*width: 32.5cm;*/
				margin: 1.2cm 0.5cm 0.5cm 0.5cm;
			}
		    footer {page-break-after: always;}
		}
    </style>
</head>
<body onload="window.print()">

@for( $h = 1; $h <= 7; $h++ )
	<div class="pull-left"><b>ABSENSI DOSEN STIE NOBEL INDONESIA TAHUN AKADEMIK {{ Sia::sessionPeriode('nama') }}</b></div>
	<div class="pull-right"><b>{{ Rmt::hari($h) }}</b></div>

	<?php
		$matakuliah = Sia::jadwalKuliah('x',1)
						->where('jdk.hari', $h)
						->where('jdk.id_smt', Sia::sessionPeriode())
						->get();
	?>
	<table border="1" class="table table-striped" width="100%">
		<thead>
			<tr>
				<th style="text-align: center">Jam</th>
				<th>Matakuliah</th>
				<th style="text-align: center">SMT</th>
				<th style="text-align: center">RGN</th>
				<th>DOSEN</th>
				@for($i = 1; $i <= 16; $i++)
					@if ( $i == 8 )
						<th width="40" style="text-align: center">UTS</th>
					@elseif( $i == 16 )
						<th width="40" style="text-align: center">UAS</th>
					@else
						<th width="40" style="text-align: center">{{ $i > 7 ? $i - 1 : $i }}</th>
					@endif
				@endfor
			</tr>
		</thead>
		<tbody>
			@foreach( $matakuliah as $r )
			<tr>
				<td align="center">{{ $r->jam_masuk }}</td>
				<td>{{ $r->nm_mk }}</td>
				<td align="center">{{ $r->smt }}</td>
				<td align="center">{{ $r->nm_ruangan }}</td>
				<td>{{ $r->dosen }}</td>
				@for($i = 1; $i <= 16; $i++)
					@if ( $i == 8 || $i == 16 )
						<td style="background-color: #eee"></td>
					@else
						<th></td>
					@endif
				@endfor
			</tr>
			@endforeach
		</tbody>
	</table>

	<footer></footer>
@endfor

</body>
</html>