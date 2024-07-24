<!DOCTYPE html>
<html>
<head>
	<title>Pembayaran</title>
</head>
<body onload="window.print()">

	<table border="0">
        <tr><td colspan="10"><b>Laporan Pembayaran Kuliah Mahasiswa</b></td></tr>
        <tr><td colspan="10"><b>SEKOLAH TINGGI ILMU EKONOMI (STIE)</b></td></tr>
        <tr><td colspan="10"><b>NOBEL INDONESIA</b></td></tr>
		<tr>
			<td colspan="10">Tanggal : 
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
                <th>Periode</th>
                <th>Jml SKS</th>
                <th>Tanggal Bayar</th>
                <th>Tempat Bayar</th>
                <th>Nama Bank</th>
                <th>Ket</th>
                <th align="right">Jml bayar</th>
            </tr>
        </thead>
        <?php if ( $pembayaran->count() == 0 ) { ?>
            <tr><td colspan="10">Tidak ada pembayaran pada rentang tanggal yang anda masukkan</td></tr>
        <?php } else { ?>
            <tbody align="center">

            	<?php $loop = 1 ?>
            	<?php $total_bayar = 0 ?>
                <?php foreach( $pembayaran as $pmb ) {

                    $jml_sks = DB::table('daftar_sp as sp')
                                ->where('sp.id_mhs_reg', $pmb->id_mhs_reg)
                                ->where('sp.id_smt', $pmb->id_smt)
                                ->sum('jml_sks');
                    ?>
                    <tr>
                    	<td>{{ $loop++ }}</td>
                    	<td>{{ $pmb->nim }}</td>
                    	<td align="left">{{ $pmb->nm_mhs }}</td>
                    	<td align="left">{{ $pmb->jenjang.' '.$pmb->nm_prodi }}</td>
                    	<td>{{ $pmb->nm_smt }}</td>
                        <td>{{ $jml_sks }}</td>
                        <td><?= Carbon::parse($pmb->tgl_bayar)->format('d/m/Y') ?></td>
                        <td>{{ $pmb->jenis_bayar }}</td>
                        <td>{{ $pmb->nm_bank }}</td>
                        <td>{{ $pmb->ket }}</td>
                        <td align="right"><?= $pmb->jml_bayar ?></td>
                    </tr>
                    <?php $total_bayar += $pmb->jml_bayar ?>
                <?php } ?>

                <tr>
                    <td colspan="10"><b>TOTAL</b></td>
                    <td align="right"><b><?= $total_bayar ?></b></td>
                </tr>

            </tbody>
        <?php } ?>
    </table>

</body>
</html>