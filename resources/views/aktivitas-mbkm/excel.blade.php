<!DOCTYPE html>
<html>
<head>
	<title>Eksport Aktivitas Mahasiswa</title>
</head>
<body onload="window.print()">


<table border="1" class="table table-bordered">
		<thead>
			<tr>
				<th>No.</th>
				<th>NIM</th>
				<th>Nama</th>
				<th>Semester</th>
				<th>SKS Smstr</th>
				<th>IPS</th>
				<th>SKS Total</th>
				<th>IPK</th>
				<th>Status</th>
				<th>Biaya Kuliah</th>
			</tr>
		</thead>
		<tbody>
			@foreach($mahasiswa as $r)
				<tr>
					<td><?= $loop->iteration ?></td>
					<td><?= $r->nim ?></td>
					<td><?= $r->nm_mhs ?></td>
					<td><?= $r->id_smt ?></td>
					<td><?= $r->ips ?></td>
					<td><?= $r->ipk ?></td>
					<td><?= $r->sks_smt ?></td>
					<td><?= $r->sks_total >= 145 ? '145' : $r->sks_total ?></td>
					<td><?= $r->status_mhs ?></td>
					<td></td>
				</tr>
			@endforeach
		</tbody>
</table>