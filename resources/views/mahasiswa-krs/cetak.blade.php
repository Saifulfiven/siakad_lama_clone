<!DOCTYPE html>
<html>
<head>
	<title>Cetak KRS</title>

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
		}

		table.header {
			font-weight: bold;
		}

		.kontainer {
			max-width: 684px;
		}

		.border-top {
			border-top: 1px dotted #999
		}

		table {
			font-size: 12px;
		}

	</style>
</head>
<body>

<div class="kontainer">
	<table width="100%" border="0">
		<tr>
			<td width="100">
        {{-- <img width="100%" src="{{ url('resources') }}/assets/img/logo.jpg"> --}}
      </td>
			<td>
        {{-- <center>
					<h3><b>SEKOLAH TINGGI ILMU EKONOMI (STIE)<br>NOBEL INDONESIA</b></h3><br>
					{{ Sia::option('alamat_kampus') }}<br>
					{{ Sia::option('nomor') }}
				</center> --}}
			</td>
			<td width="100">
				{{-- <img width="100%" src="{{ url('storage') }}/qr-code/{{ $mhs->nim }}.svg"> --}}
			</td>
		<tr>
	</table>

	<center style="padding-top: 150px">
		<h3><b><u>KARTU RENCANA STUDI</u></b></h3>
	</center>
	<br>
	<table class="header">
		<tr>
			<td width="120">NIM</td>
			<td>: {{ $mhs->nim }}</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>: {{ $mhs->mhs->nm_mhs }}</td>
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
			<td>Semester</td>
			<td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai) }}</td>
		</tr>
		<tr>
			<td>Periode</td>
			<td>: {{ Request::get('nm_periode') }}</td>
		</tr>
	</table>

	<br>

	<table border="1" width="100%" class="table">
	    <thead class="custom">
	        <tr>
	            <th width="20px">No.</th>
	            <th>Kode matakuliah</th>
	            <th>Nama matakuliah</th>
	            <th align="center">SKS</th>
	        </tr>
	    </thead>

	    <tbody align="center">
	    	@if ( count($krs) == 0 )
	    		<tr>
	    			<td colspan="5">Belum ada KRS</td>
	    		</tr>
	    	@endif

	        <?php $total_sks = 0 ?>
	        @foreach( $krs as $k )
	            <tr>
	                <td>{{ $loop->iteration }}</td>
	                <td align="left">{{ $k->kode_mk }}</td>
	                <td align="left">{{ $k->nm_mk }}</td>
	                <td>{{ $k->sks_mk }}</td>
	            </tr>
	            <?php $total_sks += $k->sks_mk ?>
	        @endforeach
	        <tr>
	            <th colspan="3">Total SKS</th>
	            <th align="center">{{ $total_sks }}</th>
	        </tr>
	    </tbody>
	</table>

    <table style="width:100%" border="0">
    	<tr>
    		<td colspan="3" align="center">
    			<b>Silahkan menyetor berkas ini ke bagian akademik untuk dijadikan sebagai arsip</b>
    			<br><br><br>
    		</td>
    	</tr>
    	<tr>
    		<td colspan="3" align="right">
    			Makassar, {{ Rmt::tgl_indo(Carbon::today()) }}
    			<br><br>
    		</td>
    	</tr>
        <tr>
            <td align="center" style="padding-bottom: 75px;width: 33%">Ketua Program Studi</td>
            <td align="center" style="padding-bottom: 50px;width: 33%">Dosen PA</td>
            <td align="center" style="padding-bottom: 75px;width: 33%">Mahasiswa</td>
        </tr>

        <tr>
        	<td align="center">
        		<b><u>{{ $mhs->prodi->ketua_prodi }}</u></b>
        	</td>
        	<td align="center">
        		@if ( !empty($mhs->dosenWali) )
        			<b><u>{{ $mhs->dosenWali->gelar_depan }} {{ $mhs->dosenWali->nm_dosen }}, {{ $mhs->dosenWali->gelar_belakang }}</u></b>
        		@else
        			-
        		@endif
        	</td>
        	<td align="center">
        		<b><u>{{ $mhs->mhs->nm_mhs }}</u></b>
        	</td>
        </tr>
    </table>
</div>
</body>
</html>