<!DOCTYPE html>
<html>
<head>
	<title>Cetak Berita acara perkuliahan</title>

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
		<h3><b>BERITA ACARA {{ $r->jenis == 1 ? 'PERKULIAHAN' : 'PERKULIAHAN SEMESTER PENDEK' }}</b></h3>
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

    <table border="1" class="table" width="100%" id="tbl">
        <thead>
            <tr>
                <th rowspan="2" width="10">No</th>
                <th rowspan="2" width="80">Hari/Tanggal</th>
                <th rowspan="2" width="80">Jam</th>
                <th rowspan="2">Pokok Bahasan</th>
                <th colspan="2">Jml Mahasiswa</th>
                <th colspan="2">Tanda Tangan</th>
            </tr>
            <tr>
                <th>Hadir</th>
                <th>Absen</th>
                <th>Dosen</th>
                <th>MHS</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;

            $hadir = DB::select("
                    SELECT SUM(a_1) as a_1, SUM(a_2) as a_2, SUM(a_3) as a_3, SUM(a_4) as a_4, SUM(a_5) as a_5, SUM(a_6) as a_6, SUM(a_7) as a_7, SUM(a_8) as a_8, SUM(a_9) as a_9, SUM(a_10) as a_10, SUM(a_11) as a_11, SUM(a_12) as a_12, SUM(a_13) as a_13, SUM(a_14) as a_14
                    FROM nilai where id_jdk = '$r->id'
                ");
            $hadir = json_decode(json_encode($hadir[0]), true); ?>

            @foreach( $absen as $ab )
                <?php if ( $r->jenis == 2 && $no++ > 10 ) continue; ?>
                <?php $nomor = $loop->iteration ?>
                <tr>
                    <td align="center">{{ $nomor }}</td>
                    <td align="center">{{ !empty($ab->tgl) ? Carbon::parse($ab->tgl)->format('d/m/Y') : '' }}</td>
                    <td align="center">{{ !empty($ab->tgl) ? $ab->jam_masuk.' - '.$ab->jam_keluar : '' }}</td>
                    <td><?= nl2br($ab->pokok_bahasan) ?></td>
                    <td align="center">{{ $hadir['a_'.$nomor] }}</td>
                    <td align="center">{{ Request::get('pst') - $hadir['a_'.$nomor] }}</td>
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
                <td>Mengetahui,<br> Ketua Program Studi</td>
                <td width="300" align="center">Dosen Matakuliah</td>
            </tr>
            <tr>
                <td colspan="2"><br><br><br><br><br></td>
            </tr>
            <?php
                $prodi = DB::table('prodi')->where('id_prodi', $r->id_prodi)->first();
            ?>
            <tr><td>{{ $prodi->ketua_prodi }}</td>
                <td align="center">{{ Sia::sessionDsn('nama') }}</td></tr>
        </table>
    </div>

</body>
</html>