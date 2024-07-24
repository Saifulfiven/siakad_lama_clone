<!DOCTYPE html>
<html>
<head>
    <title>Cetak Ijazah</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/font.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <style>

        body {
            font-family: 'Roboto';
            margin: 0mm 7mm 0mm 7mm;
        }
        @media print{
            @page {
                size: landscape A4;
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
            margin-top: 53mm;
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
            font-family: 'Roboto';
            margin-top: 4px;
            margin-bottom: 0px;
        }
        p.nim .nim {
            font-size: 14pt;
            font-family: 'KoHo-Medium';
        }
        p.nik {
            font-size: 14pt;
            font-family: 'Roboto';
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
            font-family: 'Roboto';
            font-size: 12pt;
            font-weight: 400;
        }

        h2.gelar {
            font-family: 'KoHo-Medium';
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
            font-size: 12pt;
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
            <p class="sk"><i><b>{{ $prodi->sk_akreditasi }}</b></i></p>
            <p class="paragraf" style="margin-bottom: 5px">dengan ini menyatakan bahwa :</p>
            <h2 class="nama">{{ $mhs->nm_mhs }}</h2>
            <p class="nim"><b>NIM :</b> <span class="nim"> <b>{{ $mhs->nim }}</b></span></p>
            <p class="nik"><b>NIK :</b> <span class="nik"> <b>{{ $mhs->nik }}</b></span></p>
            <p class="paragraf" style="margin: 0"><b> Lahir di </b> <span class="t4"><b> {{ trim($mhs->tempat_lahir) }}, {{ $tgl_lahir }}</b></span></p>

            <p class="paragraf">
                Telah menyelesaikan dengan baik dan memenuhi segala syarat pendidikan
                Sarjana (S1)<br>
                pada Program Studi {{ $prodi->nm_prodi }} Fakultas Teknologi dan Bisnis.<br>
            Lulus pada tanggal {{ $tgl_lulus }} dan kepadanya diberikan gelar</p>

            <h2 class="gelar"><b>{{ $prodi->gelar }}</b></h2>

            <p class="paragraf" style="margin-bottom: 0">
                beserta hak dan kewajiban yang melekat pada gelar tersebut.<br>
            </p>
            <p class="paragraf" style="margin-top: 0">Diterbitkan di Makassar pada tanggal {{ $tgl_ijazah }}.</p>
        </center>

        <table width="100%" border="0" align="center" style="margin-top: 15px;margin-left:-80px" class="footer">
{{--            <tr>--}}
{{--                <td width="155px"></td>--}}
{{--                <td width="300px" style="font-size: 11pt;text-align:left;padding-left: 100px"><br><b>REKTOR</b></td>--}}
{{--                <td width="180px">&nbsp;</td>--}}
{{--                <td style="font-size: 11pt;text-align:left;padding-left: 30px">--}}
{{--                    <b>DEKAN</b>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <td width="155px"></td>
                <td width="300px" style="font-size: 11pt;text-align:center"><br><b>REKTOR</b></td>
                <td width="180px">&nbsp;</td>
                <td style="font-size: 11pt;text-align:center">
{{--                    <b>KETUA PROGRAM STUDI</b>--}}
                    <b>DEKAN</b>
                </td>
            </tr>
            <tr><td colspan="4" style="padding-top: 75px"></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr>
                <td></td>
                <td class="hs-footer" align="center" style="width: 350px">
                    <b>Dr. Ir. Badaruddin, S.T., M.M., IPU., ASEAN Eng.</b>
{{--                     <b>{{ Sia::option('ketua') }}</b>--}}
{{--                    <b> Dr. H.Badaruddin, S.T., M.M</b>--}}
                    {{-- <b><img src="{{ url('resources/assets/img/ttd-sylvi.jpg') }}" width="150" style="margin-left: 25px;"></b> --}}
                </td>
                <td>&nbsp;</td>
                <td class="hs-footer" style="text-align:center">
                    {{-- <b>{{ $prodi->ketua_prodi }}</b> --}}
                    <b>Dr. Sylvia, S.E., M.Si., Ak., C.A.</b>
{{--                    <b>Karlina Ghazalah Rahman, SE.,M.Ak</b>--}}
{{--                    <b>Dr. Ahmad Firman,S.E.,M.Si.</b>--}}
                </td>
            </tr>
        </table>

    <!--     <p class="sk">
            Status terakreditasi BAN-PT, No. SK: {{ $prodi->sk_akreditasi }}
        </p> -->

        

    </div>

<script type="text/javascript">
    window.print();
</script>

</body>
</html>