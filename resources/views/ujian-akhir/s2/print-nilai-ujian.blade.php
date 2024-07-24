<!DOCTYPE html>
<html>
<head>
    <title>Nilai Ujian</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 0mm 15mm;
            font-size: 14px;
        }

        .kontainer {
            margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
        }

        .range-nilai th, .range-nilai td {
            padding: 2px;
        }

        footer {
            page-break-after: always;
        }

    </style>
</head>
<body onload="window.print()">

<?php $ujian = Request::get('jenis') == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL PENELITIAN'; ?>

<?php $ujian = Request::get('jenis') == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian ?>

@foreach( $penguji as $p )

<div class="kontainer">
    
    <center>
        <h2><b>NILAI UJIAN {{ $ujian }}</b>
        </h2>
    </center>

    <br>
    <table border="0" style="width: 100%" id="tbl">
        <tr>
            <td>Nama Mahasiswa</td>
            <td>:</td>
            <td>{{ $mhs->nm_mhs }}</td>
        </tr>
        <tr>
            <td width="120">NIM</td>
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
            <td valign="top">Judul Tesis</td>
            <td valign="top">:</td>
            <td valign="top">{!! $skripsi->judul_tmp !!}</td>
        </tr>
    </table>

    <br>
    <br>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th width="20">No</th>
                <th>Kriteria Penilaian</th>
                <th width="100">Nilai Angka</th>
            </tr>
        </thead>
        <tbody align="center">
            <?php $no = 1 ?>
                @foreach( Sia::kriteriaPenilaian($mhs->id_prodi) as $val )
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td align="left">{{ $val }}</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr>
                    <td>8</td>
                    <td align="left">{{ $ujian == 'AKHIR TESIS / UJIAN TUTUP' ? 'Ujian '.ucwords(strtolower($ujian)) : ucwords(strtolower($ujian)) }}</td>
                    <td></td>
                </tr>

        </tbody>
    </table>
    <br>
    
    <?php $jabatan = str_replace('ANGGOTA', 'Penguji ', $p->jabatan); ?>
    <?php $jabatan2 = ucfirst(strtolower($p->jabatan)).' Penguji'; ?>

    <table width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">

                Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}<br>
                @if ( trim($jabatan) == 'Penguji 2' )
                    <b>Penguji 2</b>
                @else
                    <b>{{ trim($jabatan) == 'Penguji' ? 'Penguji 1' : $jabatan2 }}</b>
                @endif
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br></td></tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>{{ $p->penguji }}</b></td>
        </tr>
    </table>

    <table border="1" class="range-nilai">
        <thead>
            <tr>
                <th width="80">Nilai Angka</th>
                <th width="50">Huruf Mutu</th>
                <th width="50">Angka Mutu</th>
                <th width="100">Status Penilaian</th>
            </tr>
        </thead>
        <tbody align="center">
            <tr>
                <td>>= 85</td>
                <td>A</td>
                <td>4,0</td>
                <td>Lulus</td>
            </tr>
            <tr>
                <td>80-84,9</td>
                <td>A-</td>
                <td>3,75</td>
                <td>Lulus</td>
            </tr>
            <tr>
                <td>75-79,9</td>
                <td>B+</td>
                <td>3,5</td>
                <td>Lulus</td>
            </tr>
            <tr>
                <td>70-74,9</td>
                <td>B</td>
                <td>3,0</td>
                <td>Lulus</td>
            </tr>
            <tr>
                <td>65-69,9</td>
                <td>C+</td>
                <td>2,5</td>
                <td>Lulus Bersyarat</td>
            </tr>
            <tr>
                <td>55-64,9</td>
                <td>C+</td>
                <td>2,0</td>
                <td>Lulus Bersyarat</td>
            </tr>
        </tbody>
    </table>

</div>
<footer></footer>
@endforeach
</body>
</html>