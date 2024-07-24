<!DOCTYPE html>
<html>
<head>
    <title>Cetak Transkrip Sementara</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 12mm 15mm 12mm;
        }

        @media print{
            @page {
                size: portrait;
                margin: 15mm 15mm 34mm 12mm;
            }
            footer {page-break-after: always;}
        }

        .border-top {
            border-top: 1px dotted #999
        }

        .kontainer {
            margin-top: 30mm;
            font-size: 13px;
        }

    </style>
</head>
<body onload="window.print()">

<div class="kontainer">

    @if ($mhs->jenjang != 'S2')
        <img src="{{ url('resources') }}/assets/img/new-kop.png" width="100%" style="margin-top: -200px">
    @endif

    <center>
        <h3><b>TRANSKRIP NILAI SEMENTARA</b></h3>
    </center>

    <br>
    <table border="0" style="width: 100%;" id="tbl" cellpadding="0">
        <tr>
            <td><b>Nama</b></td>
            <td>: <b>{{ strtoupper($mhs->nm_mhs) }}</b></td>
        </tr>
        <tr>
            <td width="120">NIM</td>
            <td>: {{ $mhs->nim }}</td>
        </tr>
        <tr>
            <td width="140">Tempat dan Tgl Lahir</td>
            <td>: {{ $mhs->tempat_lahir }}, {{ Rmt::tgl_indo($mhs->tgl_lahir) }}</td>
        </tr>
        <tr>
            <td width="120">Jenis Kelamin</td>
            <td>: {{ Sia::nmJenisKelamin($mhs->jenkel) }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{ $mhs->nm_prodi }} ({{ $mhs->jenjang }})</td>
        </tr>
    </table>

    <br>

    <table border="1" width="100%" style="font-size: 11px;">
        <thead>
        <tr>
            <th>NO</th>
            <th>KODE</th>
            <th>MATA KULIAH</th>
            <th>SKS</th>
            <th>HURUF</th>
            <th>NILAI</th>
            <th>BOBOT</th>
        </tr>
        </thead>
        <tbody align="center">

            <?php $total_sks = 0 ?>
            <?php $total_nilai = 0 ?>
            <?php $total_bobot = 0 ?>
            <?php $no = 1 ?>
            <?php $count_krs = count($krs) ?>
            <?php $mk_terganti = [] ?>
            
            @if ( $count_krs > 0 )

                @foreach( $krs as $r )
                    <?php $kumulatif = $r->sks_mk * $r->nilai_indeks ?>
                    <!-- Skip Matakuliah terganti -->
                    <?php if ( !empty($r->mk_terganti) ) {
                        $mk_terganti[] = $r->mk_terganti;
                    }

                    if ( in_array($r->id_mk, $mk_terganti) ) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td width="10">{{ $no++ }}</td>
                        <td>{{ $r->kode_mk }}</td>
                        <td align="left">{{ strtoupper($r->nm_mk) }}</td>
                        <td>{{ $r->sks_mk }}</td>
                        <td>{{ $r->nilai_huruf }}</td>
                        <td>{{ number_format($r->nilai_indeks,2) }}</td>
                        <td style="text-align: right;padding-right: 5px" width="40">{{ number_format($kumulatif,2) }}</td>
                    </tr>
                    <?php
                        $total_sks += $r->sks_mk;
                        $total_nilai += $r->nilai_indeks;
                        $total_bobot += $kumulatif;
                    ?>
                @endforeach

                <?php $ipk = number_format($total_bobot / $total_sks, 2); ?>
                <tr>
                    <td colspan="3"><b>Jumlah</b></td>
                    <td><b>{{ $total_sks }}</b></td> 
                    <td></td>
                    <td><b>{{ number_format($total_nilai,2) }}</b></td>
                    <td><b>{{ number_format($total_bobot,2) }}</b></td>
                </tr>
                <tr>
                    <td colspan="3"><b>Indeks Prestasi Kumulatif (IPK)</b></td>
                    <td colspan="4"><b>{{ $ipk }}</b></td>
                </tr>
            @endif
        </tbody>
    </table>

    <br>
    <br>

    <footer>
        <table width="100%">
            <tr>
                <td style="text-align: center"></td>
                <td width="40%">&nbsp;</td>
                <td style="text-align: center">
                    Makassar, {{ Rmt::tgl_indo(Carbon::now()->format('Y-m-d')) }}<br>
                    <!-- Makassar, 1 Juni 2021<br> -->
                    @if ($mhs->id_prodi == '61101')
                    {{ Config::get('app.pt') }}
                    <br>
                    Fakultas Pascasarjana
                    <br>
                    Kabag. Adm. Akademik
                    @else
                    <br>
                    Bagian Akademik
                    @endif
                </td>
            </tr>
            <tr><td colspan="3"><br><br><br><br></td></tr>
            <tr>
                <td style="text-align: center"></td>
                <td>&nbsp;</td>
                @if ( $mhs->id_prodi == '61101' )
                    <td style="text-align: center"><b>{{ Sia::option('kabag_akademik_s2') }}</b></td>
                @else
                    <td style="text-align: center"><b>{{ Sia::option('kabag_akademik') }}</b></td>
                @endif
            </tr>
        </table>
    </footer>
</div>
</body>
</html>