<!DOCTYPE html>
<html>
<head>
	<title>Cetak Absen Mahasiswa</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<style>

		body {
			margin: 0mm 10mm 0mm 10mm;
		}

		.border-top {
			border-top: 1px dotted #999
		}

		table {
			font-size: 12px;
		}

        @media print  
        {
            .footer{
                page-break-inside: avoid;
            }
        }


	</style>
</head>
<body onload="window.print()">

    @include('layouts.kop-s1')
    <hr>

	<center>
		<h3><b>ABSENSI {{ $r->jenis == 1 ? 'PERKULIAHAN' : 'SEMESTER PENDEK' }}</b></h3>
	</center>

	<br>
    <table border="0" style="width: 100%" id="tbl">
        <tr>
            <td width="120">Matakuliah</td>
            <td>: {{ $r->nm_mk }}</td>
        </tr>
        <tr>
            <td width="120">Nama Dosen</td>
            <td>: {{ Sia::sessionDsn('nama') }}</td>
        </tr>
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
                <th rowspan="2" class="text-center" style="width: 5px">NO</th>
                <th rowspan="2" class="text-center" style="width: 40px">NIM</th>
                <th rowspan="2" width="300">NAMA</th>
                <th colspan="16" class="text-center" style="padding:1px">Pertemuan</th>
            </tr>
            <tr>
                <?php $setengah = $jml_pertemuan/2; ?>

                <?php for ( $per = 1; $per <= $setengah; $per++ ) { ?>
                    <th style="padding:1px;font-size:11px" class="text-center" width="40">
                        <?= $per ?>
                    </th>
                <?php } ?>
                <th width="40">%</th>
                <?php for ( $per = $setengah+1; $per <= $jml_pertemuan; $per++ ) { ?>
                    <th style="padding:1px;font-size:11px" class="text-center" width="40">
                        <?= $per ?>
                    </th>
                <?php } ?>
                <th width="40">%</th>
            </tr>
        
        </thead>
        <tbody align="center">

            @foreach( $peserta as $r )

              <tr>
                <td><?= $loop->iteration ?></td>
                <td><?= $r->nim ?></td>
                <td align="left"><?= trim(ucwords(strtolower($r->nm_mhs))) ?></td>
                <?php
                $absen = [
                    $r->a_1,$r->a_2,$r->a_3,$r->a_4,$r->a_5,
                    $r->a_6,$r->a_7,$r->a_8,$r->a_9,$r->a_10,
                    $r->a_11,$r->a_12,$r->a_13,$r->a_14
                ];

                $persen = 0;

                for ( $per = 1; $per <= $setengah; $per++ ) { ?>
                    <td style='text-align: center !important;'>
                        <?= $absen[$per-1] == 1 ? '&#10004;':'' ?>
                    </td>
                    <?php $persen += $absen[$per-1]; ?>
                <?php } ?>

                <td><?= round($persen/$setengah * 100) ?></td>

                <?php $persen = 0; ?>

                <?php for ( $per = $setengah+1; $per <= $jml_pertemuan; $per++ ) { ?>
                    <td style='text-align: center !important;'>
                        <?= $absen[$per-1] == 1 ? '&#10004;':'' ?>
                    </td>
                    <?php $persen += $absen[$per-1]; ?>
                <?php } ?>
                
                <td><?= round($persen/$setengah * 100) ?></td>
              </tr>

            @endforeach
        </tbody>
    </table>

    <br>

    <div class="footer">
        <table width="100%" border="0">
            <tr>
                <td></td>
                <td width="300" align="center">Dosen Matakuliah</td>
            </tr>
            <tr>
                <td colspan="2"><br><br><br><br><br></td>
            </tr>
            <tr><td></td><td align="center">{{ Sia::sessionDsn('nama') }}</td></tr>
        </table>
    </div>

</body>
</html>