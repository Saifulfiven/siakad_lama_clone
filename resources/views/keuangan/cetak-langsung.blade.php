<!DOCTYPE html>
<html>
<head>
	<title>Cetak Pembayaran</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<style>
        @media print  
        {
            .footer{
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">


	<center>
	<h4>Laporan Pembayaran Kuliah Mahasiswa<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0">
		<tr>
			<td>Tahun Akademik</td>
			<td> : {{ $smt->nm_smt }}</td>
		</tr>
        <tr>
            <td>Angkatan</td>
            <td> : {{ Session::get('mhs_keu_angkatan') }}
                @if ( Session::get('mhs_keu_smtin') == 1 )
                    &nbsp;Ganjil
                @elseif ( Session::get('mhs_keu_smtin') == 2 )
                    &nbsp;Genap
                @endif 
            </td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td> : {{ Request::get('prodi') }}</td>
        </tr>
        <tr>
            <td>Status Pembayaran</td>
            <td> : 
                @if ( Session::get('mhs_keu_bayar') == 'BB' )
                    Belum Bayar
                @elseif ( Session::get('mhs_keu_bayar') == 'SB' )
                    Sudah Bayar
                @else
                    Sudah Bayar & Belum Bayar
                @endif
            </td>
        </tr>
	</table>
	
	<br>

    <table border="1" width="100%">
        <thead class="custom">
                <tr>
                    <th width="20px">No.</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Biaya Kuliah</th>
                    <th>Potongan</th>
                    <th>Telah Dibayar</th>
                    <th>Sisa Pembayaran</th>
                </tr>
        </thead>
        <tbody align="center">
            <?php 
                $total_pembayaran = 0;
                $total_biaya = 0;
                $total_potongan = 0;
                $total_tunggakan = 0;
            ?>
            @foreach($mahasiswa as $r)
                <?php $biaya = Sia::biayaPerMhs($r->id_mhs_reg,$r->semester_mulai) ?>
                <?php $potongan = Sia::totalPotonganPerMhs($r->id_mhs_reg,$r->semester_mulai, Session::get('mhs_keu_smt')) ?>
                <?php $total_bayar = Sia::totalBayar($r->id_mhs_reg, Session::get('mhs_keu_smt')); ?>
                <?php $tunggakan = $biaya - $potongan - $total_bayar ?>
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $r->nim }}</td>
                    <td align="left">{{ $r->nm_mhs }}</td>
                    <td align="left">{{ $r->jenjang .' '. $r->nm_prodi }}</td>
                    <td align="right">{{ Rmt::rupiah($biaya) }}</td>
                    <td align="right">{{ Rmt::rupiah($potongan) }}</td>
                    <td align="right">{{ empty($r->jml_bayar) ? '0' : Rmt::rupiah($r->jml_bayar) }}</td>
                    <td align="right">{{ Rmt::rupiah($tunggakan) }}</td>
                </tr>

                <?php
                    $total_pembayaran += $total_bayar;
                    $total_biaya += $biaya;
                    $total_potongan += $potongan;
                    $total_tunggakan += $tunggakan;
                ?>
            @endforeach
                <tr>
                    <td align="center" colspan="4"><strong>TOTAL</strong></td>
                    <td align="right"><strong>{{ Rmt::rupiah($total_biaya) }}</strong></td>
                    <td align="right"><strong>{{ Rmt::rupiah($total_potongan) }}</strong></td>
                    <td align="right"><strong>{{ Rmt::rupiah($total_pembayaran) }}</strong></td>
                    <td align="right"><strong>{{ Rmt::rupiah($total_tunggakan) }}</strong></td>
                </tr>
        </tbody>
    </table>


    @include('keuangan.footer')

</body>
</html>