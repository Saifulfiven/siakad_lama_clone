<!DOCTYPE html>
<html>

<head>
    <title>Cetak Transkrip</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>
        body {
            font-family: Arial !important;
            margin: 15mm 12mm 0 12mm;
            font-size: 12pt;
            font-weight: 500;
        }

        h3 {
            font-family: 'Ubuntu';
        }

        table.mk {
            font-size: 10pt !important;
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
            margin-top: 48mm;
            font-family: 'Times New Roman', Times, serif;
        }

        table.mk tr td {
            padding: 7px;
        }

        .pin {
            float: right;
        }

        .judul {
            font-weight: 800;
            font-size: 12pt;
        }
    </style>
</head>

<body>

    <div class="kontainer">

        <?php
        $tgl_lahir = Rmt::tgl_indo($mhs->tgl_lahir);
        $first_car = substr($tgl_lahir, 0, 1);
        if ($first_car === '0') {
            $tgl_lahir = substr($tgl_lahir, 1);
        }
        
        $tgl_ijazah = Rmt::tgl_indo($mhs->tgl_ijazah);
        $first_car2 = substr($tgl_ijazah, 0, 1);
        if ($first_car2 === '0') {
            $tgl_ijazah = substr($tgl_ijazah, 1);
        }
        
        $tgl_keluar = Rmt::tgl_indo($mhs->tgl_keluar);
        $first_car2 = substr($tgl_keluar, 0, 1);
        if ($first_car2 === '0') {
            $tgl_keluar = substr($tgl_keluar, 1);
        }
        ?>

        <p> &nbsp;

            @if (!empty($mhs->pin))
                <!-- <span>Nomor : {{ substr($mhs->seri_ijazah, 0, 4) }}{{ substr($mhs->seri_ijazah, -4) }}</span> -->
                <span>Nomor : {{ substr($mhs->seri_ijazah, 0, 4) }}{{ date('Y') }}</span>
                <span class="pin"> Nomor Seri Ijazah : {{ $mhs->pin }}</span>
            @endif
        </p>
        <center>
            <div class="judul">TRANSKRIP NILAI</div>
        </center>


        <br>
        <table border="0" style="width: 100%;" id="tbl" cellpadding="1">
            <tr>
                <td><b>Nama</b></td>
                <td>: <b>{{ strtoupper($mhs->nm_mhs) }}</b></td>
            </tr>
            <tr>
                <td width="160">NIM</td>
                <td>: {{ $mhs->nim }}</td>
            </tr>
            <tr>
                <td>Tempat dan Tgl Lahir</td>
                <td>: {{ trim($mhs->tempat_lahir) }}, {{ $tgl_lahir }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: Magister Manajemen</td>
            </tr>
            <tr>
                <td width="100">Konsentrasi</td>
                <td>: {{ $mhs->nm_konsentrasi }}</td>
            </tr>
            <tr>
                <td width="100">Tanggal Lulus</td>
                <td>: {{ $tgl_keluar }}</td>
            </tr>

            @if ($mhs->id_prodi == '61101')
                @php
                    $prodiFakultas = App\Prodi::where('id_prodi', '61101')->get();
                    // dd($prodiFakultas, $mhs);
                @endphp
                <tr>
                    <td colspan="2">Status {{ $prodiFakultas[0]->sk_akreditasi }}</td>
                </tr>
            @endif
        </table>

        <br>

        <table border="1" width="100%" class="mk" style="border: 2px solid #000">
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
                            <td width="10">{{ $loop->iteration }}</td>
                            <td>{{ $r->kode_mk }}</td>
                            <td align="left">{{ strtoupper($r->nm_mk) }}</td>
                            <td>{{ $r->nilai_huruf }}</td>
                            <td>{{ number_format($r->nilai_indeks, 2) }}</td>
                            <td>{{ $r->sks_mk }}</td>
                            <td style="padding-right: 5px" width="40">{{ number_format($kumulatif, 2) }}</td>
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

        <table border="1" width="100%" style="font-size: 10pt;border: 2px solid #000">
            <tr>
                <td>
                    <table border="0" width="100%">
                        <tr>
                            <td width="170"><b>Indeks Prestasi Kumulatif</b></td>
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
                            <td width="170"><b>Predikat Kelulusan</b></td>
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
                            <td width="170"><b>Judul {{ $mhs->id_prodi != 61101 ? 'Skripsi' : 'Tesis' }}</b></td>
                            <td width="10"><b>:</b></td>
                            <td style="text-transform: uppercase;font-weight: 600">{{ $mhs->judul_skripsi }}</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>



        <?php $prodi = Sia::prodiFirst($mhs->id_prodi); ?>

        <table width="100%" border="0">
            <tr>
                <td style="width: 49%"></td>
                <td style="width: 250px">Makassar, {{ $tgl_ijazah }}<br></td>
            <tr>
                <td></td>
                <td width="290">
                    WAKIL DEKAN I<br>
		    BIDANG AKADEMIK DAN SUMBER DAYA<br>
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3"><br><br><br><br><br></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <!-- <b>{{ $prodi->ketua_prodi }}</b> -->
                    <b>Dr. Yuswari Nur, S.E., M.Si.</b>
                </td>
            </tr>
        </table>
    </div>
</body>

<script>
    window.print();
</script>

</html>
