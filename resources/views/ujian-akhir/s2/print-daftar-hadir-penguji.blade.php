<!DOCTYPE html>
<html>
<head>
    <title>Daftar hadir penguji</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 0mm 15mm;
            font-size: 14px;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

        .table-penguji {
            font-size: 16px;
        }
        .table-penguji th {
            padding: 15px;
        }
        .table-penguji td {
            padding: 10px;
        }

        footer {
            page-break-after: always;
        }

    </style>
</head>
<body onload="window.print()">

<?php $ujian = Request::get('jenis') == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL'; ?>
<?php $ujian = Request::get('jenis') == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian ?>

<div class="kontainer">

    <center>
        <h2><b>DAFTAR HADIR DOSEN PENGUJI {{ $ujian }}</b>
        </h2>
    </center>
    <br>

    <table border="0" style="width: 100%" id="tbl">
        <tr>
            <td width="120">Nama</td>
            <td>:</td>
            <td>{{ $mhs->nm_mhs }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $mhs->nim }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>Magister Manajemen</td>
        </tr>
        <tr>
            <td>Program</td>
            <td>:</td>
            <td>Pascasarjana {{ config('app.itb_long') }}</td>
        </tr>
        <tr>
            <td>Pembimbing I</td>
            <td>:</td>
            <td>{{ $ketua->nm_dosen }}</td>
        </tr>
        <tr>
            <td>Pembimbing II</td>
            <td>:</td>
            <td>{{ $sekretaris->nm_dosen }}</td>
        </tr>

        <tr>
            <td valign="top">Judul Tesis</td>
            <td valign="top">:</td>
            <td valign="top">{!! $skripsi->judul_tmp !!}</td>
        </tr>
    </table>

    <br>

    <table border="1" width="100%" class="table-penguji">
        <tr>
            <th width="20">No</th>
            <th>Nama Dosen</th>
            <th>Jabatan</th>
            <th>Tanda Tangan</th>
        </tr>
        @foreach( $penguji as $pe )
            <?php $jabatan = str_replace('ANGGOTA', 'PENGUJI ', $pe->jabatan); ?>
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td>{{ $pe->penguji }}</td>
                <td align="center">{{ trim($jabatan) == 'PENGUJI' ? 'PENGUJI 1' : $jabatan }}</td>
                <td></td>
            </tr>
        @endforeach
    </table>
    <br>
    <br>

    <table width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}<br>
                <b>Ketua Penguji</b>
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br></td></tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>{{ $ketua->nm_dosen }}</b></td>
        </tr>
    </table>
</div>
<footer></footer>
</body>
</html>