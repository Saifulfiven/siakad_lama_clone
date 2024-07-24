<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Seminar</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>

        body {
            margin: 15mm 15mm 0mm 15mm;
            font-size: 14px;
        }

        .kontainer {
            /* margin-top: {{ Sia::option('margin_kertas_kop') + 10 }}mm; */
        }

        .table-penguji {
            font-size: 14px;
        }
        .table-penguji th {
            padding: 15px;
        }
        .table-penguji td {
            padding: 10px;
        }

        tr{
            page-break-inside: avoid;
        }
        .footer{
            page-break-inside: avoid;
        }
        footer {
            page-break-after: always;
        }

    </style>
</head>
<body onload="window.print()">
<?php $ujian = Session::get('ua_jenis') == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL PENELITIAN'; ?>
<?php $ujian = Session::get('ua_jenis') == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian ?>

<div class="kontainer">

    <?php
        $ruang = Request::get('ruang');
        $tgl_ujian = Request::get('tgl_ujian');
        $hari = Rmt::hari(Carbon::parse($tgl_ujian)->format('N'));
        $smt = Session::get('ua_semester');
        $smstr = DB::table('semester')
                ->where('id_smt', $smt)
                ->first();
        $prodi = DB::table('prodi')->where('id_prodi', 61101)->first();
    ?>

    <center>
        <h2><b>
            DAFTAR MAHASISWA, PEMBIMBING & PENGUJI UJIAN {{ $ujian }}<br>
            SEMESTER {{ $smstr->smt == '1' ? 'GANJIL' : 'GENAP' }} T.A {{ substr($smstr->nm_smt, 0, 9) }} <br>
            HARI/TGL UJIAN : {{ $hari }}, {{ Rmt::tgl_indo($tgl_ujian) }} (RUANG {{ $ruang }})
            </b>
        </h2>
    </center>

    <br>


    <table border="1" width="100%" class="table-penguji">
        <tr>
            <th width="20">NO</th>
            <th>NAMA</th>
            <th>JAM</th>
            <th>JUDUL TESIS</th>
            <th>PEMBIMBING</th>
            <th>PENGUJI</th>
        </tr>

        @foreach( $mahasiswa as $mhs )
        <tr>
            <td align="center">{{ $loop->iteration }}</td>
            <td align="center">{{ $mhs->nm_mhs }}<br>{{ $mhs->nim }}</td>
            <td align="center" width="85">{{ $mhs->pukul }}</td>
            <td style="text-align: justify;">{{ $mhs->judul_tmp }}</td>
            <td width="250">
                <?php
                    $penguji = DB::table('penguji as p')
                            ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                            ->select(DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as penguji'),'p.jabatan')
                            ->where('p.id_mhs_reg', $mhs->id_mhs_reg)
                            ->where('p.jenis', $jenis)
                            ->where('p.id_smt', Session::get('ua_semester'))
                            ->orderBy('p.id')
                            ->get();
                    $no = 1;
                ?>
                <ol style="padding-left: 10px !important;margin: 0">
                    @foreach( $penguji as $pe )
                        @if ( $pe->jabatan == 'KETUA' || $pe->jabatan == 'SEKRETARIS' )
                            <li>{{ $pe->penguji }}</li>
                        @endif
                    @endforeach
                </ol>
            </td>
            <td width="250">
                <ol style="padding-left: 10px !important;margin: 0">
                    @foreach( $penguji as $pe )
                        <li>{{ $pe->penguji }}</li>
                    @endforeach
                </ol>
            </td>

        </tr>
        @endforeach
    </table>

    <br><br>
    <div class="footer">
        <table border="0" width="100%">
            <tr>
                <td width="70%"></td>
                <td>
                    Makassar, {{ Rmt::tgl_indo(Carbon::now()) }}<br>
			Wakil Dekan I Bidang Akademik dan Sumber Daya
                    <!--Ketua Program Studi Magister Manajemen-->
                    <!-- Direktur,<br> -->
                    <!-- Ub. Asdir 1 Bidang Akademik,<br> -->
                &nbsp;&nbsp;&nbsp; <img src="{{ url('resources/assets/img/ttd-yuswari.png') }}" width="150" style="margin-left:20px"><br>
                <b  style="margin-left:1px"><u>Dr. Yuswari Nur, S.E., M.Si.</u></b>
		{{--<b><u>Dr. Azlan Azhari, S.E., M.M.s</u></b>--}}
                {{-- <br>Nip. {{ $prodi->nip_ketua_prodi }} --}}
                {{-- <b><u>{{ $prodi->ketua_prodi }}</u></b>
                <br>Nip. {{ $prodi->nip_ketua_prodi }} --}}
                </td>
            </tr>
        </table>
    </div>

</div>
<footer></footer>
</body>
</html>