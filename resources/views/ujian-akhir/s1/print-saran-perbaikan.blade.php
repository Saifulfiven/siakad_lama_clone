<!DOCTYPE html>
<html>

<head>
    <title>Catatan perbaikan</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>
        body {
            margin: 5mm 10mm 0mm 10mm;
            font-size: 15px;
        }

        .table-saran tr td.none-border {
            border-top: none;
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

    <?php
    if (empty($skripsi)) {
        echo '<h2>Pastikan Tangal dan Waktu seminar telah diinput.</h2>';
        exit();
    }
    
    $ujian = Request::get('jenis') == 'P' ? 'SEMINAR PROPOSAL PENELITIAN' : 'SEMINAR HASIL PENELITIAN'; ?>
    <?php $ujian = Request::get('jenis') == 'S' ? 'UJIAN TUTUP' : $ujian; ?>

    @foreach ($penguji as $p)

        <div class="kontainer">
            <?php $jabatan = $p->jabatan == 'KETUA' || $p->jabatan == 'SEKRETARIS' ? 'Pembimbing' : 'Penguji'; ?>
            <table width="100%" border="0" class="kop">
                {{-- <tr>
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
        <tr> --}}
                <tr>
                    <td><img src="{{ url('resources') }}/assets/img/new-kop.png" width="100%"></td>
                </tr>
            </table>
            <hr>

            <center>
                <h2><b>CATATAN PERBAIKAN <br>{{ $ujian }}</b>
                </h2>
                <hr>
            </center>
            <div class="border-top"></div>
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
                    <td>{{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
                </tr>
                @if (!empty($mhs->nm_konsentrasi))
                    <tr>
                        <td width="100">Konsentrasi</td>
                        <td>: {{ $mhs->nm_konsentrasi }}</td>
                    </tr>
                @endif
                <tr>
                    <td valign="top">Judul Skripsi</td>
                    <td valign="top">:</td>
                    <td valign="top">{!! $skripsi->judul_tmp !!}</td>
                </tr>
            </table>

            <br>
            <br>
            <br>

            <h3>CATATAN :</h3>
            <br>
            <br>
            <table width="100%" border="0" class="table-saran">

                @for ($a = 1; $a <= 3; $a++)
                    <tr>
                        <td class="none-border" rowspan="4" width="20" valign="top">{{ $a }}.</td>
                        <td class="none-border"></td>
                    </tr>
                    @for ($i = 1; $i <= 3; $i++)
                        <tr>
                            <td></td>
                        </tr>
                    @endfor
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
                        <b>Dosen {{ $jabatan }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><br><br><br><br></td>
                </tr>
                <tr>
                    <td></td>
                    <td>&nbsp;</td>
                    <td style="text-align: center"><b>{{ $p->penguji }}</b></td>
                </tr>
            </table>
        </div>
        <footer></footer>
    @endforeach
</body>

</html>
