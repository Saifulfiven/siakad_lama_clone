@if ( empty($skripsi->tgl_ujian) )
    <center><h3>Tanggal ujian belum diinput oleh bag. akademik</h3></center>
    <?php exit; ?>
@endif
<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Ujian Skripsi</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 5mm 15mm 5mm 15mm;
            font-size: 14px;
        }

        .kontainer {
            max-width: 7in;
            margin: 0 auto;
        }
        .box-ttd {
            width: 180px;
            position: relative;
        }

        .box-ttd img {
            position: absolute;
            left: 45px;
            top: -3px;
            width: 100px;
        }

        .footer-ttd {
            position: relative;
            padding: 25px;
            width: 324px;
        }

        .footer-ttd img {
            position: absolute;
            left: 55px;
            bottom: -8px;
        }
    </style>
</head>
<body onload="window.print()">

<?php
    $jenis = Request::get('jenis');
    if ( $jenis == 'P' ) {
        $jenis_ = 'Seminar Proposal Penelitian';
    } elseif ( $jenis == 'H' ) {
        $jenis_ = 'Seminar Hasil Penelitian';
    } else {
        $jenis_ = 'Ujian Skripsi dan Komprehensif';
    }
?>

<div class="kontainer">
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
        <h2><b>BERITA ACARA</b><br>
            Pelaksanaan {{ $jenis_ }}
        </h2>
    </center>

        <br>
        <p>Pada hari {{ Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')) }} Tanggal {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}, telah diadakan {{ $jenis_ }} kepada mahasiswa :
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

        <br>

        Dosen Penguji dan Nilai Sebagai Berikut:<br><br>

        <table border="1" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Penguji</th>
                    <th>Jabatan</th>
                    <th>Nilai</th>
                    <th>Tanda Tangan</th>
                </tr>
            </thead>
            <tbody align="center">
                <?php $no = 1 ?>
                <?php $pembagi = 0 ?>
                <?php $total = 0 ?>
                @foreach( $penguji as $p )
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td align="left">{{ $p->penguji }}</td>
                        <td>{{ $p->jabatan }}</td>
                        <td>{{ $p->nilai }}</td>
                        <td class="box-ttd">
                            @if ( !empty($p->ttd) )
                                <img src="{{ Rmt::linkTtd($p->ttd) }}">
                            @endif
                        </td>
                    </tr>
                    <?php $total += $p->nilai ?>
                    <?php $pembagi += 1 ?>
                @endforeach

                <tr>
                    <td colspan="3"><b>TOTAL</b></td>
                    <td><b>{{ $total }}</b></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>

        <?php $rata2 = number_format($total/$pembagi, 2) ?>

        <table border="0">
            <tr>
                <td rowspan="2">Nilai Rata-rata =</td>
                <td width="70px" align="center">Jumlah Nilai</td>
                <td rowspan="2">=</td>
                <td width="70px" align="center">{{ $total }}</td>
                <td rowspan="2">=</td>
                <td rowspan="2">{{ $rata2 }}</td>
            </tr>
            <tr>
                <td align="center" style="border-top: 1px solid #000 !important">{{ $pembagi }}</td>
                <td align="center" style="border-top: 1px solid #000 !important">{{ $pembagi }}</td>
            </tr>
        </table>
        <br>
        <br>
        Atau dengan huruf ( <b>{{ Sia::grade(Request::get('prodi'), $rata2) }}</b> )
        <br>
        <br>
        Berdasarkan Nilai tersebut, maka yang bersangkutan dinyatakan:
        <br><br>
        <center><h3><strong>LULUS / <strike>TIDAK LULUS</strike></strong></h3></center>
        <br>
        <table  border="0" width="100%">
            <tr>
                <td style="text-align: center">Ketua Tim Penguji</td>
                <td width="100">&nbsp;</td>
                <td style="text-align: center">
                    Makassar, {{ Rmt::tgl_indo(Carbon::parse($skripsi->tgl_ujian)->format('Y-m-d')) }}<br>
                    Sekretaris
                </td>
            </tr>
            <tr>
                <td align="center" class="footer-ttd">
                    @if ( !empty($ketua->ttd) )
                        <img src="{{ Rmt::linkTtd($ketua->ttd) }}" width="150">
                    @endif
                </td>
                <td></td>
                <td align="center" class="footer-ttd">
                    @if ( !empty($sekretaris->ttd) )
                        <img src="{{ Rmt::linkTtd($sekretaris->ttd) }}" width="150">
                    @endif
                </td>
            </tr>
            <tr style="padding: 0">
                <td style="text-align: center;padding: 0"><b>{{ $ketua->nm_dosen }}</b></td>
                <td>&nbsp;</td>
                <td style="text-align: center;padding: 0"><b>{{ $sekretaris->nm_dosen }}</b></td>
            </tr>
        </table>
<!--         <br>
        <br>
        <small>
            Catatan:
            <ol>
                <li>Coret yang tidak perlu</li>
                <li>Range Nilai (A = 85 - 100), (B = 75 - 84), (C =< 74)</li>
            </ol>
        </small> -->
</div>

</body>
</html>