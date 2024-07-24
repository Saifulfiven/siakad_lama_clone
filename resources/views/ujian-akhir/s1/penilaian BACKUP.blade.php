<!DOCTYPE html>
<html>
<head>
    <title>Daftar Penilaian</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 5mm 10mm 0mm 10mm;
            font-size: 15px;
        }

        .kontainer p, table tr td, table tr th {
            font-size: 1rem;
        }

        footer {
            page-break-after: always;
        }

    </style>
</head>
<body onload="window.print()">

<?php

if ( empty($skripsi) ) {
    echo '<h2>Pastikan Tanggal dan Waktu seminar telah diinput.</h2>';
    exit;
}

?>

@foreach( $penguji as $p )

<div class="kontainer">
    
    <?php $jabatan = $p->jabatan == 'KETUA' || $p->jabatan == 'SEKRETARIS' ? 'Pembimbing' : 'Penguji' ?>
    <table width="100%" border="0" class="kop">
        <tr>
            <td width="100"><img width="100%" src="{{ url('resources') }}/assets/img/logo.jpg"></td>
            <td><center>
                    <h3><b>SEKOLAH TINGGI ILMU EKONOMI (STIE)<br>NOBEL INDONESIA</b></h3>
                    {{ Sia::option('alamat_kampus') }}<br>
                    {{ Sia::option('nomor') }}
                </center>
            </td>
            <td width="100">
                <img width="100%" src="{{ url('resources') }}/assets/img/nobel-qr.svg">
            </td>
        <tr>
    </table>
    <hr>
    <center>
        <h2><b>DAFTAR PENILAIAN<br>
            <?php $jenis = Request::get('jenis') ?>
            @if ( $jenis == 'P' )
                SEMINAR PROPOSAL PENELITIAN
            @elseif ( $jenis == 'H' )
                SEMINAR HASIL PENELITIAN
            @else
                UJIAN SKRIPSI
            @endif
            </b>
        </h2>
        <hr>
    </center>
    <div class="border-top"></div>

    <br>
    <p>Yang bertanda tangan di bawah ini:
        <table>
            <tr>
                <td>Nama</td>
                <td>: {{ $p->penguji }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $jabatan }}</td>
            </tr>
        </table>

    <p>Dengan ini menyatakan bahwa:</p>

    <table border="0" style="width: 100%" id="tbl">
        <tr>
            <td width="120">NIM</td>
            <td>: {{ $mhs->nim }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $mhs->nm_mhs }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:  {{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
        </tr>
            @if ( !empty($mhs->nm_konsentrasi) )
                <tr>
                    <td width="100">Konsentrasi</td>
                    <td>: {{ $mhs->nm_konsentrasi }}</td>
                </tr>
            @endif
        <tr>
            <td>Judul Skripsi</td>
            <td>: {!! $skripsi->judul_tmp !!}</td>
        </tr>
    </table>

    @if ( $jenis == 'P' )
        <?php $jns = 'seminar Proposal Penelitian'; ?>
    @elseif ( $jenis == 'H' )
        <?php $jns = 'seminar Hasil Penelitian'; ?>
    @else
        <?php $jns = 'Ujian Skripsi'; ?>
    @endif

    <!-- <p>Telah melaksanakan seminar {{ Request::get('jenis') == 'P' ? 'Proposal Penelitian' : 'Hasil Penelitan' }} -->
    <p>Telah melaksanakan {{ $jns }} 
    pada tanggal {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}, pada pukul.............. WITA dengan hasil sebagai berikut:
    <br>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th width="20" rowspan="2">No</th>
                <th rowspan="2">Kriteris Penilaian</th>
                <th colspan="2">Nilai</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>Angka</th>
                <th>Huruf</th>
            </tr>
        </thead>
        <tbody align="center">
            <?php $no = 1 ?>
                @foreach( Sia::kriteriaPenilaian($mhs->id_prodi) as $val )
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td align="left">{{ $val }}</td>
                        <td></td>
                        <td></td>
                        @if ( $no == 2 )
                            <td rowspan="{{ count(Sia::kriteriaPenilaian($mhs->id_prodi)) + 1 }}"></td>
                        @endif
                    </tr>
                @endforeach
            <tr>
                <td colspan="2"><b>TOTAL</b></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <br>

    <table border="0">
        <tr>
            <td rowspan="2">Nilai Rata-rata =</td>
            <td width="85px" align="center">Jumlah Nilai</td>
            <td rowspan="2">=</td>
            <td width="70px"></td>
            <td rowspan="2">=</td>
            <td width="70px"></td>
            <td rowspan="2">= A - B - C *</td>
        </tr>
        <tr>
            <td align="center" style="border-top: 1px solid #000 !important">{{ $no - 1 }}</td>
            <td align="center" style="border-top: 1px solid #000 !important">{{ $no - 1 }}</td>
        </tr>
    </table>
    <br>

    <table width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}<br>
                Dosen {{ $jabatan }}
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br></td></tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>{{ $p->penguji }}</b></td>
        </tr>
    </table>
    <br>
    <br>
    <small>
        Catatan:
        <ol>
            <li>*) Lingkari</li>
            <li>Range Nilai: (85 - 100 = A), (75 - 84 - B), (< 74 = C)</li>
        </ol>
    </small>
</div>
<footer></footer>
@endforeach
</body>
</html>