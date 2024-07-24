<!DOCTYPE html>
<html>
<head>
	<title>Cetak Mahasiswa</title>

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
	<h3>STIE NOBEL INDONESIA MAKASSAR</h3>
	<p>Jl. Sultan Alauddin No. 212 Makassar</p>
	<hr>
	<table border="0">
		<tr>
			<th width="150px">Program Studi</th>
			<td> : {{ Sia::prodiEkspor() }}</td>
		</tr>
		<tr>
			<th>Angkatan</th>
			<td> : {{ Sia::angkatanEkspor() }}</td>
		</tr>
		@if ( Sia::statusEkspor() )
			<tr>
				<th>Status Mahasiswa</th>
				<td> : {{ Sia::statusEkspor() }}</td>
			</tr>
		@endif
	</table>
	<br>
	<table border="1" class="table table-bordered">
			<thead>
				<tr>
					<th width="10">No</th>
					<th>NIM</th>
					<th>Nama</th>
					<th>Kelamin</th>
					<th>Agama</th>
					<th>Tempat,tgl lahir</th>
					<th>Alamat</th>
					<th>Prodi</th>
					<th>Kelas</th>
					<th>Status</th>
			</thead>
			<tbody>
				@foreach( $mahasiswa as $r )
				<tr>
					<td align="center">{{ $loop->iteration }}</td>
					<td>{{ $r->nim }}</td>
					<td align="left">{{ $r->nm_mhs }}</td>
					<td>{{ Sia::nmJenisKelamin($r->jenkel) }}</td>
					<td>{{ $r->nm_agama }}</td>
					<td>{{ $r->tempat_lahir }}, {{ $r->tgl_lahir }}</td>
					<td>{{ $r->alamat }}</td>
					<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
					<td>{{ $r->kode_kelas }}</td>
					<td>{{ $r->ket_keluar == '' ? 'AKTIF' : $r->ket_keluar }}</td>
				</tr>
				@endforeach
			</tbody>
	</table>

	<footer></footer>
</div>

</body>
</html>