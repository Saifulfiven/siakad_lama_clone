<!DOCTYPE html>
<html>
<head>
	<title>Cetak Jadwal Kuliah</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>
		.hidden{
	    	display:none;
		}
		@page {
			size: landscape;
		}
		body {
        	margin: 0 5mm 40mm 10mm;
        }
		@media print  
        {
        	/*table tr.page-break {
        		page-break-after: always !important;
        	}*/
   			/*tbody tr td{ page-break-inside: avoid; }*/
        }
	</style>
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4><b>SEKOLAH TINGGI ILMU EKONOMI (STIE)</b></h4>
	<h2><b>NOBEL INDONESIA</b></h2>
	<h4>PROGRAM STUDI MANAJEMEN (S2)</h4>

	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<?php
		$smt = DB::table('semester')
				->whereIn('id_smt', Session::get('jdk_smt'))
				->first();

		$jadwal = DB::table('jadwal_pertemuan_s2 as jp')
					->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'jp.id_jdk')
					->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
					->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
					->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
					->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
					->select('jdk.id','jp.tgl','jp.jam','jp.pertemuan_ke','r.nm_ruangan','mk.kode_mk','mk.nm_mk','mkur.smt')
					->whereIn('jdk.id_smt', Session::get('jdk_smt'))
					->whereIn('jdk.id_prodi', Sia::getProdiUser())
					->where('jdk.kode_kls', Request::get('kelas'))
					->orderBy('jp.tgl')
					->orderBy('jp.jam')
					->get();
	?>
	<center>
	<h4><strong>JADWAL PERKULIAHAN PROGRAM STUDI MANAJEMEN KELAS {{ Request::get('kelas') }}<br>
			PERIODE {{ $smt->nm_smt }}</strong></h4>
	
	<br>
	
	<table border="1" class="table" width="100%" id="tbl">
		<thead>
			<tr>
				<th>Hari/Tanggal</th>
				<th>Jam </th>
				<th>Kode Matakuliah</th>
				<th>Matakuliah</th>
				<th>SMT</th>
				<th>Dosen</th>
				<th>Ruangan</th>
			</tr>
		</thead>
		<tbody>
			<?php $no = 1 ?>
			@for( $i = 1; $i <= 4; $i++ )
			@foreach( $jadwal as $r )
				<?php
					$dosen = DB::table('dosen_mengajar as dm')
								->leftJoin('dosen as d', 'dm.id_dosen', 'd.id')
								->select('d.gelar_depan', 'd.nm_dosen', 'd.gelar_belakang')
								->where('dm.id_jdk', $r->id);

					$count_pertemuan = DB::table('jadwal_pertemuan_s2')
										->where('id_jdk', $r->id)->count();

					$bagi_pertemuan = round($count_pertemuan/2);

					if ( $r->pertemuan_ke <= $bagi_pertemuan ) {
						$dosen->orderBy('dm.id','asc');
					} else {
						$dosen->orderBy('dm.id', 'desc');
					}
					$dsn = $dosen->first();

					$dm = $dsn->gelar_depan.' '.$dsn->nm_dosen.', '.$dsn->gelar_belakang;
				?>
				<tr>
					<td align="center"><?= $r->tgl == '0000-00-00' ? '-' : Rmt::hari(Rmt::formatTgl($r->tgl, 'N')).'<br>'.Rmt::formatTgl($r->tgl, 'd/m/Y') ?></td>
					<td align="center">{{ $r->jam }}</td>
					<td align="center">{{ $r->kode_mk }}</td>
					<td>{{ $r->nm_mk }}</td>
					<td align="center">{{ $r->smt }}</td>
					<td align="left">{{ $dm }}</td>
					<td align="center">{{ $r->nm_ruangan }}</td>
				</tr>
				<?php $no++ ?>
			@endforeach
			@endfor
		</tbody>
	</table>

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
	                cellToExtend.attr("class", 'rowspan');
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