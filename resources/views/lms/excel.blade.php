<!DOCTYPE html>
<html>
<head>
	<title>Ekspor</title>

</head>
<body>

	<table>
		<tr>
            <th>No</th>
            <th>Dosen</th>
            <th>Prodi</th>
            <th>Jenjang</th>
            <th>Materi</th>
            <th>Tugas</th>
            <th>Catatan</th>
            <th>Topik</th>
            <th>Total</th>
		</tr>
		<?php $no = 1 ?>
		@foreach( $dosen as $val )
		<tr>
			<td>{{ $no++ }}</td>
            <td><?= Sia::namaDosen($val->gelar_depan, $val->nm_dosen, $val->gelar_belakang); ?></td>
            <td><?= $val->nm_prodi ?></td>
            <td><?= $val->jenjang ?></td>
            <td><?= $val->materi ?></td>
            <td><?= $val->tugas ?></td>
            <td><?= $val->catatan ?></td>
            <td><?= $val->topik ?></td>
            <td><?= $val->materi + $val->tugas + $val->catatan + $val->topik ?></td>
		</tr>
		@endforeach
	</table>

</body>
</html>