<!DOCTYPE html>
<html>
<head>
	<title>Cetak Aktivitas Mahasiswa</title>

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
	<h3>AKTIVITAS MAHASISWA <br>
	STIE NOBEL INDONESIA MAKASSAR</h3>
	<p>Jl. Sultan Alauddin No. 212 Makassar</p>
	<hr>
	<table border="0">
		<tr>
			<th width="150px">Program Studi</th>
			<td> : {{ Sia::prodiEkspor('akm_prodi') }}</td>
		</tr>
		<tr>
			<th>Tahun Akademik</th>
			<td>: {{ Sia::taEkspor('akm_ta') }}
		<tr>
			<th>Angkatan</th>
			<td> : {{ Sia::angkatanEkspor('akm_angkatan') }}</td>
		</tr>
		<tr>
			<th>Status Mahasiswa</th>
			<td> : {{ Sia::statusAkmEkspor() }}</td>
		</tr>
	</table>
	<br>
	<table border="1" class="table table-bordered">
			<thead>
				<tr>
					<th width="20px">No.</th>
					<th>NIM</th>
					<th>Nama</th>
					<th>Prodi</th>
					<th>Semester</th>
					<th>Status</th>
					<th>IPS</th>
					<th>IPK</th>
					<th>SKS Smstr</th>
					<th>SKS Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($mahasiswa as $r)
					<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ $r->nim }}</td>
						<td align="left">{{ $r->nm_mhs }}</td>
						<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
						<td align="left">{{ $r->nm_smt }}</td>
						<td>{{ $r->nm_stat_mhs }}</td>
						<td>{{ $r->ips }}</td>
						<td>{{ $r->ipk }}</td>
						<td>{{ $r->sks_smt }}</td>
						<td>{{ $r->sks_total }}</td>
					</tr>
				@endforeach
			</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>