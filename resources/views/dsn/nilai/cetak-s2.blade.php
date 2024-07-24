<!DOCTYPE html>
<html>
<head>
	<title>Cetak Nilai</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<style>

		body {
			margin: 10mm 10mm 10mm 10mm;
		}
		.hidden{
	    display:none;
		}

		.border-top {
			border-top: 1px dotted #999
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
            .footer{
                page-break-inside: avoid;
            }
            .bg-ganjil {
                background-color: #eee;
            }
        }


	</style>
</head>
<body onload="window.print()">

    @include('dsn.kop-laporan')

	<center>
		<h3><b>NILAI {{ $r->jenis == 1 ? 'PERKULIAHAN' : 'SEMESTER PENDEK' }}</b></h3>
	</center>

    <?php

        $dosen_ajar = DB::table('dosen_mengajar as dm')
                    ->leftJoin('dosen as d', 'dm.id_dosen', 'd.id')
                    ->select('d.nm_dosen','d.gelar_depan', 'd.gelar_belakang')
                    ->where('dm.id_jdk', $r->id)
                    ->get();

        $jml_dosen = DB::table('dosen_mengajar')
                        ->where('id_jdk', $r->id)
                        ->count();
    ?>

	<br>
	<table border="0" style="width: 100%" id="tbl">
        <tr>
            <td width="120">Matakuliah</td>
            <td>: {{ $r->nm_mk }}</td>
        </tr>

        <?php $i = 1; ?>
        

        @foreach( $dosen_ajar as $da )
    		<tr>
    			<td width="120"><?= $jml_dosen == 1 ? 'Nama Dosen' : 'Nama Dosen '.$i++ ?></td>
    			<td>: {{ Sia::namaDosen($da->gelar_depan, $da->nm_dosen, $da->gelar_belakang) }}</td>
    		</tr>
        @endforeach

		<tr>
			<td>Kelas/Ruangan</td>
			<td>: {{ $r->kode_kls.' / '.$r->nm_ruangan }}</td>
		</tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{ $r->jenjang.' '.$r->nm_prodi }}</td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td>
            <td>: {{ $r->nm_smt }}</td>
        </tr>
	</table>

	<br>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th width="20px">No.</th>
                <th width="120">NIM</th>
                <th style="text-align: left">Nama</th>
                @if ( $jml_dosen == 2 )
                    <th>Mid</th>
                    <th>Final</th>
                    <th>AVG</th>
                    <th>Nilai</th>
                @else
                    <th>Nilai Angka</th>
                    <th>Nilai Huruf</th>
                @endif
            </tr>
        </thead>
        <tbody align="center">

            @foreach( $peserta as $ps )
                <tr<?= $loop->iteration % 2 != 0 ? ' class="bg-ganjil"':'' ?>>
                    <td>{{ $loop->iteration }}</td>
                    <td align="left">{{ $ps->nim }}</td>
                    <td align="left">{{ $ps->nm_mhs }}</td>
                    @if ( $jml_dosen == 2 )
                        <td>{{ $ps->nil_mid }}</td>
                        <td>{{ $ps->nil_final }}</td>
                        <td>{{ $ps->nilai_angka }}</td>
                        <td>{{ $ps->nilai_huruf }}</td>
                    @else
                        <td>{{ $ps->nil_final }}</td>
                        <td>{{ $ps->nilai_huruf }}</td>
                    @endif
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
                <td></td>
                <td width="300" align="center">Dosen Matakuliah</td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr><td></td><td align="center">{{ Sia::sessionDsn('nama') }}</td></tr>
            <tr><td colspan="2"></td></tr>
        </table>
    </div>

</body>
</html>