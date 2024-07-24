<!DOCTYPE html>
<html>
<head>
    <title>Cetak Ijazah</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/font.css" />
    <link href="//db.onlinewebfonts.com/c/3d1b893686cc335f18f3ef9dd56380b4?family=Batang" rel="stylesheet" type="text/css"/>
    <link href="//db.onlinewebfonts.com/c/457055c70a9c98aa64002f05d6652edc?family=ITC+Eras" rel="stylesheet" type="text/css"/>
    <style>
        body {
            font-family: 'Batang';
            margin: 0mm 7mm 0mm 7mm;
        }
        @media print{
            @page {
                size: landscape;
                margin: 0mm 7mm 0mm 10mm;
            }
        }

        p {
            margin: 10px 0;
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
            margin-top: 70mm;
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
            margin-top: 5px;
        }
        p.nim .nim {
            font-size: 14pt;
            font-family: 'KoHo-Medium';
        }
        p span.t4 {
            font-family: 'KoHo-Medium';
        }

        .paragraf {
            font-family: 'Batang';
            font-size: 13pt;
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
            font-size: 12pt;
            margin-bottom: 20px;
        }

        .hs-footer {
            font-size: 14pt;
        }
    </style>
</head>
<body onload="window.print">

<div class="seri">Nomor Seri : {{ $mhs->seri_ijazah }}
    @if ( !empty($mhs->pin) )
        <span class="pin">Nomor PIN : {{ $mhs->pin }}</span>
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
        <p class="paragraf"><b> Lahir di </b> <span class="t4"><b> {{ trim($mhs->tempat_lahir) }}, {{ $tgl_lahir }}</b></span></p>

        <p class="paragraf">
            Telah menyelesaikan dengan baik dan memenuhi segala syarat pendidikan
            Magister (S2)<br>
            pada Program Studi Magister Manajemen STIE Nobel Indonesia.<br>
        Lulus pada tanggal {{ $tgl_lulus }} dan kepadanya diberikan gelar</p>

        <h2 class="gelar"><b>MAGISTER MANAJEMEN (M.M.)</b></h2>

        <p class="paragraf">
            beserta hak dan kewajiban yang melekat pada gelar tersebut.<br>
        </p>
    </center>

    <table width="100%" border="0" align="center" style="margin-top: 13px">
        <tr>
            <td width="250px" style="font-size: 13pt" align="center"><br><b>DIREKTUR</b></td>
            <td>&nbsp;</td>
            <!-- <td width="325px" style="font-size: 13pt" align="center"> -->
            <td width="350px" style="font-size: 13pt;padding-right: 15px" align="center">
                <p class="paragraf">Makassar, {{ $tgl_ijazah }}</p>
                <b>KETUA</b>
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

</body>
</html>