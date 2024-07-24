<!DOCTYPE html>
<html>

<head>
    <title>Cetak Transkrip</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>
        body {
            margin: 15mm 12mm 0 12mm;
        }

        @media print {
            @page {
                size: portrait;
                margin: 15mm 15mm 34mm 12mm;
            }
        }

        .border-top {
            border-top: 1px dotted #999
        }

        .kontainer {
            margin-top: 20mm;
            font-family: 'Times New Roman', Times, serif;
        }

        .pin {
            float: right;
            font-size: 13px;
        }
    </style>
</head>

<body onload="window.print()">

    <?php
    
    $tgl_keluar = Carbon::parse($mhs->tgl_keluar)->format('Y-m-d');
    
    $tgl_ijazah = Rmt::tgl_indo($mhs->tgl_ijazah);
    $first_car2 = substr($tgl_ijazah, 0, 1);
    if ($first_car2 === '0') {
        $tgl_ijazah = substr($tgl_ijazah, 1);
    }
    
    $prodi = Sia::prodiFirst($mhs->id_prodi);
    
    ?>

    <div class="kontainer">
        <br><br>
        <p>
            <span>Nomor : {{ substr($mhs->seri_ijazah, 0, 4) }}{{ date('Y') }}</span>
            <span style="float: right;">Nomor Seri : {{ $mhs->pin }}</span>
        </p>
        <center style="margin-top: 45px;">
            <h3><b>TRANSKRIP NILAI</b></h3>
        </center>

        <br>
        <table border="0" style="width: 100%;" id="tbl">
            <tr>
                <td><b>Nama</b></td>
                <td>: <b>{{ strtoupper($mhs->nm_mhs) }}</b></td>
            </tr>
            <tr>
                <td width="120">NIM</td>
                <td>: {{ $mhs->nim }}</td>
            </tr>
            <tr>
                <td width="120">Tempat dan Tgl Lahir</td>
                <td>: {{ $mhs->tempat_lahir }}, {{ Rmt::tgl_indo($mhs->tgl_lahir) }}</td>
            </tr>
            <tr>
                <td>Program Pendidikan</td>
                <td>: {{ Rmt::nmJenjang($mhs->jenjang) }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $mhs->nm_prodi }} ({{ $mhs->jenjang }})</td>
            </tr>
            @if (!empty($mhs->nm_konsentrasi))
                <tr>
                    <td width="100">Konsentrasi</td>
                    <td>: {{ $mhs->nm_konsentrasi }}</td>
                </tr>
            @endif
            <tr>
                <td>Tanggal Lulus</td>
                <td>: {{ Rmt::tgl_indo($tgl_keluar) }}</td>
            </tr>
            {{-- @php
                dd($mhs);
            @endphp --}}
            <tr>
                @if ($mhs->id_prodi == '61201')
                    @php
                        $prodiFakultas = App\Prodi::where('id_prodi', '61201')->get();
                    @endphp
                    <td colspan="2">Status Terakreditasi BAN-PT, No. {{ $prodiFakultas[0]->sk_akreditasi }}</td>
                @elseif ($mhs->id_prodi == '62201')
                    @php
                        $prodiFakultas = App\Prodi::where('id_prodi', '62201')->get();
                    @endphp
                    <td colspan="2">Status Terakreditasi BAN-PT, No. {{ $prodiFakultas[0]->sk_akreditasi }}</td>
                @endif
            </tr>
        </table>

        <br>

        <table border="1" width="100%" style="font-size: 11px;border: 2px solid #000">
            <thead>
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2">KODE</th>
                    <th rowspan="2">MATA KULIAH</th>
                    <th colspan="2">NILAI</th>
                    <th rowspan="2">SKS</th>
                    <th rowspan="2">MUTU</th>
                </tr>
                <tr>
                    <th>Huruf</th>
                    <th>Angka</th>
                </tr>
            </thead>
            <tbody align="center">

                <?php $total_sks = 0; ?>
                <?php $total_nilai = 0; ?>
                <?php $total_bobot = 0; ?>

                <?php $count_krs = count($krs); ?>
                <?php $mk_terganti = []; ?>

                @if ($count_krs > 0)
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($krs as $r)
                        <!-- Skip Matakuliah terganti -->
                        <?php if (!empty($r->mk_terganti)) {
                            $mk_terganti[] = $r->mk_terganti;
                        }
                        
                        if (in_array($r->id_mk, $mk_terganti)) {
                            continue;
                        } ?>

                        <?php $kumulatif = $r->sks_mk * $r->nilai_indeks; ?>
                        <tr>
                            <td width="10">{{ $i++ }}</td>
{{--                            <td>{{ $r->kode_mk }} - {{$r->smt}}</td>--}}
                            <td>{{ $r->kode_mk }} </td>
                            <td align="left">{{ strtoupper($r->nm_mk) }}</td>
                            <td>{{ $r->nilai_huruf }}</td>
                            <td>{{ number_format($r->nilai_indeks, 2) }}</td>
                            <td>{{ $r->sks_mk }}</td>
                            <td style="text-align: right;padding-right: 5px" width="40">
                                {{ number_format($kumulatif, 2) }}</td>
                        </tr>
                        <?php
                        $total_sks += $r->sks_mk;
                        $total_nilai += $r->nilai_indeks;
                        $total_bobot += $kumulatif;
                        ?>
                    @endforeach

                    <?php $ipk = number_format($total_bobot / $total_sks, 2); ?>
                    <tr>
                        <td colspan="3" valign="top"><b>Jumlah</b></td>
                        <td></td>
                        <td></td>
                        <td><b>{{ $total_sks }}</b></td>
                        <td><b>{{ number_format($total_bobot, 2) }}</b></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <table border="1" width="100%" style="font-size: 11px;border: 2px solid #000">
            <tr>
                <td>
                    <table border="0" width="100%">
                        <tr>
                            <td width="150"><b>Indeks Prestasi Kumulatif</b></td>
                            <td width="10"><b><b>:</b></td>
                            <td style="border:0 !important"><b>{{ $ipk }}
                                    ({{ ucwords(Rmt::terbilang($ipk)) }})</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" width="100%">
                        <tr>
                            <td width="150"><b>Predikat Kelulusan</b></td>
                            <td width="10"><b>:</b></td>
                            <td><b>{{ Sia::predikat($ipk) }}</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" width="100%">
                        <tr>
                            <td width="150"><b>Judul {{ $mhs->id_prodi != 61101 ? 'Skripsi' : 'Tesis' }}</b></td>
                            <td width="10"><b>:</b></td>
                            <td><b>{{ strtoupper($mhs->judul_skripsi) }}</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <br>

        @if ($mhs->id_prodi != 61101)
            <table width="100%" border="0">
                <tr>
                    <td width="370px">&nbsp;</td>
                    <td align="center">
                        <!-- Makassar, {{ Rmt::tgl_indo($tgl_keluar) }}<br> -->
                        Makassar, {{ $tgl_ijazah }}<br>
                        Dekan Fakultas Teknologi dan Bisnis,
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><br><br><br><br><br><br></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <!-- <td align="center"><b>{{ $prodi->ketua_prodi }}</b></td> -->
                    <td align="center"><b>Dr. Sylvia, S.E, M.Si., Ak., C.A</b></td>
                </tr>
            </table>
        @else
            <table width="100%">
                <tr>
                    <td style="text-align: center">Direktur<br>Program Pascasarjana</td>
                    <td width="40%">&nbsp;</td>
                    <td style="text-align: center">

                        Makassar, {{ Rmt::tgl_indo($tgl_keluar) }}<br>

                        <br>
                        Asisten Direktur I<br>
                        Program Pascasarjana
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><br><br><br><br><br><br></td>
                </tr>
                <tr>
                    <td style="text-align: center"><b>{{ Sia::option('direktur_pps') }}</b></td>
                    <td>&nbsp;</td>
                    <td style="text-align: center"><b>{{ Sia::option('asdir_1') }}</b></td>
                </tr>
            </table>
        @endif
    </div>
</body>

</html>
