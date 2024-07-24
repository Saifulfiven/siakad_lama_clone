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
	<h4>Laporan Pembayaran Semester Pendek Mahasiswa<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0">
		<tr>
			<td>Tahun Akademik</td>
			<td> : {{ Request::get('nmsmt') }}</td>
		</tr>
        <tr>
            <td>Program Studi</td>
            <td> : {{ Request::get('prodi') }}</td>
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
                    <th>Periode</th>
                    <th>Jumlah SKS</th>
                    <th>Telah Dibayar</th>
                </tr>
        </thead>
        <tbody align="center">
            <?php $total_pembayaran = 0 ?>
            @foreach($mahasiswa as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td width="100">{{ $r->nim }}</td>
                    <td align="left">{{ $r->nm_mhs }}</td>
                    <td align="left">{{ $r->jenjang .' '. $r->nm_prodi }}</td>
                    <td>{{ $r->nm_smt }}</td>
                    <td>{{ $r->jml_sks }}</td>
                    <td align="right">{{ empty($r->jml_bayar) ? '-' : 'Rp '.Rmt::rupiah($r->jml_bayar) }}</td>
                </tr>
                <?php $total_pembayaran += $r->jml_bayar ?>
            @endforeach
            <tr>
                <td colspan="6"><center><strong>TOTAL</strong></center></td>
                <td align="right"><strong>Rp {{ Rmt::rupiah($total_pembayaran) }}</strong></td>
            </tr>
        </tbody>
    </table>


    @include('keuangan.footer')

</body>
</html>