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
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

        table {
            font-size: 15px;
        }

    </style>
</head>
<body onload="window.print()">

<div class="kontainer">

    <center>
        <h2 style="margin-bottom: 1px"><b><u>SURAT KETERANGAN LULUS</u></b></h2>
        <b>Nomor: 

        @if ( Request::get('nomor') )
            {{ Request::get('nomor') }}
        @else
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        @endif

        {{ $mhs->id_prodi == 61101 ? '/PPs': '' }}/STIE-NI/S-KET/VI/2021</b>
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
                <td>: {{ Sia::option('ketua') }}</td>
            @endif
        </tr>
        <tr>
            @if ( $mhs->id_prodi == 61101 )
                <td>NIDN</td>
                <td>: {{ Sia::option('nip_direktur_pps') }}</td>
            @else
                <td>NIP</td>
                <td>: {{ Sia::option('ketua_nip') }}</td>
            @endif
        </tr>
        <tr>
            <td>Jabatan</td>
            @if ( $mhs->id_prodi == 61101 )
                <td>: Direktur Pascasarjana STIE Nobel Indonesia Makassar</td>
            @else
                <td>: Ketua</td>
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
            <td colspan="2"><br>Yang bersangkutan telah dinyatakan LULUS (Ijazah dalam proses penyelesaian), dan mendapat
                 Predikat Kelulusan {{ Sia::predikat($mhs->ipk) }} serta terlampir Transkrip Nilai Sementara.

            <br><br>Demikian surat keterangan ini dikeluarkan untuk dipergunakan sebagaimana mestinya.</td>
        </tr>
    </table>

    <br>

    <table border="0" style="width:100%" border="0">
        <tr>
            <td width="40%"></td>
            <td>
                Makassar, 1 Juni 2021 <br><br>
                <b>{{ $mhs->id_prodi == 61101 ? 'Direktur PPs ' : 'Ketua ' }} STIE Nobel Indonesia Makassar</b>

                @if ( $mhs->id_prodi == 61101 )
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                @else
                    <br><br><br><img src="{{ url('resources/assets/img/ttd-mashur.png') }}" width="210"><br><br>
                @endif

                @if ( $mhs->id_prodi == 61101 )
                   <b><u>{{ Sia::option('direktur_pps') }}</u></b><br>
                   <b>NIDN: {{ Sia::option('nip_direktur_pps') }}</b>
                @else
                    <b>{{ Sia::option('ketua') }}</b>
                @endif
            </td>
        </tr>
    </table>

</div>

</body>
</html>