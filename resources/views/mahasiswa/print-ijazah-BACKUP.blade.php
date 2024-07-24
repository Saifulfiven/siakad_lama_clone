<!DOCTYPE html>
<html>
<head>
	<title>Cetak Ijazah</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<style>
        body {
            font-family: 'Arial';
            margin: 0mm 12mm 0mm 15mm;
        }
        @media print{
            @page {
                size: landscape;
                margin: 0mm 12mm 0mm 10mm;
            }
        }

        .seri {
            font-size: 13px;
            margin-top: 40px;
        }
		.kontainer {
			margin-top: 50mm;
            position: relative;
		}

        .kontainer p {
            font-size: 14.5px !important;
        }

        table {
            font-size: 16px;
            line-height: 1.3em;
        }

        table td.label {
            font-size: 15px;
            width: 210px !important;
        }

        table .hs {
            font-family: 'Monotype Corsiva','Lucida Calligraphy';
            font-size: 25px;
            font-weight: bold;
        }

        span.sk_akreditasi {
            font-size: 15px;
            font-weight: bold;
            margin-left: 30px;
            font-family: 'Arial';
        }

        .pin {
            float: right;
            font-size: 13px;
        }

	</style>
</head>
<body onload="window.print()">

<div class="seri">Nomor Seri : {{ $mhs->seri_ijazah }}
    @if ( !empty($mhs->pin) )
        <span class="pin">Nomor PIN : {{ $mhs->pin }}</span>
    @endif
</div>



<div class="kontainer">

    <?php $prodi = Sia::prodiFirst($mhs->id_prodi) ?>

    <table border="0" width="100%" class="ijazah" style="margin: 0 0 0 35px">
        <tr>
            <td class="label">Memberikan Ijazah Kepada</td>
            <td align="center" width="20"> : </td>
            <td class="hs">{{ $mhs->nm_mhs }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Induk Mahasiswa</td>
            <td align="center"> : </td>
            <td class="hs">{{ $mhs->nim }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Induk Kependudukan</td>
            <td align="center" width="20"> : </td>
            <td class="hs">{{ $mhs->nik }}</td>
        </tr>
        <tr>
            <td class="label">Tempat dan Tanggal Lahir</td>
            <td align="center"> : </td>
            <td class="hs">{{ trim($mhs->tempat_lahir) }}, {{ Rmt::tgl_indo($mhs->tgl_lahir) }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Pertama Masuk</td>
            <td align="center"> : </td>
            <td class="hs">{{ substr($mhs->semester_mulai,0,4) }}</td>
        </tr>
        <tr>
            <td class="label">Jenjang Program</td>
            <td align="center"> : </td>
            <td class="hs">{{ Sia::nmJenjang($mhs->jenjang). ' ('.$mhs->jenjang.')' }}</td>
        </tr>
        <tr>
            <td class="label">Program Studi</td>
            <td align="center"> : </td>
            <td class="hs">{{ $mhs->nm_prodi }}</td>
        </tr>
        @if ( !empty($mhs->id_konsentrasi) )
            <tr>
                <td class="label">Konsentrasi</td>
                <td align="center"> : </td>
                <td class="hs">{{ $mhs->nm_konsentrasi }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">Status</td>
            <td align="center"> : </td>
            <td class="hs">Terakreditasi <span class="sk_akreditasi">{{ $prodi->sk_akreditasi }}</span></td>
        </tr>
        <tr>
            <td class="label">Tanggal Yudisium</td>
            <td align="center"> : </td>
            <td class="hs">{{ Rmt::tgl_indo($mhs->tgl_sk_yudisium) }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 0">Ijazah ini diserahkan setelah yang bersangkutan memenuhi semua persyaratan
        yang ditentukan dan kepadanya dilimpahkan segala wewenang dan hak yang berhubungan
        dengan ijazah yang dimilikinya, serta berhak memakai gelar akademik : <b>{{ $prodi->gelar }}</b>
    </p>

    @if ( empty($mhs->id_konsentrasi) )
        <br>
        <br>
    @endif


    <table width="100%" border="0">
        <tr>
            <?php if ( $prodi->id_prodi <> 61101 ) { ?>
                <td style="text-align: center;font-size: 13px"><br>Wakil Ketua Bidang Akademik</td>
            <?php } else { ?>

                <td style="text-align: center;font-size: 13px"><br>Direktur <br>Program Pascasarjana</td>
            <?php } ?>
            <td width="30%">&nbsp;</td>
            <td style="text-align: center;font-size: 13px;padding-left: 50px">
                <?php $tgl_keluar = Carbon::parse($mhs->tgl_keluar)->addDay()->format('Y-m-d'); ?>
                Makassar, {{ Rmt::tgl_indo($tgl_keluar) }}<br>
                Ketua
            </td>
        </tr>
        <tr><td colspan="3" style="padding-top: 100px"></td></tr>
        <tr>
            <?php if ( $prodi->id_prodi <> 61101 ) { ?>
                <td class="hs" style="text-align: center"><b>{{ Sia::option('ketua_1') }}</b></td>
            <?php } else { ?>
                <td class="hs" style="text-align: center"><b>{{ Sia::option('direktur_pps') }}</b></td>
            <?php } ?>
            <td>&nbsp;</td>
            <td class="hs" style="text-align: center;padding-left: 15px;"><b>{{ Sia::option('ketua') }}</b></td>
        </tr>
    </table>

</div>

</body>
</html>