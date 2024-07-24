 <!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Nilai</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 0mm 15mm;
            font-size: 14px;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

        .table-penguji {
            font-size: 16px;
        }
        .table-penguji th {
            padding: 10px;
        }
        .table-penguji td {
            padding: 7px;
        }

        #tbl tr td {
            padding: 3px;
        }

        footer {
            page-break-after: always;
        }

    </style>
</head>
<body onload="window.print()">

<?php $ujian = Request::get('jenis') == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL PENELITIAN'; ?>
<?php $ujian = Request::get('jenis') == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian ?>

<div class="kontainer">

    <center>
        <h2><b>REKAPITULASI NILAI UJIAN {{ $ujian }}</b>
        </h2>
    </center>
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
            <td>Magister Manajemen</td>
        </tr>
        <tr>
            <td>Program</td>
            <td>:</td>
            <td>Pascasarjana {{ config('app.itb_long') }}</td>
        </tr>
        <tr>
            <td>Pembimbing I</td>
            <td>:</td>
            <td>{{ $ketua->nm_dosen }}</td>
        </tr>
        <tr>
            <td>Pembimbing II</td>
            <td>:</td>
            <td>{{ $sekretaris->nm_dosen }}</td>
        </tr>

        <tr>
            <td valign="top">Judul Tesis</td>
            <td valign="top">:</td>
            <td valign="top">{!! $skripsi->judul_tmp !!}</td>
        </tr>
    </table>

    <br>

    <table border="1" width="100%" class="table-penguji">
        <tr>
            <th width="10">No</th>
            <th width="280">Nama Dosen</th>
            <th>Jabatan</th>
            <th>Nilai (N)</th>
        </tr>
        <?php $nilai_akhir = 0 ?>
        <?php $pembagi = 0 ?>

        @foreach( $penguji as $pe )
            <?php $jabatan = str_replace('ANGGOTA', 'PENGUJI ', $pe->jabatan); ?>
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td>{{ $pe->penguji }}</td>
                <td align="center">{{ trim($jabatan) == 'PENGUJI' ? 'PENGUJI 1' : $jabatan }}</td>
                <td align="center">{{ $pe->nilai }}</td>
            </tr>

            <?php $nilai_akhir += $pe->nilai; ?>
            <?php $pembagi += 1 ?>

        @endforeach
    </table>
<!--     <br>
        <table>
            <tr>
                <td><b>NILAI KEPUTUSAN AWAL</b></td>
                <td>:</td>
                <td align="center">
                    <div style="border-bottom: 1px solid #000">
                        <b>NB</b>
                    </div>
                    <b>100</b>
                </td>
                <td>=</td>
            </tr>
        </table> -->
        <br>
        <?php $total = number_format($nilai_akhir/$pembagi, 2) ?>

        <table>
            <tr>
                <td><b>NILAI AKHIR</b></td>
                <td width="200"><b style="font-size: 14pt">: &nbsp; {{ $total }}</b></td>
                <td><b>HURUF MUTU</b></td>
                <td><b style="font-size: 14pt">: &nbsp; {{ Sia::grade(Request::get('prodi'), $total) }}</b></td>
            </tr>
        </table>
    <br>

    <p><b>Tim Penguji Terdiri Atas:</b></p>

    <table border="1" width="100%" class="table-penguji">
        <tr>
            <th width="20">No</th>
            <th>Nama Dosen</th>
            <th>Status</th>
            <th>Tanda Tangan</th>
        </tr>
        @foreach( $penguji as $pe )
            <?php $jabatan = str_replace('ANGGOTA', 'PENGUJI ', $pe->jabatan); ?>
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td>{{ $pe->penguji }}</td>
                <td align="center">{{ trim($jabatan) == 'PENGUJI' ? 'PENGUJI 1' : $jabatan }}</td>
                <td align="center">
                    @if ( !empty($pe->ttd) )
                        <img src="{{ Rmt::linkTtd($pe->ttd) }}" width="70">
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <table width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}<br>
                <b>Ketua Tim Penguji</b>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td align="center">
            @if ( !empty($ketua->ttd) )
                <img src="{{ Rmt::linkTtd($ketua->ttd) }}" width="150">
            @else
                <br><br><br><br>
            @endif
        </td></tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>{{ $ketua->nm_dosen }}</b></td>
        </tr>
    </table>
</div>
<footer></footer>
</body>
</html>