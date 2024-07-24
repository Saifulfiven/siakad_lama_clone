<!DOCTYPE html>
<html>
<head>
	<title>Cetak Hasil Kuesioner</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />

	<style type="text/css">
		body {
			font-size: 14px !important;
			margin-left: 20mm;
			margin-right: 10mm;
		}
	</style>
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>HASIL EVALUASI DOSEN {{ Request::get('jenis') == 'MID' ? 'TENGAH SEMESTER': 'AKHIR SEMESTER' }} TA. {{ $ta->nm_smt }}<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0">
		<tr>
			<td>Nama Dosen</td>
			<td>: {{ Request::get('dosen') }}</td>
		</tr>
		<tr>
			<td>Matakuliah</td>
			<td>: {{ Request::get('matakuliah') }}</td>
		</tr>
		<tr>
			<td>Kelas / Ruangan</td>
			<td>: {{ Request::get('kelas') }} / {{ Request::get('ruangan') }}</td>
		</tr>
		<tr>
			<td>Program Studi</td>
			<td>: {{ Request::get('prodi') }}</td>
		</tr>
	</table>
	<br>
	<?php
	    $no = 1;
        $jml_hasil = 0;
        $total_nilai = 0;
    ?>
	<table border="1" width="100%" class="table table-bordered">
    	<?php foreach( $komponen as $ko ) { ?>
    		<thead class="custom">
            	<tr>
            		<th width="20">No</th>
            		<th style="text-align: left">
            			<?= $ko->judul == 'blank' ? 'KRITERIA' : $ko->judul ?>
            		</th>
            		<th>Total Skor</th>
            		<th>Grade</th>
            	</tr>
            </thead>
                <?php
                $isi = DB::table('kues as k')
	    				->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
	    				->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
	    				->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
	    				->select('kki.id','kki.pertanyaan','kh.penilaian')
	    				->where('k.id_jdk', Request::get('id_jdk'))
	    				->where('k.id_dosen', Request::get('id_dosen'))
	    				->where('kk.id', $ko->id)
	    				->where('kh.penilaian','<>', 0)
	    				->groupBy('kki.id')
	    				->get();
	    		
	    		$subtot_nilai = 0;
	    		$no2 = 1;

            	foreach( $isi as $is ) { 

            		$nilai = DB::table('kues as k')
	    				->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
	    				->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
	    				->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
	    				->select('kki.pertanyaan','kh.penilaian')
	    				->where('k.id_jdk', Request::get('id_jdk'))
	    				->where('k.id_dosen', Request::get('id_dosen'))
	    				->where('kki.id', $is->id)
	    				->where('kh.penilaian','<>', 0);

	    			$sum_nilai = $nilai->sum('kh.penilaian');
	    			$count_nilai = $nilai->count();
            		?>

            		<?php $jml_hasil += $count_nilai; ?>
            		<?php $grade_1 = !empty($count_nilai) ? $sum_nilai / $count_nilai : 0 ?>

                	<tr>
                		<td style="text-align: center"><?= $no2++ ?></td>
                		<td><?= $is->pertanyaan ?></td>
                		<td width="80" align="center"><?= $sum_nilai ?></td>
                		<td width="40" align="center"><?= round($grade_1, 2) ?></td>
                	</tr>
                	<?php $subtot_nilai += $sum_nilai; ?>
                
                <?php } ?>
                <?php $total_nilai += $subtot_nilai; ?>
        <?php } ?>
    </table>

    <table>
    	<tr>
        	<td width="150"><b>TOTAL SKOR</b></td>
        	<td>: <b><?= $total_nilai ?></b></td>
        </tr>
        <tr>
        	<td><b>GRADE</b></td>
        	<td>: <b>
        		<?= round($total_nilai / $jml_hasil, 1) ?> 
        		(<?= strtoupper(Sia::kuesionerGrade($total_nilai / $jml_hasil)) ?>)
        		</b>
        	</td>
        </tr>
    </table>

    <div style="padding-top: 20px">
    	<h4><b><u>Komentar atau Saran untuk Dosen</u></b></h4>
    	<?php
    		$jml = 0;
    		$responden = DB::table('kues')
    					->where('id_jdk', Request::get('id_jdk'))
	    				->where('id_dosen', Request::get('id_dosen'))
	    				->get();
	    	$no3 = 1; ?>
	    	<?php foreach( $responden as $res ) { 

	    		$komen = DB::table('kues as k')
	    				->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
	    				->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
	    				->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
	    				->select('kk.id_prodi','kki.pertanyaan','kh.penilaian_text')
	    				->where('k.id_jdk', Request::get('id_jdk'))
	    				->where('k.id_dosen', Request::get('id_dosen'))
	    				->where('k.id', $res->id)
	    				->where('kh.penilaian', 0)
	    				->where('kh.penilaian_text','<>','')
	    				->get(); ?>
	    			<ol style="padding-left: 15px">
	    				<?php foreach( $komen as $kom ) { ?>
	    					<?php if (!empty(trim($kom->penilaian_text))) {
	    						$jml += 1;
	    					} ?>
	    					@if ( $kom->id_prodi == 61101 )
			    				<li><b><?= $kom->pertanyaan ?></b><br>
	                				<?= $kom->penilaian_text ?>
	                			</li>
	                		@else
	                			<li><?= $kom->penilaian_text ?></li>
	                		@endif
                		<?php } ?>
            		</ol>
           	<?php } ?>
		
		@if ( $jml == 0 )
			<small>Tidak ada komentar</small>
		@endif           
    </div>
	<footer></footer>
</div>

<table width="100%">
    <tr>
        <td>
        	<table border="1">
        		<!-- <tr><th colspan="2">Keterangan</th> -->
        		<tr>
        			<th>Skor</th>
        			<th>Keterangan</th>
        		</tr>
        		<tr>
	        		<td>4,5 - 5</td>
	        		<td>Sangat Baik</td>
	        	</tr>
	        	<tr>
	        		<td>3,5 - 4,4</td>
	        		<td>Baik</td>
	        	</tr>
	        	<tr>
	        		<td>2,5 - 3,4</td>
	        		<td>Cukup</td>
	        	</tr>
	        	<tr>
	        		<td>1,5 - 2,4</td>
	        		<td>Kurang</td>
	        	</tr>
	        	<tr>
	        		<td>< 1,5</td>
	        		<td>Tidak baik</td>
	        	</tr>
	        </table>
        </td>
        <td width="38%">&nbsp;</td>
        <td style="text-align: center">
            Makassar, {{ Rmt::tgl_indo(Carbon::now()->format('Y-m-d')) }}<br>
            Wakil Ketua Bid. Akademik
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <b>{{ Sia::option('ketua_1') }}</b>
        </td>
    </tr>
</table>

</body>
</html>