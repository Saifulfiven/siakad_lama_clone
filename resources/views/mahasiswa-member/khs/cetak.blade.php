<!DOCTYPE html>
<html>

<head>
    <title>Cetak KHS</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>
        body {
            margin: 0mm 10mm 0mm 10mm;
        }

        .border-top {
            border-top: 1px dotted #999
        }

        table {
            font-size: 12px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="kontainer">
        {{-- @if ( $mhs->jenjang != 'S2 Magister') --}}
            @include('layouts.kop-s1')
        {{-- @endif --}}
        <hr>
        <center>
            <h3><b>{{ Session::get('jeniskrs_in_nilai') == 2 ? 'NILAI SEMESTER PENDEK TA. ' . Sia::sessionPeriode('nama') : 'KARTU HASIL STUDI (KHS)' }}</b>
            </h3>
        </center>

        <br>
        <table border="0" style="width: 100%" id="tbl">
            <tr>
                <td width="120">NIM</td>
                <td>: {{ $mhs->nim }}</td>
                <td>Jenis Kelamin</td>
                <td>: {{ Sia::nmJenisKelamin($mhs->jenkel) }}</td>
            </tr>
            <tr>
                <td>Nama Mahasiswa</td>
                <td>: {{ $mhs->nm_mhs }}</td>
                <td>Semester</td>
                <td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai, Session::get('smt_in_nilai')) }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
                <td width="100">Konsentrasi</td>
                <td>: {{ $mhs->nm_konsentrasi }}</td>
            </tr>
        </table>

        <br>

        <table border="1" width="100%">
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Kode MK</th>
                    <th rowspan="2">Nama MK</th>
                    <th rowspan="2">SKS</th>
                    <th colspan="2">Nilai</th>
                    <th rowspan="2">Bobot</th>
                </tr>
                <tr>
                    <th>Huruf</th>
                    <th>Indeks</th>
                </tr>
            </thead>
            <tbody align="center">

                <?php $total_sks = 0; ?>
                <?php $total_nilai = 0; ?>
                <?php $total_bobot = 0; ?>

                @foreach ($krs as $r)
                    <?php $kumulatif = $r->sks_mk * $r->nilai_indeks; ?>
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $r->kode_mk }}</td>
                        <td align="left">{{ $r->nm_mk }}</td>
                        <td>{{ $r->sks_mk }}</td>
                        <td>{{ $r->nilai_huruf }}</td>
                        <td>{{ number_format($r->nilai_indeks, 2) }}</td>
                        <td>{{ number_format($kumulatif, 2) }}</td>
                    </tr>
                    <?php
                    $total_sks += $r->sks_mk;
                    $total_nilai += $r->nilai_indeks;
                    $total_bobot += $kumulatif;
                    ?>
                @endforeach
                <tr>
                    <th colspan="3">Total</th>
                    <th>{{ $total_sks }}</th>
                    <th></th>
                    <th>{{ number_format($total_nilai, 2) }}</th>
                    <th>{{ number_format($total_bobot, 2) }}</th>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="width:100%" border="0">
            <tr>
                <td width="250">{{ Session::get('jeniskrs_in_nilai') == 1 ? 'SKS Semester Ini' : '' }}</td>
                <td width="60">{{ Session::get('jeniskrs_in_nilai') == 1 ? ': &nbsp;' . $total_sks : '' }}</td>
                <td rowspan="5" valign="top">
                    <div style="padding-left: 50px">
                        Makassar, {{ Rmt::tgl_indo(Carbon::today()) }} <br>
                        {{ Config::get('app.pt') }}<br>
                        Kabag. Adm. Akademik
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        @if (Sia::sessionMhs('prodi') == '61101')
                            {{ Sia::option('kabag_akademik_s2') }}
                        @else
                            {{ Sia::option('kabag_akademik') }}
                        @endif
                    </div>
                </td>
            </tr>

            @if (Session::get('jeniskrs_in_nilai') == 1)
                <tr class="border-top">
                    <td>SKS yang Telah Dilulusi</td>
                    <td>: &nbsp; {{ $ipk->tot_sks }}</td>
                </tr>
                <tr class="border-top">
                    <td>Indeks Prestasi Semester (IPS)</td>
                    <td>: &nbsp; {{ Sia::ipk($total_bobot, $total_sks) }}</td>
                </tr>
                <tr class="border-top">
                    <td>Indeks Prestasi Kumulatif (IPK)</td>
                    <td>: &nbsp; {{ Sia::ipk($ipk->tot_nilai, $ipk->tot_sks) }}</td>
                </tr>
                <tr class="border-top">
                    <td>Max. Beban SKS Semester Depan</td>
                    <td>: &nbsp; {{ $total_sks }}</td>
                </tr>
            @endif
        </table>

    </div>

</body>

</html>
