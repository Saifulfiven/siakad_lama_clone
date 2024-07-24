<!DOCTYPE html>
<html>
<head>
    <title>Cetak Surat Ket Lulus</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 10mm 20mm 0mm 20mm;
        }
        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') - 20 }}mm;
        }

        table {
            font-size: 15px;
        }

    </style>
</head>
<body onload="window.print()">

<div class="kontainer">
    @if ($mhs->id_prodi != 61101)
        <img src="{{ url('resources/assets/img/new-kop.png') }}" width="100%" style="margin-top: -100px;">
        <hr>
    @endif

<!-- {{$mhs->id_prodi == 61101 ? '' : ''}} -->

    <center>
        <h2 style="margin-bottom: 1px;font-size: 1.9em"><b><u>SURAT KETERANGAN LULUS</u></b></h2>
        <b style="font-size: 15px">&nbsp;Nomor: 

        @if ( Request::get('nomor') )
            {{ Request::get('nomor') }}
        @else
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        @endif

        {{ $mhs->id_prodi == 61101 ? '/FPS': '/FTB' }}/ITB-NI/S-KET/{{ Rmt::romawi(date('n')) }}/{{ date('Y') }}</b>
    </center>

    <br>
    <br>
    <table border="0" style="width: 100%" id="tbl">
        <tr><td colspan="2">Yang bertanda tangan di bawah ini:<br><br></td></tr>
        <tr>
            <td width="180">Nama</td>
            @if ( $mhs->id_prodi == 61101 )
                <td>: {{ Sia::option('direktur_pps') }}</td>
            @else
                {{-- <td>: {{ Sia::option('ketua') }}</td> --}}
                <td>: Dr. Sylvia, S.E., M.Si., Ak., CA</td>
            @endif
        </tr>
        <tr>
            @if ( $mhs->id_prodi == 61101 )
                <td>NIDN</td>
                <td>: {{ Sia::option('nip_direktur_pps') }}</td>
            @else
                <td>NIP</td>
                {{-- <td>: {{ Sia::option('ketua_nip') }}</td> --}}
                <td>: 0918047501</td>
            @endif
        </tr>
        <tr>
            <td>Jabatan</td>
            @if ( $mhs->id_prodi == 61101 )
                <td>: Dekan Fakultas Pascasarjana</td>
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
            <td>Tanggal Lulus</td>
            <td>: {{ Rmt::tgl_indo($mhs->tgl_keluar) }}</td>
        </tr>
        @if ( $mhs->id_prodi == 61101 )
            <tr>
                <td colspan="2"><br>Yang bersangkutan telah dinyatakan LULUS (Ijazah dalam proses penyelesaian), 
                    dan mendapat Predikat Kelulusan {{ Sia::predikat($mhs->ipk) }} serta terlampir Transkrip Nilai Sementara.<br><br>Demikian surat keterangan ini dikeluarkan untuk dipergunakan sebagaimana mestinya.</td>
            </tr>
        @else
            <tr>
                <td colspan="2"><br>Yang bersangkutan telah dinyatakan LULUS (Ijazah dalam proses penyelesaian), dengan Lampiran Transkrip Nilai Sementara.<br><br>Demikian surat keterangan ini dikeluarkan untuk dipergunakan sebagaimana mestinya.</td>
            </tr>
        @endif
    </table>

    <br>

    @if ( $mhs->id_prodi == 61101 )
        <table border="0" style="width:100%" border="0">
            <tr>
                <td></td>
                <td width="350">
                    Makassar, {{ Rmt::tgl_indo(Carbon::today()) }} <br><br>
                    <b>Fakultas Pascasarjana<br>{{ Config::get('app.pt') }}</b>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>

                   <b><u>{{ Sia::option('direktur_pps') }}</u></b><br>
                   Dekan
                </td>
            </tr>
        </table>
    @else
        <table border="0" style="width:100%">
            <tr>
                <td width="55%">
<img class="stempel" src="{{ url('resources/assets/img/stempel-itb2.png') }}" style="width: 150px;margin-left: 275px;position: fixed; margin-top: -50px;">
                </td>
                <td width="">
                    Makassar, {{ Rmt::tgl_indo(Carbon::today()) }} <br><br>
                    {{-- <b>{{  ? 'Direktur PPs ' : 'Rektor ' }} {{ Config::get('app.pt') }}</b> --}}
                    @if ($mhs->id_prodi == 61101)
                        <b>Direktur PPs {{ Config::get('app.pt') }}</b>
                    @else
                        <b>Dekan Fakultas Teknologi dan Bisnis Nobel Indonesia</b>
                    @endif

                    @if ( $mhs->id_prodi == 61101 )
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    @else
                        {{-- <br><br><br><img src="{{ url('resources/assets/img/badar.png') }}" width="210"><br><br> --}}
                        <br><img src="{{ url('resources/assets/img/ttd-sylvi.jpg') }}" width="210"><br>
                    @endif

                    @if ( $mhs->id_prodi == 61101 )
                       <b><u>{{ Sia::option('direktur_pps') }}</u></b><br>
                       <b>NIDN: {{ Sia::option('nip_direktur_pps') }}</b>
                    @else
                        {{-- <b>{{ Sia::option('ketua') }}</b> --}}
                        <b>Dr. Sylvia, S.E., M.Si., Ak., CA</b>
                    @endif
                </td>
            </tr>
        </table>
    @endif

</div>

</body>
</html>
