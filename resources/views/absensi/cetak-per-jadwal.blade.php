<!DOCTYPE html>
<html>
<head>
	<title>Cetak Absensi</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>
		.hidden{
	    display:none;
		}
	</style>
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>REKAP KEHADIRAN MAHASISWA PER JADWAL<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0">
        <tr>
            <th>Nama</th><td>: {{ $mhs->nm_mhs }}</td>
        </tr>
        <tr>
            <th width="130px">NIM</th>
            <td>:{{ $mhs->nim }}
            </td>
        </tr>
        <tr>
            <th width="130px">Tahun Akademik</th>
            <td>: {{ $ta }}</td>
        </tr>
        <tr>
            <th>Angkatan</th><td>: {{ substr($mhs->nim,0,4) }}</td>
        </tr>
        <tr>
            <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
        </tr>
	</table>
	
	<br>

	<table border="1" class="table" width="100%" id="tbl">
		<thead>
			<tr>
				<th>HARI</th>
				<th>JAM</th>
				<th>KDMK</th>
				<th>MATAKULIAH</th>
				<th>SKS</th>
				<th>SMT</th>
				<th>KELAS</th>
				<th>RUANGAN</th>
				<th>NAMA DOSEN</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $jadwal as $r )
				<tr>
					<td align="center">{{ Rmt::hari($r->hari) }}</td>
					<td align="center">{{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}</td>
					<td align="center">{{ $r->kode_mk }}</td>
					<td>{{ $r->nm_mk }}</td>
					<td align="center">{{ $r->sks_mk }}</td>
					<td align="center">{{ $r->smt }}</td>
					<td align="center">{{ $r->kode_kls }}</td>
					<td align="center">{{ $r->nm_ruangan }}</td>
					<td align="left">{{ $r->dosen }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<footer></footer>
</div>

<script>
	function MergeCommonRows(table) {
	    var firstColumnBrakes = [];
	    var jmlColum = table.find('th').length;
	    for(var i=1; i<= 2; i++){
	        var previous = null, cellToExtend = null, rowspan = 1;
	        table.find("td:nth-child(" + i + ")").each(function(index, e){
	            var jthis = $(this), content = jthis.text();
	            if (previous == content && content !== "" && $.inArray(index, firstColumnBrakes) === -1) {
	                jthis.addClass('hidden');
	                cellToExtend.attr("rowspan", (rowspan = rowspan+1));
	            }else{
	                if(i === 1) firstColumnBrakes.push(index);
	                rowspan = 1;
	                previous = content;
	                cellToExtend = jthis;
	            }
	        });
	    }
	    $('td.hidden').remove();
	}

	MergeCommonRows($('#tbl'));

</script>
</body>
</html>