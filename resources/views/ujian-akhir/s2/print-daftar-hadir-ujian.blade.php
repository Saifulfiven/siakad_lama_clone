<!DOCTYPE html>
<html>
<head>
    <title>Daftar hadir ujian</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        @page {
            size: auto;
            margin: 20mm 20mm 30mm 20mm;
        }

        body {
            font-size: 14px;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

        .table-penguji {
            font-size: 16px;
        }
        .table-penguji th {
            padding: 10px;
        }
        .table-penguji td {
            padding: 10px;
        }

        table { page-break-inside:auto }
        tr    { page-break-inside:avoid; page-break-after:auto }

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
        <h2><b>DAFTAR HADIR UJIAN {{ $ujian }}</b>
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
        <tr>
            <td colspan="2"></td>
        </tr>

        <tr>
            <td>Hari/Tanggal</td>
            <td>:</td>
            <td>{{ ucfirst(strtolower(Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')))) }}, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}</td>
        </tr>
        <tr>
            <td>Pukul</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td>Moderatori</td>
            <td>:</td>
            <td>Ketua Penguji</td>
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
        <?php $no = 1 ?>
        @foreach( $penguji as $pe )
            <?php $jabatan = str_replace('ANGGOTA', 'PENGUJI ', $pe->jabatan); ?>
            <tr>
                <td align="center">{{ $no++ }}</td>
                <td>{{ $pe->penguji }}</td>
                <td align="center">{{ trim($jabatan) == 'PENGUJI' ? 'PENGUJI 1' : $jabatan }}</td>
                <td></td>
            </tr>
        @endforeach

        @for( $i = $no; $i <= 12; $i++ )
            <tr>
                <td align="center">{{ $i }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor
    </table>

</div>

<footer></footer>

<div class="kontainer">

    <center>
        <h2><b>DAFTAR HADIR UJIAN {{ $ujian }}</b>
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
        <tr>
            <td colspan="2"></td>
        </tr>

        <tr>
            <td>Hari/Tanggal</td>
            <td>:</td>
            <td>{{ ucfirst(strtolower(Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')))) }}, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}</td>
        </tr>
        <tr>
            <td>Pukul</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td>Moderatori</td>
            <td>:</td>
            <td>Ketua Penguji</td>
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

        @for( $i = 13; $i <= 20; $i++ )
            <tr>
                <td align="center">{{ $i }}</td>
                <td></td>
                <td></td>
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
</body>
</html>