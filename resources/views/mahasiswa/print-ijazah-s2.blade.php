<!DOCTYPE html>
<html>
<head>
    <title>Cetak Ijazah</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/font.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aref+Ruqaa">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Viga">
    <style>

        body {
            font-family: 'Aref Ruqaa';
            font-size: 24pt !important;
            margin: 0mm 7mm 0mm 7mm;
        }
        @media print{
            @page {
                size: landscape A4;
                margin: 0mm 7mm 0mm 7mm;
            }

        }

        p {
            font-weight: bold !important;
            margin: 0;
            line-height: 20px;
        }



        .seri {
            font-size: 12pt;
            margin-top: 35px;
            font-weight: bold;
        }
        .pin {
            float: right;
            font-size: 12pt;
            padding-right: 10px;
            font-weight: bold;
        }
        .kontainer {
            margin-top: 55mm;
            position: relative;
        }

        .kontainer .koten {
            text-align: center;
            margin: 0 auto;
        }

        h2.nama {
            font-size: 25pt;
            font-weight: 700;
            font-family: 'KoHo-Medium';
        }
        p.nim {
            font-size: 12pt;
            font-family: 'KoHo-Medium';
            margin-top: 4px;
            margin-bottom: 0px;
        }
        p.nim .nim {
            font-family: 'KoHo-Medium';
        }
        p.nik {
            font-size: 13pt;
            font-family: 'KoHo-Medium';
            margin: 0px;
        }
        p.nik .nik {
            font-family: 'KoHo-Medium';
        }
        p.lahir {
            font-size: 13pt;
            font-family: 'KoHo-Medium';
            margin: 0px;
        }

        p.program {
            font-size: 14pt;
            font-family: 'Viga';
            font-weight: 700;
            margin: 0px;
        }

        .paragraf {
            font-family: 'Aref Ruqaa';
            font-size: 12pt;
            font-weight: 400;
            margin: 0px;
        }

        p.gelar {
            font-family: 'Viga';
            font-size: 16pt;
        }


        .sk {
            font-size: 12pt;
            margin-top: -20px;
        }

        .hs-footer {
            font-size: 13pt;
        }


    </style>
</head>
<body>

    <div class="seri">&nbsp;
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
            <p></p>

            <p class="sk">{{ $prodi->sk_akreditasi }}</p>
            <p class="paragraf">dengan ini menyatakan bahwa :</p>
            <h2 class="nama">{{ $mhs->nm_mhs }}</h2>
            <p class="nim"><b>NIM :</b> <span class="nim"> <b>{{ $mhs->nim }}</b></span></p>
            <p class="nik" style="margin-bottom: 5px;"><b>NIK :</b> <span class="nik"> <b>{{ $mhs->nik }}</b></span></p>
            <p class="lahir"><b>Lahir di {{ trim($mhs->tempat_lahir) }}, {{ $tgl_lahir }}</b></p>

            <p class="paragraf" style="">
                Telah menyelesaikan dengan baik dan memenuhi segala syarat pendidikan
                Magister (S-2)</p>

            <p class="paragraf" style="margin-bottom: 10px">pada Program Studi {{ $prodi->nm_prodi }} Fakultas Pascasarjana Institut Teknologi dan Bisnis Nobel Indonesia.<br>
            Lulus pada tanggal {{ $tgl_lulus }} dan kepadanya diberikan gelar
            </p>

            <p class="gelar"><b>{{ $prodi->gelar }}</b></p>

            <p class="paragraf">
                beserta hak dan kewajiban yang melekat pada gelar tersebut.<br>
            </p>
            <p class="paragraf">Diterbitkan di Makassar pada tanggal {{ $tgl_ijazah }}.</p>
        </center>

        <table width="100%" border="0" align="center" style="margin-top: 35px;margin-left: -80px" class="footer">
            <tr>
                <td width="135px"></td>
                <td width="500px" style="font-size: 12pt" align="center"><b>REKTOR</b></td>
                <td width="0px">&nbsp;</td>
                <td style="width:500px;font-size: 12pt;padding-left: 0px;position: relative" align="center">
                    <!-- <b>DIREKTUR</b> -->
                    <b style="position: absolute;right:10px;top:20px ">DEKAN<br>FAKULTAS PASCASARJANA</b>
                </td>
            </tr>
            <tr><td colspan="4" style="padding-top: 75px;"></td></tr>
            <tr>
                <td></td>
                <td class="hs-footer" align="center" style="width: 420px">
                    <b style="margin-left: 35px">Dr. Ir. Badaruddin, S.T., M.M., IPU., ASEAN Eng.</b>
                </td>
                <td>&nbsp;</td>
                <td class="hs-footer" align="center" style="position: relative">
                    <b style="position: absolute;right:-50px;top:20px ">{{ Sia::option('direktur_pps') }}</b>
                </td>
            </tr>
        </table>

    </div>

<script type="text/javascript">
    window.print();
</script>

</body>
</html>