<!DOCTYPE html>
<html>
<head>
	<title>Cetak Hasil Kuesioner</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>HASIL KUESIONER TA. {{ $ta->nm_smt }}<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0">
		<tr>
			<td>Program Studi</td>
			<td>: {{ $prodi->jenjang.' '.$prodi->nm_prodi }}</td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>: {{ Session::get('kues_jenis') }}</td>
		</tr>
	</table>
	<br>

	<table border="1" width="100%" style="font-size: 11px">
		<tr>
			<th>No.</th>
			<th>Matakuliah</th>
			<th>Kelas /<br>Ruang</th>
			<th>Program Studi</th>
			<th>Dosen</th>
			<th>Skor</th>
			<th>Grade</th>
		</tr>
		<?php $identifier = '' ?>
		<?php $no = 1; ?>

		@foreach($kuesioner as $r)
			<?php

				$grade = DB::table('kues_hasil as kh')
						->leftJoin('kues as k', 'kh.id_kues', 'k.id')
						->where('k.id_jdk', $r->id)
						->where('kh.penilaian','<>', 0)
						->where('k.id_dosen', $r->id_dosen)
						->sum('kh.penilaian');
				$count_hasil = DB::table('kues_hasil as kh')
						->leftJoin('kues as k', 'kh.id_kues', 'k.id')
						->where('k.id_jdk', $r->id)
						->where('kh.penilaian','<>', 0)
						->where('k.id_dosen', $r->id_dosen)
						->count();
				$dosen = Sia::namaDosen($r->gelar_depan, $r->nm_dosen, $r->gelar_belakang);
				
				if ( $identifier == $dosen.$r->kode_kls ) continue;

				$identifier = $dosen.$r->kode_kls;

				$skor = !empty($grade) && !empty($count_hasil) ? $grade/$count_hasil : 0;
			?>
			<tr>
				<td align="center">{{ $no++ }}</td>
				<td align="left">
					{{ strtoupper($r->nm_mk) }}
				</td>
				<td align="center">{{ $r->kode_kls }} / {{ $r->ruangan }}</td>
				<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
				<td align="left"><?= $dosen ?></td>
				<td align="center">{{ round($skor,2) }}</td>
				<td>{{ Sia::kuesionerGrade($skor) }}</td>
			</tr>
		@endforeach
	</table>
	<footer></footer>
</div>

</body>
</html>