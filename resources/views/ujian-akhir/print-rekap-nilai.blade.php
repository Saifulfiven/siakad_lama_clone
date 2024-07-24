<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi nilai ujian akhir</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 5mm 15mm;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

    </style>
</head>
<body onload="window.print()">

<div class="kontainer">

    <center>
        <h2><b>REKAPITULASI NILAI {{ strtoupper(Session::get('ua_nm_jenis')) }}<br>
            PROGRAM STUDI {{ strtoupper($prodi->nm_prodi .' ('.$prodi->jenjang.')') }}</b>
        </h2>
        <h3><b>Tanggal, {{ Rmt::tgl_indo(Request::get('tgl')) }}</b></h3>
    </center>

    <br>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Nilai 1</th>
                <th>Nilai 2</th>
                <th>Nilai 3</th>
                    <th>Nilai 4</th>
                <th>Jumlah</th>
                <th>Nilai Huruf</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody align="center">
            @foreach( $mhs as $r )
                <?php $nilai = explode(',', $r->nilai); ?>
                <?php $grade = Sia::grade($prodi->id_prodi, $r->total_nilai, 3) ?>
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td align="left">{{ $r->nim }}</td>
                    <td align="left">{{ $r->nm_mhs }}</td>
                    <td>{{ @$nilai[0] }}</td>
                    <td>{{ @$nilai[1] }}</td>
                    <td>{{ @$nilai[2] }}</td>
                    <td>{{ @$nilai[3] }}</td>
                    <td>{{ number_format($r->total_nilai,1) }}</td>
                    <td>{{ $grade }}</td>
                    <td>{{ Sia::predikatUjianAkhir($grade) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>

    <table width="100%">
        <tr>
            <td style="text-align: center">{{ config('app.pt') }}<br>Mengetahui</td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo(Carbon::now()->format('Y-m-d')) }}<br>
                Dibuat oleh
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br></td></tr>
        <tr>
            <td style="text-align: center">
                <b><u>{{ Sia::option('ketua_1') }}</u></b><br>
                Wakil Ketua I Bid. Akademik
            </td>
            <td>&nbsp;</td>
            <td style="text-align: center">
                @if ( in_array(61101, Sia::getProdiUser()) )
                    <b><u>{{ Sia::option('kabag_akademik_s2') }}</u></b>
                @else
                    <b><u>{{ Sia::option('kabag_akademik') }}</u></b>
                @endif
                <br>
                Kabag. Akademik
            </td>
        </tr>
    </table>
</div>

</body>
</html>