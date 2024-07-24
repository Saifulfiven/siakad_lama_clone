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

        footer {
            page-break-after: always;
        }

    </style>
</head>
<body onload="window.print()">


@foreach( $penguji as $p )

<div class="kontainer">
    
    <?php $jabatan = $p->jabatan == 'KETUA' ? 'Pembimbing' : 'Penguji' ?>

    <center>
        <h2><b>NILAI UJIAN 
        </h2>
        <hr>
    </center>
    <div class="border-top"></div>

    <br>
    <p>Yang bertanda tangan di bawah ini:
        <table>
            <tr>
                <td>Nama</td>
                <td>: {{ $p->penguji }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $jabatan }}</td>
            </tr>
        </table>

    <p>Dengan ini menyatakan bahwa:</p>

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


    <p>Telah melaksanakan seminar {{ Request::get('jenis') == 'P' ? 'Proposal' : 'Hasil Penelitan' }}
    pada tanggal {{ Rmt::tgl_indo($skripsi->tgl_ujian) }} pada pukul.............. WITA dengan hasil sebagai berikut:
    <br>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th width="20">No</th>
                <th>Kriteris Penilaian</th>
                <th>Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody align="center">
            <?php $no = 1 ?>
                @foreach( Sia::kriteriaPenilaian($mhs->id_prodi) as $val )
                <tr>
                    <td>{{ $no++ }}</td>
                    <td align="left">{{ $val }}</td>
                    <td></td>
                    @if ( $no == 2 )
                        <td rowspan="{{ count(Sia::kriteriaPenilaian($mhs->id_prodi)) + 1 }}"></td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="2"><b>TOTAL</b></td>
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
            <td rowspan="2">= A - B - C *</td>
        </tr>
        <tr>
            <td align="center" style="border-top: 1px solid #000 !important">{{ $no - 1 }}</td>
            <td align="center" style="border-top: 1px solid #000 !important">{{ $no - 1 }}</td>
        </tr>
    </table>
    <br>
    <br>

    <table width="100%">
        <tr>
            <td></td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}<br>
                Dosen {{ $jabatan }}
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br></td></tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>{{ $p->penguji }}</b></td>
        </tr>
    </table>
    <br>
    <br>
    <small>
        Catatan:
        <ol>
            <li>*) Lingkari</li>
            <li>Range Nilai (A = 85 - 100), (B = 75 - 84), (C =< 74)</li>
        </ol>
    </small>
</div>
<footer></footer>
@endforeach
</body>
</html>