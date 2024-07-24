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

    <?php
        $potongan = 0;
        $ket = '';

        if ( !empty($potong) ) {
            $potongan = $potong->potongan;
            $ket = '('.$potong->ket.')';
        }

        $total_bayar = 0;
        $sisa_bayar = 0;
        $total_tagihan = 0;

    ?>

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
                    <th align="left">Tahun Akademik</th>
                    <td>: </td>
                </tr>
            </tbody>
        </table>

    
    @if ( $mhs->akm == 'C' || $mhs->akm == 'D' )

        <hr>
        <div class="alert alert-info">
            Tidak data pembayaran ditampilkan, mahasiswa ini sedang CUTI atau DOUBLE DEGREE pada semester ini
        </div>

    @else

        <!-- Data tagihan -->
            <table width="50%" border="1">
                <thead class="custom">
                    <tr>
                        <th colspan="2">Tagihan Pembayaran</th>
                    </tr>
                    <tr>
                        <th>Nama Tagihan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>

                <?php if ( !empty($tagihan) ) { ?>
                    <tbody>

                        <?php if ( Sia::posisiSemesterMhs($mhs->semester_mulai, Request::get('smt')) > 1 ) { ?>

                            <tr>
                                <td>BPP</td>
                                <td>Rp <?= Rmt::rupiah($tagihan->bpp) ?></td>
                            </tr>
                            <tr>
                                <td>Potongan <?= $ket ?></td>
                                <td><?= 'Rp '.Rmt::rupiah($potongan) ?></td>
                            </tr>
                            <?php $total_tagihan = $tagihan->bpp ?>

                        <?php } else { ?>

                            <tr>
                                <td>BPP</td>
                                <td>Rp <?= Rmt::rupiah($tagihan->bpp) ?></td>
                            </tr>
                            <tr>
                                <td>SPP</td>
                                <td>Rp <?= Rmt::rupiah($tagihan->spp) ?></td>
                            </tr>
                            <tr>
                                <td>Seragam</td>
                                <td>Rp <?= Rmt::rupiah($tagihan->seragam) ?></td>
                            </tr>
                            <tr>
                                <td>Lain-lain</td>
                                <td><?= empty($tagihan->lainnya) ? '0' : 'Rp '.Rmt::rupiah($tagihan->lainnya) ?></td>
                            </tr>
                            <tr>
                                <td>Potongan <?= $ket ?></td>
                                <td>Rp <?= Rmt::rupiah($potongan) ?></td>
                            </tr>

                            <?php $total_tagihan = $tagihan->bpp + $tagihan->spp + $tagihan->seragam + $tagihan->lainnya ?>

                        <?php } ?>

                        <tr>
                            <td><b>TOTAL</b></td>
                            <td><b>Rp <?= Rmt::rupiah($total_tagihan - $potongan) ?></b></td>
                        </tr>
                        
                    </tbody>
                <?php } else { ?>
                    <tr><td colspan="2">Tagihan belum diinput pada tahun masuk mahasiswa ini</td></tr>
                <?php } ?>
            </table>

            <br>
            
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

                        <?php $loop = 1 ?>
                        <?php foreach( $pembayaran as $pmb ) { ?>
                            <tr>
                                <td>{{ $loop++ }}</td>
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


                        <?php if ( !empty($tagihan) ) { ?>
                            <tr><td colspan="6"></td></tr>
                            <tr>
                                <td colspan="5" align="right" style="padding-right: 10px"><b>SISA PEMBAYARAN</b></td>
                                <td align="left"><b>Rp <?= Rmt::rupiah($total_tagihan - $total_bayar - $potongan) ?></b></td>
                            </tr>
                        <?php } ?>

                    </tbody>
                <?php } ?>
            </table>
        <!-- End -->

    @endif
	
	<br>

    @include('keuangan.footer')
    
</body>
</html>