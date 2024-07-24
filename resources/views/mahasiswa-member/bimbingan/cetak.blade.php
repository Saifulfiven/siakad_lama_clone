<!DOCTYPE html>
<html>
<head>
	<title>Cetak Bimbingan</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<link type=text/css rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap-table.min.css" >
	<style>

		@media print {
	        @page {
	        	size: portrait;
			}
		}

		body {
			margin: 0mm 10mm 0mm 10mm;
			font-size: 12pt;
		}

		.kontainer {
			max-width: 684px;
		}
		ol {
			list-style-type: decimal;
			margin: 0 0 10px 20px;
			padding: 0;
		}
		ul {
			list-style: inside !important;
		}

		.border-top {
			border-top: 1px dotted #999
		}

		table {
			font-size: 12pt;
		}

		table .alamat {
			font-size: 11pt !important;
		}

	</style>
</head>
<body onload="window.print()">

<div class="kontainer">
	@include('layouts.kop-s1')

	<hr>

	<center>
		<h3><b>KARTU KONTROL BIMBINGAN {{ Sia::sessionMhs('prodi') == 61101 ? 'TESIS' : 'SKRIPSI' }}</b></h3>
	</center>
	<br>
	<table class="header">
		<tr>
			<td width="230">Nama</td>
			<td>: {{ $mhs->mhs->nm_mhs }}</td>
		</tr>
		<tr>
			<td width="120">NIM</td>
			<td>: {{ $mhs->nim }}</td>
		</tr>
		<tr>
			<td>Program Studi</td>
			<td>: {{ $mhs->prodi->nm_prodi.' ('.$mhs->prodi->jenjang.')' }}</td>
		</tr>
		@if ( !empty($mhs->id_konsentrasi) )
			<tr>
				<td>Konsentrasi</td>
				<td>: {{ $mhs->konsentrasi->nm_konsentrasi }}</td>
			</tr>
		@endif
		<tr>
			<td>No. HP</td>
			<td>: {{ $mhs->mhs->hp }}</td>
		</tr>
		<tr>
			<td>Nama Dosen Pembimbing</td>
			<td>: <b>{{ Sia::namaDosen($dsn->gelar_depan, $dsn->nm_dosen, $dsn->gelar_belakang) }}</b></td>
		</tr>
		<tr>
			<td>No. HP</td>
			<td>: {{ $dsn->hp }}</td>
		</tr>
		<tr>
			<td>Judul {{ Sia::sessionMhs('prodi') == 61101 ? 'Tesis':'Skripsi' }}</td>
			<td>{{ Request::get('jdl') }}</td>
		</tr>
	</table>

	<br>

	<table border="1" width="100%" class="table">
	    <thead class="custom">
	        <tr>
	            <th width="20px" valign="middle">No.</th>
	            <th width="120">Tgl. Konsultasi</th>
	            <th>Sub Pokok Bahasan</th>
	            <th>Saran</th>
	            <th width="80">Paraf</th>
	        </tr>
	    </thead>

	    <tbody align="center">

	        @foreach( $bimbingan as $bim )
	            <tr>
	                <td>{{ $loop->iteration }}</td>
	                <td>{{ Rmt::tgl_indo($bim->tgl_bimbingan) }}</td>
	                <td align="left">{{ $bim->sub_bahasan }}</td>
	                <td align="left">{!! $bim->komentar !!}</td>
	                <td></td>
	            </tr>
	        @endforeach
	    </tbody>
	</table>

    <table style="width:100%;margin-top: 30px" border="0">

    	<tr>
    		<td width="55%"></td>
    		<td>
    			Mengetahui
    			<br>
    			<br>
    			<b>{{ $jabatan }},</b>
    			<br>
    			<br>
    			<br>
    			<br>
    			<br>
    			<b>{{ Sia::namaDosen($dsn->gelar_depan, $dsn->nm_dosen, $dsn->gelar_belakang) }}</b>
    		</td>
    	</tr>
    </table>
</div>
</body>
</html>