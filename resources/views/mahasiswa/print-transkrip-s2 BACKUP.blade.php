<!DOCTYPE html>
<html>
<head>
    <title>Cetak Ijazah</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/font.css" />
    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/font-ijazah.css" />

    <style>

        body {
            font-family: 'Batang';
            margin: 0mm 7mm 0mm 7mm;
        }
        @media print{
            @page {
                size: landscape;
                margin: 0mm 7mm 0mm 7mm;
            }
        }

        p {
            margin: 5px 0;
        }
        .seri {
            font-size: 11pt;
            margin-top: 35px;
            font-weight: bold;
        }
        .pin {
            float: right;
            font-size: 11pt;
            padding-right: 10px;
            font-weight: bold;
        }
        .kontainer {
            margin-top: 60mm;
            position: relative;
        }

        .kontainer .koten {
            text-align: center;
            margin: 0 auto;
        }

        h2.nama {
            font-size: 24pt;
            font-weight: 700;
            font-family: 'KoHo-Medium';
        }
        p.nim {
            font-size: 14pt;
            font-family: 'Batang';
            margin-top: 4px;
            margin-bottom: 0px;
        }
        p.nim .nim {
            font-size: 14pt;
            font-family: 'KoHo-Medium';
        }
        p.program {
            font-size: 16pt;
            font-family: 'KoHo-Medium';
            font-weight: 700;
        }
        p.nik {
            font-size: 14pt;
            font-family: 'Batang';
            margin: 0px;
        }
        p.nik .nik {
            font-size: 14pt;
            font-family: 'KoHo-Medium';
        }
        p span.t4 {
            font-family: 'KoHo-Medium';
        }

        .paragraf {
            font-family: 'Batang';
            font-size: 12pt;
            font-weight: bold;
        }

        h2.gelar {
            font-family: 'ITC Eras';
            font-weight: bold;
            font-size: 20pt;
        }

        
        .sk {
            font-style: italic;
            font-weight: bold;
            font-family: 'Consolas';
            font-size: 13pt;
            margin-bottom: 10px;
        }

        .hs-footer {
            font-size: 13pt;
        }
    </style>
</head>
<body>

<div class="seri"> &nbsp;
    @if ( !empty($mhs->pin) )
        <span class="pin">Nomor Seri Ijazah : {{ $mhs->pin }}</span>
    @endif
</div>



<div class="kontainer">

    <?php
        $tgl_lahir = Rmt::tgl_indo($mhs->tgl_lahir);
        $first_car = substr($tgl_lahir, 0,1);
        if ( $first_car === '0' ) {
            $tgl_lahir = substr($tgl_lahir, 1);
        }

        $tgl_lulus = Rmt::tgl_indo($mhs->tgl_keluar);
        $first_car2 = substr($tgl_lulus, 0,1);
        if ( $first_car2 === '0' ) {
            $tgl_lulus = substr($tgl_lulus, 1);
        }

        $tgl_ijazah = Rmt::tgl_indo(Request::get('tgl_ijazah'));
        $first_car2 = substr($tgl_ijazah, 0,1);
        if ( $first_car2 === '0' ) {
            $tgl_ijazah = substr($tgl_ijazah, 1);
        }
    ?>

    <center>
        <?php $prodi = Sia::prodiFirst($mhs->id_prodi) ?>
        <p class="sk"><i><b>{{ $prodi->sk_akreditasi }}</b></i></p>
        <p class="paragraf" style="margin-bottom: 5px">dengan ini menyatakan bahwa :</p>
        <h2 class="nama">{{ $mhs->nm_mhs }}</h2>
        <p class="nim"><b>NIM :</b> <span class="nim"> <b>{{ $mhs->nim }}</b></span></p>
        <p class="nik"><b>NIK :</b> <span class="nik"> <b>{{ $mhs->nik }}</b></span></p>
        <p class="paragraf" style="margin: 0"><b> Lahir di </b> <span class="t4"><b> {{ trim($mhs->tempat_lahir) }}, {{ $tgl_lahir }}</b></span></p>

        <p class="paragraf">
            Telah menyelesaikan dengan baik dan memenuhi segala syarat pendidikan
            Magister (S-2)</p>
        <p class="program">PROGRAM PASCASARJANA<br></p>

        <p class="paragraf" style="margin-bottom: 10px">pada Program Studi Magister Manajemen STIE Nobel Indonesia.</p>

        <p class="paragraf">Lulus pada tanggal {{ $tgl_lulus }} dan kepadanya diberikan gelar</p>

        <h2 class="gelar"><b>MAGISTER MANAJEMEN (M.M.)</b></h2>

        <p class="paragraf" style="margin-bottom: 0">
            beserta hak dan kewajiban yang melekat pada gelar tersebut.<br>
        </p>
        <p class="paragraf" style="margin-top: 0">Diterbitkan di Makassar pada tanggal {{ $tgl_ijazah }}.</p>
    </center>

    <table width="100%" border="0" align="center" style="margin-top: 26px">
        <tr>
            <td width="250px" style="font-size: 12pt" align="center"><br><b>DIREKTUR</b></td>
            <td>&nbsp;</td>
            <!-- <td width="325px" style="font-size: 12pt" align="center"> -->
            <td width="320px" style="font-size: 12pt;padding-right: 15px" align="center">
                <br><b>KETUA</b>
            </td>
        </tr>
        <tr><td colspan="3" style="padding-top: 85px"></td></tr>
        <tr>
            <td class="hs-footer" align="center">
                <b>{{ Sia::option('direktur_pps') }}</b>
            </td>
            <td>&nbsp;</td>
            <td class="hs-footer" align="center" style="padding-right: 15px">
                <b>{{ Sia::option('ketua') }}</b>
            </td>
        </tr>
    </table>
<!--     <p class="sk">
        Status terakreditasi BAN-PT, No. SK: {{ $prodi->sk_akreditasi }}
    </p> -->

    

</div>

<script>
    window.print();
</script>

</body>
</html>