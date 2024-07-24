<!DOCTYPE html>
<html>
<head>
    <title>Surat Pernyataan Tesis</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 10mm 15mm 0mm 15mm;
            font-size: 15px;
        }

    </style>
</head>
<body onload="window.print()">

<div class="kontainer" style="text-align: justify;">

    <center>
        <h2><b><u>SURAT PERNYATAAN</u></b>
        </h2>
    </center>
    <br>

    Yang bertanda tangan di bawah ini :
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
            <td>Angkatan</td>
            <td>:</td>
            <td>{{ substr($mhs->nim, 4) }}</td>
        </tr>
        <tr>
            <td>Alamat Rumah</td>
            <td>:</td>
            <td>{{ $mhs->alamat }}</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>{{ $mhs->hp }}</td>
        </tr>
    </table>

    <p>
        Telah mengikuti Ujian Tesis pada Program Pascasarjana <b>{{ config('app.itb_long') }}</b> Makassar pada tanggal {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}.
    </p>

    <p>Dengan ini saya menyatakan :</p>
    <ol style="line-height: 1.7em">
        <li>Setuju dan sanggup melaksanakan perbaikan tesis sebagaimana yang diputuskan pada
        sidang Ujian Tesis yang telah saya jalani;</li>
        <li>Setuju dan sanggup menyusun artikel ilmiah yang akan dipublikasikan pada jurnal ilmiah dengan merujuk pada tesis yang telah saya tulis;</li>
        <li>Setuju dan sanggup melaksanakan perbaikan tesis paling lambat 1 (satu) bulan setelah ujian tesis dilaksanakan yang dibuktikan dengan tanda tangan tim pembimbing;</li>
        <li>Setuju dan sanggup melaksanakan penyusunan artikel ilmiah paling lambat 1 (satu) bulan setelah ujian tesis dilaksanakan yang dibuktikan dengan penyetoran naskah kepada
        Sekretaris Program Studi Magister Manajemen PPS {{ config('app.itb_long') }} Makassar;</li>
        <li>Apabila saya tidak mampu melakukan perbaikan tesis dan penyusunan artikel ilmiah
        sebagaimana yang tertuang dalam surat pernyataan ini, maka saya bersedia untuk mengikuti
        ujian ulang tesis.</li>
    </ol>

    <p>Demikian surat pernyataan ini saya buat dengan penuh kesadaran dan rasa tanggung jawab untuk dipergunakan sebagaimana mestinya.</p>

    <p>Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}</p>

    <table width="100%">
        <tr>
            <td>
                Mengetahui :<br>
                Ketua Prodi Magister Manajemen<br>
                PPS <b>{{ config('app.itb_long') }}</b> Makassar
            </td>
            <td width="30%">&nbsp;</td>
            <td style="text-align: center">
                Yang Membuat Pernyataan,
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br></td></tr>
        <tr>
            <td><b><u>{{ $prodi->ketua_prodi }}</u></b></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b><u>{{ $mhs->nm_mhs }}</u></b></td>
        </tr>
    </table>

</div>
<footer></footer>
</body>
</html>