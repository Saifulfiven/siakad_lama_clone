<!DOCTYPE html>
<html>
<head>
    <title>Mahasiswa yang telah ujian</title>
</head>
<body>

<div class="kontainer">
    <table>
        <thead class="custom">
            <tr>
                <th>No.</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>ID Prodi</th>
                <th>Nama Prodi</th>
                <th>Pembimbing</th>
                <th>Jenis</th>
            </tr>
        </thead>
        <tbody align="center">
            
            <?php $jenis_arr = ['S' => 'SKRIPSI', 'P' => 'PROPOSAL', 'H' => 'HASIL'] ?>

            @foreach($mahasiswa as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $r->nim }}</td>
                    <td>{{ $r->nm_mhs }}</td>
                    <td>{{ $r->id_prodi }}</td>
                    <td>{{ $r->jenjang }} - {{ $r->nm_prodi }}</td>
                    <td>{{ $r->pembimbing }}</td>
                    <td>{{ $jenis_arr[$jenis] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>