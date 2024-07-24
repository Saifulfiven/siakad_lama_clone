<!DOCTYPE html>
<html>
<head>
	<title>Cetak Peserta</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>
		.hidden{
	    display:none;
		}
		@media print  
        {
            tr{
                page-break-inside: avoid;
            }
        }
	</style>
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>PESERTA KELAS<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>
	<table border="0" class="table table-striped">
        <tbody class="detail-mhs">

            <tr>
                <th width="160px" align="left">Semester</th>
                <td width="300px">: {{ $r->nm_smt }}</td>
                <th width="160px" align="left">Nama Kelas</th>
                <td>: {{ $r->kode_kls }}</td>
            </tr>
            <tr>
                <th align="left">Hari/Jam</th>
                <td>: 
                	@if ( !empty($r->jam_masuk) )
                		{{ Rmt::hari($r->hari) }} - {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}
                	@else
                		-
                	@endif
                </td>
                <th align="left">Ruangan</th>
                <td>: {{ $r->nm_ruangan }}</td>
            </tr>
            <tr>
                <th align="left">Program Studi</th>
                <td>: {{ $r->jenjang }} {{ $r->nm_prodi }}</td>
            </tr>
            <tr>
                <th align="left">Matakuliah</th>
                <td>: {{ $r->kode_mk }} - {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
	
	<br>
	
	<table border="1" class="table" width="100%" id="tbl">
		<thead>
			<tr>
				<th width="20px">No.</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Angkatan</th>
			</tr>
		</thead>
		<tbody>
			 @foreach( $peserta as $ps )
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td align="left">{{ $ps->nim }}</td>
                    <td align="left">{{ $ps->nm_mhs }}</td>
                    <td>{{ $ps->jenjang }} - {{ $ps->nm_prodi }}</td>
                    <td align="center">{{ substr($ps->semester_mulai, 0, 4) }}</td>
                </tr>
            @endforeach
		</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>