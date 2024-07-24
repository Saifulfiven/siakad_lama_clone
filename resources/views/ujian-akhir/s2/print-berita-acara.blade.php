@if ( empty($skripsi->tgl_ujian) )
    <center><h3>Masukkan tanggal ujian terlebih dahulu</h3></center>
    <?php exit; ?>
@endif
<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Seminar</title>

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

<?php $ujian = Request::get('jenis') == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL PENELITIAN'; ?>
<?php $ujian = Request::get('jenis') == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian ?>

<div class="kontainer">

    <center>
        <h2><b>BERITA ACARA UJIAN {{ $ujian }}</b>
        </h2>
    </center>

    <br>
    <p style="line-height: 1.5rem">Pada hari ini, {{ Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')) }} Tanggal {{ Rmt::tgl_indo($skripsi->tgl_ujian) }} bertempat di ruang Ujian Program Pascasarjana <b>{{ config('app.itb_long') }}</b>, telah dilaksanakan <b>UJIAN {{ $ujian }}</b> dari mahasiswa :</p>

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
            <td>{{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
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


    <p style="line-height: 1.5rem;">Berdasarkan hasil ujian, maka Mahasiswa tersebut dinyatakan <b>LULUS/TIDAK LULUS *)</b> UJIAN {{ $ujian }} dengan nilai akhir ............ dan huruf mutu ............, serta Indeks Prestasi Kumulatif (IPK) ............... dengan predikat: <b>tidak memuaskan/memuaskan/sangat memuaskan/kummlaude*</b></p> 
    

    <p><b>Tim penguji terdiri atas:</b></p>

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
    <p>Tanggal Lulus: </p>
    <br>
    <br>

    <table width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}<br>
                <b>Ketua TIM Penguji</b>
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