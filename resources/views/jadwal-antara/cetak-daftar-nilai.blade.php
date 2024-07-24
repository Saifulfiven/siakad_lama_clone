<!DOCTYPE html>
<html>
<head>
	<title>Cetak Daftar Nilai SP</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
	<style>

		body {
			margin: 0mm 5mm 5mm 5mm;
            font-size: 13px;
		}

		.kontainer {
			margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
		}

		table {
			font-size: 12px;
		}

        table.ket tr, table.ket td, table.ket th {
            border: 1px solid #eee;
            border-collapse: collapse;
            width: 200px !important;
        }

		@media print  
        {
            @page {

                margin: 10mm 10mm 10mm 10mm;
                width: 33cm;
            }
            tr{
                page-break-inside: avoid;
            }
            .footer{
                page-break-inside: avoid;
            }
        }
	</style>
</head>
<body onload="window.print()">

<?php $no = 1 ?>

@foreach( $dosen as $val )
<div class="kontainer">
	<center>
	<h2><b>DAFTAR NILAI MAHASISWA</b></h2>
	</center>
	<br>

	<table border="0">
		<tr>
			<td width="100">Program Studi</td>
			<td width="300">: {{ $r->jenjang.' '.$r->nm_prodi }}</td>

			<td width="100">Kelas</td>
			<td>: {{ $r->kode_kls }}</td>
		</tr>
		<tr>
			<td>Dosen</td>
			<td>: {{ $val->gelar_depan.' '.$val->nm_dosen.', '.$val->gelar_belakang }}</td>

			<td>Ruangan</td>
			<td>: {{ $r->ruangan }}</td>
		</tr>
		<tr>
			<td>Hari/Jam</td>
			<td>: {{ Rmt::hari($r->hari) }}, {{ substr($r->jam_masuk,0,5).' - '.substr($r->jam_keluar,0,5) }}</td>

			<td>Semester</td>
			<td>: {{ $r->smt }}</td>
		</tr>
		<tr>
			<td>Matakuliah</td>
			<td>: {{ $r->nm_mk }}</td>

			<td>Jumlah Peserta</td>
			<td>: {{ empty($r->terisi) ? '':$r->terisi }}</td>
		</tr>

	</table>
	
	<br>

	<?php 
		$start = 7;
		$jml_pertemuan = 12;
	?>

	<table border="1" class="table" width="100%" id="tbl">
		<thead>
			<tr>
				<th width="20px">No.</th>
                <th width="120">NIM</th>
                <th style="text-align: left">Nama</th>
                <th>Tugas/Kuis (40%)</th>
                <th>UTS (30%)</th>
                <th>UAS (30%)</th>
                <th>Total Nilai</th>
                <th>Nilai Huruf</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $mahasiswa as $res )
				<tr>
					<td align="center">{{ $loop->iteration }}</td>
					<td align="center">{{ $res->nim }}</td>
					<td>{{ $res->nm_mhs }}</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<br>

    <div class="footer">
        <table width="100%" border="0">
            <tr>
                <td rowspan="4">
                    <table class="ket">
                        <tr><th colspan="2">Skala Nilai</th>
                        <tr>
                            <th>Nilai Huruf</th>
                            <th>Rentang</th>
                        </tr>
                        <?php
                            $skala = DB::table('skala_nilai')
                                        ->where('id_prodi',$r->id_prodi)
                                        ->where('nilai_huruf','<>', 'T')
                                        ->orderBy('range_atas', 'desc')->get() ?>
                        @foreach( $skala as $sk )
                            <tr>
                                <td style="text-align: center">{{ $sk->nilai_huruf }}</td>
                                <td style="text-align: center">{{ $sk->range_nilai }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td width="20%"></td>
                <td width="300" align="center">
                	Makassar, {{ Rmt::tgl_indo(date('Y-m-d')) }}<br>
                	Dosen Matakuliah</td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr><td></td><td align="center">{{ $val->gelar_depan.' '.$val->nm_dosen.', '.$val->gelar_belakang }}</td></tr>
            <tr><td colspan="2"></td></tr>
        </table>
    </div>
</div>

	<?php $no++ ?>
@endforeach
</body>
</html>