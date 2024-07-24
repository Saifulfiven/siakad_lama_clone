<!DOCTYPE html>
<html>
<head>
    <title>Saran perbaikan</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 0mm 15mm;
            font-size: 14px;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

        .table-saran tr td {
            padding: 15px;
            border-top: 1px dashed #999;
        }

        footer {
            page-break-after: always;
        }

    </style>
</head>
<body onload="window.print()">

<?php $ujian = Request::get('jenis') == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL PENELITIAN'; ?>
<?php $ujian = Request::get('jenis') == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian ?>

<div class="kontainer">

    <center>
        <h2><b>SARAN-SARAN PERBAIKAN UJIAN {{ $ujian }}</b>
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
            <td valign="top">Judul Tesis</td>
            <td valign="top">:</td>
            <td valign="top">{!! $skripsi->judul_tmp !!}</td>
        </tr>
    </table>

    <br>
    <br>
    <br>

    <table width="100%" class="table-saran">

        @for( $i = 1; $i <= 17; $i++ )
            <tr>
                <td></td>
            </tr>
        @endfor
    </table>
    <br>
    <br>

    <table width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}<br>
                <b>Penilai</b>
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br></td></tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>....................................</b></td>
        </tr>
    </table>
</div>
<footer></footer>
</body>
</html>