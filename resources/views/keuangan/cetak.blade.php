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
			<td>Tanggal</td>
			<td>: 
				@if ( Request::get('tgl1') == Request::get('tgl2') )
					{{ Carbon::parse(Request::get('tgl1'))->format('d/m/Y') }}
				@else 
					{{ Carbon::parse(Request::get('tgl1'))->format('d/m/Y') }} - 
					{{ Carbon::parse(Request::get('tgl2'))->format('d/m/Y') }}
				@endif
			</td>
		</tr>
	</table>
	
	<br>

	<table border="1" width="100%">
        <thead class="custom">
            <tr>
                <th width="10">No</th>
                <th width="80">NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th>Smstr</th>
                <th>Tanggal Bayar</th>
                <th>Tempat Bayar</th>
                <th>Nama Bank</th>
                <th style="max-width: 200px">Ket</th>
                <th align="right">Jml bayar</th>
            </tr>
        </thead>
        <?php if ( $pembayaran->count() == 0 ) { ?>
            <tr><td colspan="10">Tidak ada pembayaran pada rentang tanggal yang anda masukkan</td></tr>
        <?php } else { ?>
            <tbody align="center">

            	<?php $loop = 1 ?>
            	<?php $total_bayar = 0 ?>
                <?php foreach( $pembayaran as $pmb ) { ?>
                    <tr>
                    	<td>{{ $loop++ }}</td>
                    	<td>{{ $pmb->nim }}</td>
                    	<td align="left">{{ $pmb->nm_mhs }}</td>
                    	<td align="left">{{ $pmb->jenjang.' '.$pmb->nm_prodi }}</td>
                    	<td>{{ Sia::posisiSemesterMhs($pmb->semester_mulai) }}</td>
                        <td><?= Carbon::parse($pmb->tgl_bayar)->format('d/m/Y') ?></td>
                        <td>{{ $pmb->jenis_bayar }}</td>
                        <td>{{ $pmb->nm_bank }}</td>
                        <td align="left">{{ $pmb->ket }}</td>
                        <td align="right">Rp <?= Rmt::rupiah($pmb->jml_bayar) ?></td>
                    </tr>
                    <?php $total_bayar += $pmb->jml_bayar ?>
                <?php } ?>

                <tr>
                    <td colspan="9"><b>TOTAL</b></td>
                    <td align="right"><b>Rp <?= Rmt::rupiah($total_bayar) ?></b></td>
                </tr>

            </tbody>
        <?php } ?>
    </table>

    @include('keuangan.footer')
    
</body>
</html>