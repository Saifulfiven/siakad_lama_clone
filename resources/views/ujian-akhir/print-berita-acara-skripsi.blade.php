<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Ujian Skripsi</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 5mm 15mm;
            font-size: 14px;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

    </style>
</head>
<body onload="window.print()">

<div class="kontainer">

    <center>
        <h2><b>BERITA ACARA</b><br>
            Pelaksanaan Ujian Skripsi dan Komprehensif
        </h2>
    </center>

    @if ( !$skripsi->tgl_ujian )
        <h2>Isi dahulu tanggal ujian, jam, ruangan dan judul skripsinya</h2>
    @else

        <br>
        <p>Pada hari {{ Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')) }} Tanggal {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}, telah diadakan Ujian Skripsi dan Komprehensif kepada mahasiswa :
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
                @foreach( $penguji as $p )
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td align="left">{{ $p->penguji }}</td>
                        <td>{{ $p->jabatan }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3"><b>TOTAL</b></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>

        <table border="0">
            <tr>
                <td rowspan="2">Nilai Rata-rata =</td>
                <td width="70px">Jumlah Nilai</td>
                <td rowspan="2">=</td>
                <td width="70px"></td>
                <td rowspan="2">=</td>
            </tr>
            <tr>
                <td align="center" style="border-top: 1px solid #000 !important">{{ $no - 1 }}</td>
                <td align="center" style="border-top: 1px solid #000 !important">{{ $no - 1 }}</td>
            </tr>
        </table>
        <br>
        <br>
        Atau dengan huruf (..........)
        <br>
        <br>
        Berdasarkan Nilai tersebut, maka yang bersangkutan dinyatakan:
        <br><br>
        <center><h3><strong>LULUS / TIDAK LULUS</strong></h3></center>

        <table width="100%">
            <tr>
                <td style="text-align: center">Ketua Tim Penguji</td>
                <td width="100">&nbsp;</td>
                <td style="text-align: center">
                    Makassar, {{ Rmt::tgl_indo(Carbon::parse($skripsi->tgl_ujian)->format('Y-m-d')) }}<br>
                    Sekretaris
                </td>
            </tr>
            <tr><td colspan="3"><br><br><br><br></td></tr>
            <tr>
                <td style="text-align: center"><b>{{ $ketua->nm_dosen }}</b></td>
                <td>&nbsp;</td>
                <td style="text-align: center"><b>{{ $sekretaris->nm_dosen }}</b></td>
            </tr>
        </table>
        <br>
        <br>
        <small>
            Catatan:
            <ol>
                <li>Coret yang tidak perlu</li>
                <li>Range Nilai (A = 85 - 100), (B = 75 - 84), (C =< 74)</li>
            </ol>
        </small>
    @endif
</div>

</body>
</html>