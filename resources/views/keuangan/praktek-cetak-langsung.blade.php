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


    @include('layouts.kop-s1')


    <div class="garis-1"></div>
    <div class="garis-2"></div>
    <br>
    <center>
        <h2>Laporan Pembayaran Praktek Mahasiswa</h2>
    </center>
    <br>

	<table border="0">
		<tr>
			<td>Tahun Akademik</td>
			<td> :{{ Request::get('nmsmt') }}</td>
		</tr>
        <tr>
            <td>Program Studi</td>
            <td> : {{ Session::has('pr_prodi') ? Rmt::nmProdi(Session::get('pr_prodi')) : 'Semua Program Studi' }}</td>
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
                    <th>Jumlah Dibayar</th>
                    <th>Tanggal Bayar</th>
                    <th>Jenis Bayar</th>
                </tr>
        </thead>
        <tbody align="center">
            <?php $no = 1 ?>
            @foreach($mahasiswa as $r)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td width="100">{{ $r->nim }}</td>
                    <td align="left">{{ $r->nm_mhs }}</td>
                    <td align="left">{{ $r->jenjang .' '. $r->nm_prodi }}</td>
                    <td align="right">{{ empty($r->jml_bayar) ? '-' : 'Rp '.Rmt::rupiah($r->jml_bayar) }}</td>
                    <td>{{ Carbon::parse($r->tgl_bayar)->format('d/m/Y') }}</td>
                    <td>{{ $r->ket }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    @include('keuangan.footer')

</body>
</html>