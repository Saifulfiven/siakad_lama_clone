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
	<h4>Laporan Pembayaran Kuliah Praktek Mahasiswa<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

    <!-- Data mahasiswa -->

        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
            <tbody class="detail-mhs">
                <tr>
                    <th width="130px" align="left">NIM</th>
                    <td>: {{ $mhs->nim }} </td>
                </tr>
                <tr>
                    <th width="130px" align="left">Nama</th>
                    <td>: {{ $mhs->nm_mhs }} </td>
                </tr>
                <tr>
                    <th align="left">Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                </tr>
                <tr>
                    <th align="left">Semester</th><td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai, Request::get('smt')) }}</td>
                </tr>
                <tr>
                    <th align="left">Jenis Pembayaran</th>
                    <td>: {{ $mhs->jenis_bayar }}</td>
                </tr>
            </tbody>
        </table>

    <!-- History pembayaran -->

        <table width="100%" border="1">
            <thead class="custom">
                <tr>
                    <th colspan="6">History Pembayaran</th>
                </tr>
                <tr>
                    <th width="10">No</th>
                    <th>Tanggal Bayar</th>
                    <th>Tempat Bayar</th>
                    <th>Nama Bank</th>
                    <th>Ket</th>
                    <th>Jml bayar</th>
                </tr>
            </thead>
            <?php if ( $pembayaran->count() == 0 ) { ?>
                <tr><td colspan="6">Tidak ada history</td></tr>
            <?php } else { ?>
                <tbody align="center">

                    <?php $no = 1 ?>
                    <?php $total_bayar = 0 ?>
                    <?php foreach( $pembayaran as $pmb ) { ?>
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td><?= Carbon::parse($pmb->tgl_bayar)->format('d/m/Y') ?></td>
                            <td>{{ $pmb->jenis_bayar }}</td>
                            <td>{{ $pmb->nm_bank }}</td>
                            <td>{{ $pmb->ket }}</td>
                            <td align="left">Rp <?= Rmt::rupiah($pmb->jml_bayar) ?></td>
                        </tr>
                        <?php $total_bayar += $pmb->jml_bayar ?>
                    <?php } ?>

                    <tr>
                        <td colspan="5" align="right" style="padding-right: 10px"><b>TOTAL</b></td>
                        <td align="left"><b>Rp <?= Rmt::rupiah($total_bayar) ?></b></td>
                    </tr>

                </tbody>
            <?php } ?>
        </table>

    <!-- End -->
	
	<br>

    @include('keuangan.footer')
    
</body>
</html>