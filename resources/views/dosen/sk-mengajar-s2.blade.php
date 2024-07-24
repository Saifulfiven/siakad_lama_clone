<!DOCTYPE html>
<html>
<head>
    <title>Cetak SK Mengajar S2</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css"/>

    <style>
        .print {
            max-width: 7.5in;
        }

        ol.urut {
            margin: 0 !important;
            padding: 0 0 0 23px !important;
        }

        body, table tr td, table tr th {
            font-size: 11pt;
            line-height: 1.5em;
        }

        table.ybt tr td {
            line-height: 1.2em !important;
            padding: 0 !important;
        }

        table.penetapan {
            text-align: justify;
        }

        .stempel-box {
            position: relative;
        }

        img.stempel {
            width: 130px;
            position: absolute;
            left: -25px;
            top: 70px;
            margin-top: 760px;
            margin-left: 400px;
        }

        @media print {
            body {
                font-family: Arial;
            }

            .cl {
                font-family: 'Calligraphic' !important;
            }

            h3 {
                font-size: 17px;
                margin-bottom: 1px;
                font-weight: 700;
            }

            h4 {
                font-size: 12pt;
                font-weight: 600;
            }

            .print {
                max-width: 7.5in;
            }

            @page {
                margin: 1cm 1.5cm 0.5cm 1.4cm;
            }

            footer {
                page-break-after: always;
            }
        }
    </style>
</head>
<body onload="window.print()">

<?php

$bulan = Carbon::parse($tgl_cetak)->format('m');;
$tahun_sk = Carbon::parse($tgl_cetak)->format('Y');
$jenis_str = $jenis == 1 ? '' : ' Pendek';
?>

