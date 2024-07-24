<!DOCTYPE html>
<html>
<head>
    <title>Undangan Seminar</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            /*margin: 15mm 15mm 0mm 15mm;*/
            margin: 5mm 10mm 0mm 10mm;
            font-size: 14px;
        }

        .kontainer {
            /*margin-top: {{ Sia::option('margin_kertas_kop') }}mm;*/
        }

        table {
            line-height: .8em;
        }
        table.kop {
            line-height: 1.5em;
        }

        footer {
            page-break-after: always;
        }
        .stempel-box {
            position: relative;
        }

        img.stempel {
            width: 120px;
            position: absolute;
            left: -70px;
            top: 20px;
        }
    </style>
</head>
<body onload="window.print()">

<?php

$ujian = Request::get('jenis') == 'P' ? 'Seminar Proposal Tesis' : 'Seminar Hasil Tesis';
$ujian = Request::get('jenis') == 'S' ? 'Ujian Tutup Tesis' : $ujian;

$today = Carbon::today()->format('Y-m-d');
$bulan = Carbon::today()->format('m');;
$tahun_sk = Carbon::today()->format('Y');
$jenis = Request::get('jenis') == 1 ? '' : 'Pendek';
$prodi = DB::table('prodi')->where('id_prodi', 61101)->first();
?>
<div class="kontainer">

    <!-- @include('layouts.kop-s2') -->
    <img src="{{ url('resources') }}/assets/img/fps-kop.png" width="100%">
    <hr>
    <table border="0" style="width: 100%" id="tbl">
        <tr>
            <td width="120">Nomor</td>
            <td width="10">:</td>
            <td><?= Request::get('nomor') ? Request::get('nomor') : '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  '?>/FPS/ITB-NI/<?= Rmt::romawi($bulan) ?>/<?= $tahun_sk ?></td>
            <td><span style="float: right">Makassar, {{ Rmt::tgl_indo(Carbon::now()->format('Y-m-d')) }}</span></td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td colspan="2">Satu Exp.</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td colspan="2"><b><u>{{ $ujian }}</u></b></td>
        </tr>
    </table>

    <br>

    Kepada Yth:
    <ol>
        @foreach( $penguji as $pe )
            <li>{{ $pe->penguji }}</li>
        @endforeach
    </ol>
    Di-
    <br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Makassar

    <p>Dengan hormat kami mengharapkan kehadiran Bapak/Ibu pada {{ $ujian }} Mahasiswa Program Studi Magister Manajemen <b>{{ config('app.itb_long') }}</b>, yang Insya Allah dilaksanakan pada:</p>

    <table width="100%">
        <tr>
            <td width="200">Hari/Tanggal</td>
            <td>:</td>
            <td>{{ Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')) }} / {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}</td>
        </tr>
        <tr>
            <td>Pukul</td>
            <td>:</td>
            <td>{{ $skripsi->pukul }} Wita</td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>:</td>
            <td>Kampus {{ config('app.itb') }} {{ config('app.ni') }} (Ruangan {{ $skripsi->ruangan }})</td>
        </tr>
        <tr>
            <td colspan="3"><br><b>Yang akan disajikan oleh</b><br></td>
        </tr>
        <tr>
            <td>Nama Mahasiswa</td>
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
            <td>Magister Manajemen (S2)</td>
        </tr>
        <tr>
            <td valign="top">Judul Tesis</td>
            <td valign="top">:</td>
            <td valign="top" style="line-height: 1.2em !important">{!! $skripsi->judul_tmp !!}</td>
        </tr>
    </table>

    <p>Atas perhatian dan kehadiran Bapak/Ibu kami ucapkan terima kasih.</p>

    <br>
<!-- 
    <table border="0" width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td>
                <span style="float: right;">
                    Asisten Direktur I Bidang Akademik,<br><br><br>
                    <img src="{{ url('resources/assets/img/brand-merah-hitam.png') }}">
                </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td><span style="float: right;"><b><u>{{ Sia::option('asdir_1') }}</u></b><br>
                <br>Nip. {{ Sia::option('nip_asdir_1') }}</span>
            </td>
        </tr>
    </table> -->

    <div style="float: right;">
        <div class="stempel-box">
            <!-- Asisten Direktur I Bidang Akademik,<br><br><br> -->
            Wakil Dekan I<br>Bidang Akademik dan Sumber Daya,<br><br><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="{{ url('resources/assets/img/ttd-yuswari.png') }}" width="150">
            <div ><img class="stempel" src="{{ url('resources') }}/assets/img/fps-stempel.png" style="margin-left:5px;margin-top:20px"></div>

            <b><u>Dr. Yuswari Nur, S.E., M.Si.</u></b>
                {{-- <br>Nip. {{ $prodi->nip_ketua_prodi }} --}}
            {{-- <b><u>{{ $prodi->ketua_prodi }}</u></b>
                <br>Nip. {{ $prodi->nip_ketua_prodi }} --}}
        </div>
    </div>

    <div style="margin-top: 150px">
        <p style="margin-bottom: 2px">Pakaian :</p>
        <table border="0">
            <tr>
                <td>&nbsp;&nbsp;&nbsp; - Mahasiswa</td>
                <td>:</td>
                <td>Jas Hitam + Dasi</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp; - Penguji</td>
                <td>:</td>
                <td>Jas + Dasi</td>
            </tr>
        </table>

        <p style="margin-bottom: 2px"><i>Tembusan : </i></p>
        <ol style="margin-top: 0">
            <li><i>Dekan Fakultas Pascasarjana (Sebagai laporan);</i></li>
            <li>Mahasiswa Ybs;</li>
            <li>Pertinggal.</li>
        </ol>
    </div>
</div>
<footer></footer>
</body>
</html>