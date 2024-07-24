<!DOCTYPE html>
<html>
<head>
    <title>Cetak Berita Acara Yudisium</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 15mm 15mm;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

        .footer{
            page-break-inside: avoid;
        }

    </style>
</head>
<body onload="window.print()">

<div class="kontainer">

    <center>
        <h2><b>BERITA ACARA YUDISIUM</b></h2>
    </center>

    <br>
    <p>Pada hari ini, {{ Rmt::hari(Carbon::parse(Request::get('tgl'))->format('N')) }} Tanggal {{ Rmt::tgl_indo(Request::get('tgl')) }}, bertempat di kampus STIE Nobel Indonesia. Telah diadakan Ujian Skripsi dan Komprehensif terhadap mahasiswa dengan program studi {{ $prodi->jenjang .' '. $prodi->nm_prodi }} dengan hasil sebagai berikut:
    <br>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">NIM</th>
                <th rowspan="2">Nama Mahasiswa</th>
                <th colspan="2">Nilai</th>
                <th rowspan="2">Ket</th>
                <th rowspan="2">IPK</th>
                <th rowspan="2">Predikat Kelulusan</th>
            </tr>
            <tr>
                <th>Angka</th>
                <th>Huruf</th>
            </tr>
        </thead>
        <tbody align="center">
            <?php $no = 1 ?>
            @foreach( $mahasiswa as $r )
                <tr>
                    <td>{{ $no++ }}</td>
                    <td align="left">{{ $r->nim }}</td>
                    <td align="left">{{ $r->nm_mhs }}</td>
                    <td>{{ round($r->angka / 3, 2) }}</td>
                    <td>{{ Sia::grade($r->id_prodi,$r->angka, 3) }}</td>
                    <td>LULUS</td>
                    <td>{{ $r->ipk }}</td>
                    <td>{{ Sia::predikat($r->ipk) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>

    Selanjutnya saudara(i) dinyatakan berhak LULUS dan berhak menyandang Gelar Akademik {{ $prodi->gelar }}
    <br>
    <br>
    
    <div class="footer">
        <table width="100%">
            <tr><td colspan="3" align="center"><strong>TIM PENGUJI</strong></td></tr>
            <tr>
                <td style="text-align: center">Ketua</td>
                <td width="40%">&nbsp;</td>
                <td style="text-align: center">
                    Wakil Ketua Bidang Akademik
                </td>
            </tr>
            <tr><td colspan="3"><br><br><br><br></td></tr>
            <tr>
                <td style="text-align: center"><b>{{ Sia::option('ketua') }}</b></td>
                <td>&nbsp;</td>
                <td style="text-align: center"><b>{{ Sia::option('ketua_1') }}</b></td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>