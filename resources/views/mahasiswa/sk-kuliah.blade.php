<!DOCTYPE html>
<html>
<head>
    <title>Cetak Surat Ket Kuliah</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 10mm 10mm 0mm 10mm;
            font-size: 14pt;
        }
        .kontainer {
            @if ( $mhs->id_prodi == 61101 )
                margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
            @endif
        }

        table {
            font-size: 14px;
        }
        table.kop {
            line-height: 1.5em;
        }
        .stempel-box {
          position: relative;
        }
        img.stempel {
            width: 120px;
            position: absolute;
            left: -25px;
            top: 70px;
        }

    </style>
</head>
<body onload="window.print()">

<?php $bulan = Carbon::parse(Request::get('tgl'))->format('n'); ?>

<div class="kontainer">

{{--    @if ( $mhs->id_prodi <> 61101 )--}}
{{--        --}}
{{--        <img src="{{ url('resources') }}/assets/img/new-kop.png" width="100%">--}}
{{--        <hr>--}}
{{--    @endif--}}

    <center>
        <h2 style="margin-bottom: 1px; margin-top: 130px; font-size: 14pt !important;"><b><u>SURAT KETERANGAN KULIAH</u></b></h2>

        <b style="font-size: 11pt !important;">Nomor: 
        @if ( Request::get('nomor') ) 
            {{ Request::get('nomor') }}
        @else
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
        @endif

        {{ $mhs->id_prodi == 61101 ? '/FPS': '/FTB' }}/ITB-NI/S-KET/{{ Rmt::romawi($bulan) }}/{{ date('Y') }}</b>
    </center>

    <br>
    <br>
    <table border="0" style="width: 100%" id="tbl">
        <tr><td colspan="2">Yang bertanda tangan di bawah ini:<br><br></td></tr>
        <tr>
            <td width="180">Nama</td>
            @if ( $mhs->id_prodi == 61101 )
                <td>: {{ $mhs->ketua_prodi }}</td>
            @else
                <!-- <td>: {{ Sia::option('ketua_1') }}</td> -->
                <td>: Dr. Sylvia, S.E., M.Si., Ak., CA</td>
            @endif
        </tr>
        <tr>
        @if ( $mhs->id_prodi == 61101 )
        <td>NIDN</td>
            <td>: {{ $mhs->nip_ketua_prodi }}</td>
            @else
            <td>NIP/NIDN</td>
                <!-- <td>: {{ Sia::option('nip_ketua_1') }}</td> -->
                <td>: 0918047501</td>
            @endif
        </tr>
        <tr>
            <td>Jabatan</td>
            @if ( $mhs->id_prodi == 61101 )
                <td>: Ketua Prodi Magister Manajemen Fakultas Pascasarjana</td>
            @else
                <td>: Dekan Fakultas Teknologi dan Bisnis</td>
            @endif
        </tr>
        <tr>
            <td colspan="2"><br>Menyatakan bahwa:<br><br></td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $mhs->nm_mhs }}</td>
        </tr>
        <tr>
            <td width="120">NIM</td>
            <td>: {{ $mhs->nim }}</td>
        </tr>
        <tr>
            <td>Tempat / Tanggal Lahir</td>
            <td>: {{ $mhs->tempat_lahir }} / {{ Rmt::tgl_indo($mhs->tgl_lahir) }}</td>
        </tr>
        <tr>
        @if($mhs->id_prodi == 61101)
            <td colspan="2"><br>Adalah benar Mahasiswa Aktif Periode {{ Sia::sessionPeriode('nama') }} pada Fakultas Pascasarjana dan terdaftar pada :<br><br></td>
            @else
            <td colspan="2"><br>Adalah benar Mahasiswa Aktif Periode {{ Sia::sessionPeriode('nama') }} pada {{ config('app.itb_long')}} dan terdaftar pada :<br><br></td>
            @endif
        </tr>
        <tr>
            <td>Jurusan / Program Studi</td>
            <td>: {{ $mhs->nm_prodi }} ({{ $mhs->jenjang }})</td>
        </tr>
        @if ( !empty($mhs->nm_konsentrasi) )
            <tr>
                <td>Konsentrasi</td>
                <td>: {{ $mhs->nm_konsentrasi }}</td>
            </tr>
        @endif

        <tr>
            <td colspan="2"><br>Demikian surat keterangan ini diberikan untuk dipergunakan sebagaimana mestinya.<br><br></td>
        </tr>
    </table>

    <br>
    <br>

    @if ( $mhs->id_prodi == 61101 )
        <table border="0" style="width:100%" border="0">
            <tr>
                <td width="45%"></td>
                <td>
                    Makassar, {{ Rmt::tgl_indo(Request::get('tgl')) }} <br>
                    <b>
                    @if ( $mhs->id_prodi == 61101 )
                    {{ Config::get('app.pt') }}<br>
                    Fakultas Pascasarjana<br>
                            Ketua Prodi Magister Manajemen<br>
                        @else
                            a.n. Rektor <br>
                            {{ Config::get('app.pt') }}
                        @endif
                        </b>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>

                    @if ( $mhs->id_prodi == 61101 )
                       <b>{{ $mhs->ketua_prodi }}</b>
                    @else
                        <b><u>{{ Sia::option('ketua_1') }}</u></b>
                        <br>{{ config('app.pimpinan1') }} Bidang Akademik
                    @endif
                </td>
            </tr>
        </table>
    @else
        <div style="float: right;">
            <div class="stempel-box">
                Makassar, {{ Rmt::tgl_indo(Request::get('tgl')) }} <br><br>
                <!-- <b>{{ config('app.itb_long') }}</b> <br>a.n. {{ config('app.pimpinan') }}<br><br> -->
                <b>Fakultas Teknologi dan Bisnis (FTB) Nobel Indonesia</b> <br>Dekan Fakultas Teknologi dan Bisnis<br>
                <img src="{{ url('resources/assets/img/ttd-sylvi.jpg') }}" width="150" style="margin-left: 25px;"><br>
                
                <img class="stempel" src="{{ url('resources') }}/assets/img/stempel-itb2.png">

                <!-- <b><u>{{ Sia::option('ketua_1') }}</u></b> -->
                <b><u>Dr. Sylvia, S.E., M.Si., Ak., CA</u></b>
                    <!-- <br>{{ config('app.pimpinan1') }} Bidang Akademik -->
            </div>
        </div>
    @endif

</div>

</body>
</html>