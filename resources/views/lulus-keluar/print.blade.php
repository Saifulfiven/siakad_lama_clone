<!DOCTYPE html>
<html>
<head>
	<title>Cetak Lulus/Keluar</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style type="text/css">
    body {
    	background-color: #fff;
    	color: #000;
    }
    table, tr, td, th {
    	color: #000 !important;
    	border-color: #000 !important;
    }
		@media print {
			 @page {
			  	/*width: 32.5cm;*/
				margin: 1.2cm 0.5cm 0.5cm 1.5cm;
			}
			footer {page-break-after: always;}
		}
    </style>
</head>
<body onload="window.print()">


<div class="col-md-12">
	<h3>{{ strtoupper(config('app.pt')) }}</h3>
	<p>{{ Sia::option('alamat_kampus') }}</p>
	<hr>
	<table border="0">
		<tr>
			<th width="150px">Program Studi</th>
			<td> : {{ Sia::prodiEkspor('lk_prodi') }}</td>
		</tr>
		<tr>
			<th>Tahun Akademik</th>
			<td> : {{ Sia::taEkspor('lk_ta') }}
		<tr>
			<th>Angkatan</th>
			<td> : {{ Sia::angkatanEkspor('lk_angkatan') }}</td>
		</tr>
		<tr>
			<th>Status</th>
			<td> : {{ empty(Sia::statusEkspor('lk_status')) ? 'Semua':Sia::statusEkspor('lk_status') }}</td>
		</tr>
	</table>
	<br>
	<table border="1" class="table table-bordered">
			<thead>
				<tr>
					<th width="10">No</th>
					<th>NIM</th>
					<th>Nama</th>
					<th>Prodi</th>
					<th>Smt lulus/keluar</th>
					<th>Status</th>
					<th>Tgl keluar</th>
			</thead>
			<tbody>
				@foreach( $mahasiswa as $r )
				<tr>
					<td>{{ $loop->iteration }}</td>
					<td>{{ $r->nim }}</td>
					<td align="left">{{ $r->nm_mhs }}</td>
					<td>{{ $r->nm_prodi }} ({{ $r->jenjang }})</td>
					<td>{{ $r->semester_keluar }}</td>
					<td>{{ $r->ket_keluar }}</td>
					<td>{{ Carbon::parse($r->tgl_keluar)->format('d-m-Y') }}</td>
				</tr>
				@endforeach
			</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>