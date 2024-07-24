<table>
    <tr>
        <td colspan="2"><b>NILAI {{ $r->jenis == 1 ? 'PERKULIAHAN' : 'SEMESTER PENDEK' }} STIE Nobel Indonesia Makassar</b></td>
    </tr>
    <tr>
        <td>Matakuliah</td>
        <td>{{ $r->nm_mk }}</td>
    </tr>
	<tr>
		<td>Nama Dosen</td>
		<td>{{ Sia::sessionDsn('nama') }}</td>
	</tr>
	<tr>
		<td>Kelas/Ruangan</td>
		<td>{{ $r->kode_kls.' / '.$r->nm_ruangan }}</td>
	</tr>
    <tr>
        <td>Program Studi</td>
        <td>{{ $r->jenjang.' '.$r->nm_prodi }}</td>
    </tr>
    <tr>
        <td>Tahun Ajaran</td>
        <td>{{ $r->nm_smt }}</td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Kehadiran</th>
            <th>Tugas</th>
            <th>Mid</th>
            <th>Final</th>
            <th>AVG</th>
            <th>Nilai</th>
        </tr>
    </thead>
    <tbody>

        @foreach( $peserta as $ps )
            <tr>
                <td>{{ $ps->nim }}</td>
                <td>{{ $ps->nm_mhs }}</td>
                <td>{{ $ps->nil_kehadiran }}</td>
                <td>{{ $ps->nil_tugas }}</td>
                <td>{{ $ps->nil_mid }}</td>
                <td>{{ $ps->nil_final }}</td>
                <td>{{ $ps->nilai_angka }}</td>
                <td>{{ $ps->nilai_huruf }}</td>
            </tr>
        @endforeach
    </tbody>
</table>