<div class="print">
    {{--		@include('layouts.kop-s1')--}}
    <!-- <center> -->
    <center style="margin-left: 20px; margin-right: 15px;margin-top: -10px;">
        @include('layouts.kop-s2')
        <!-- <hr> -->

        <!-- <h3 style="margin-bottom: 10px"><u><b>SURAT KEPUTUSAN</b></u></h3> -->
        <h1 style="margin-bottom: 0px;font-size: 16px"><u><b>SURAT KEPUTUSAN </b></u></h1>

        <!-- Nomor: {{ isset($_GET['no']) ? $_GET['no'] : '' }}/SK/PPS/ITB-NI/<?= Rmt::romawi($bulan) ?>/<?= $tahun_sk ?> -->

        <!-- Nomor: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;/SK/PPS/ITB-NI/<?= Rmt::romawi($bulan) ?>/<?= $tahun_sk ?> -->

        Nomor: {{$nomor_surat}}/KEP/FPS/ITB-NI/<?= Rmt::romawi($bulan) ?>/<?= $tahun_sk ?>

                <!-- Nomor: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  /SK/PPS/STIE-NI/<?= Rmt::romawi($bulan) ?>/<?= $tahun_sk ?> -->
        <br>
        <p>Tentang</p>

        <h4><b>PENUGASAN DOSEN UNTUK MENGAMPU MATA KULIAH</b></h4>
        {{-- <h4><b>DIREKTUR PROGRAM PASCASARJANA {{ config('app.itb_long') }}</b></h4> --}}
        <h4><b>DEKAN FAKULTAS PASCASARJANA INSTITUT TEKNOLOGI DAN BISNIS NOBEL INDONESIA</b></h4>
        <br>
    </center>

    <table border="0" class="penetapan">
        <tr>
            <td valign="top">Menimbang</td>
            <td valign="top"> :</td>
            <td valign="top">
                <ol class="urut">
                    <li>Bahwa untuk kelancaran kegiatan perkuliahan pada Fakultas
                        Pascasarjana {{ config('app.itb_long') }} T.A. <?= Sia::nmSmt($ta, $jenis); ?><?= $jenis_str ?>,
                        dipandang perlu menugaskan Dosen untuk mengampu mata kuliah;
                    </li>
                    <li>Bahwa saudara(i) yang namanya tercantum dalam Keputusan ini dinilai memenuhi syarat untuk
                        ditugaskan sebagai Dosen Pengampu pada Fakultas Pascasarjana {{ config('app.itb_long') }}.
                    </li>
                </ol>
            </td>
        </tr>
        <tr>
            <td valign="top">Mengingat</td>
            <td valign="top"> :</td>
            <td valign="top">
                <ol class="urut">
                    <li>Undang-Undang No.20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
                    <li>Undang-Undang No.12 Tahun 2012 tentang Pendidikan Tinggi;</li>
                    <li>Statuta {{ config('app.itb_long') }}.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td valign="top">Menetapkan</td>
            <td valign="top"> :</td>
            <td>
            </td>
        </tr>
        <tr>
            <td valign="top">Pertama</td>
            <td valign="top"> :</td>
            <td valign="top">
                Menugaskan Sdr(i).:
                <b> <?= Sia::namaDosen($dsn->gelar_depan, $dsn->nm_dosen, $dsn->gelar_belakang) ?></b> sebagai Dosen
                Pengampu Perkuliahan Fakultas Pascasarjana Institut Teknologi dan Bisnis Nobel Indonesia
                T.A. <?= Sia::nmSmt($ta, $jenis); ?> <?= $jenis_str ?> untuk mata kuliah:
            </td>
        </tr>


        <tr>
            <td colspan="3">
                <table border="1" style="font-size: 10px;width:7.5in;border:1px solid #000">
                    <tr>
                        <th align="center">No</th>
                        <th align="center">Mata Kuliah</th>
                        <th align="center">SKS</th>
                        <th align="center">Prodi</th>
                        <th align="center">Hari</th>
                        <th align="center">Jam</th>
                        <th align="center">Kelas</th>
                        <th align="center">Semester</th>
                        <th align="center">Ruang</th>
                    </tr>

                    <?php
                    $no = 1;
                    $jam_masuk = '';
                    $ruang = '';
                    $hari = '';

                    foreach ($jadwal as $r) {

                        $id = $r->id;
                        $jam_masuk = substr($r->jam_masuk, 0, 5);
                        $jam_keluar = substr($r->jam_keluar, 0, 5);
                        $ruang = $r->nm_ruangan;
                        $hari = $r->hari;
                        $kls = $r->kode_kls . "-" . Rmt::romawi($r->smt);

                        ?>

                    <tr>
                        <td style="padding:3px;" align="center"><?= $no++ ?></td>
                        <td style="padding:3px" align="left"><?= trim(ucwords(strtolower($r->nm_mk))) ?></td>
                        <td style="padding:3px;" align="center"><?= $r->sks_mk ?></td>
                        <td style="padding:3px;" align="center">
                            @if($r->nm_prodi === 'Magister Manajemen')
                                MM
                            @elseif($r->nm_prodi === 'Magister Keuangan Publik')
                                MKP
                            @elseif($r->nm_prodi === 'Magister Manajemen dan Kewirausahaan')
                                MMK
                            @else
                                MM
                            @endif
                        </td>
                        <td style="padding:3px;" align="center"><?= Rmt::hari($r->hari) ?></td>
                        <td style="padding:3px;" align="center"><?= $jam_masuk ?> - <?= $jam_keluar ?></td>
                        <td style="padding:3px;" align="center"><?= $r->kode_kls ?></td>
                        <td style="padding:3px;" align="center"><?= $r->smt ?>
                            (<?= $r->smt % 2 == 0 ? 'Genap' : 'Ganjil' ?>)
                        </td>
                        <td style="padding:3px;" align="center"><?= $ruang ?></td>
                    </tr>

                    <?php } ?>
                </table>
            </td>
        </tr>

        <tr>
            <td valign="top">Kedua</td>
            <td valign="top"> :</td>
            <td valign="top">
                Bahwa segala sesuatu yang berkaitan dengan honorarium ditentukan berdasarkan peraturan yang berlaku pada
                Fakultas Pascasarjana {{ config('app.itb_long') }};
            </td>
        </tr>
        <tr>
            <td valign="top">Ketiga</td>
            <td valign="top"> :</td>
            <td valign="top">
                Keputusan ini berlaku sejak tanggal ditetapkan dan apabila ternyata dikemudian hari terdapat kekeliruan
                di dalamnya, akan diadakan perbaikan sebagaimana mestinya.
            </td>
        </tr>

    </table>

    <br>

    <table border="0" class="ybt">
        <tr>
            <td width="400"></td>
            <td width="100">Ditetapkan di</td>
            <td width="10"> :</td>
            <td>Makassar</td>
        </tr>
        <tr>
            <td></td>
            <td>Pada Tanggal</td>
            <td> :</td>
            <td><?= Rmt::tgl_indo($tgl_cetak) ?></td>
        </tr>
        <tr>
            <td></td>
            {{-- <td colspan="3"><br><b>Direktur Program Pascasarjana<br>{{ config('app.itb_long') }}</b></td> --}}
            <td colspan="3" width="750" style=""><br><b>Dekan Fakultas Pascasarjana<br>Institut Teknologi dan Bisnis
                    Nobel Indonesia</b></td>
        </tr>
        <tr>
            <td colspan="4" style="position: relative">
                <img style="position: absolute;right: 290px;bottom: -20px;width: 120px;z-index: -1"
                     src="{{ url('resources') }}/assets/img/fps-stempel.png">
                <img src="{{ url('resources/assets/img/ttd-maryadi.png') }}" width="150"
                     style="margin-left: 420px; margin-top: 25px; position: absolute"><br><br><br><br>
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3"><b><u>{{ Sia::option('direktur_pps') }}</u></b></td>
        </tr>

    </table>

    <div style="font-size: 10px">
        <i>Tambusan :</i>
        <ol class="urut" style="line-height: 1.2em">
            <li>Dosen yang bersangkutan</li>
            <li>Arsip</li>
        </ol>
    </div>
</div>


</body>
</html>