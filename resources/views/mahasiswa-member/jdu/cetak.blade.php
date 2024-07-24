<!DOCTYPE html>
<html>
<head>
	<title>Cetak Jadwal Ujian</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<style>

		body {
			margin: 10mm 10mm 10mm 10mm;
		}
		.hidden{
	    display:none;
		}

		.border-top {
			border-top: 1px dotted #999
		}

		table {
			font-size: 12px;
		}

	</style>
</head>
<body onload="window.print()">

<div class="kontainer">

    @include('layouts.kop-s1')
    <hr>

	<center>
		<h3><b>JADWAL UJIAN<br>TAHUN AKADEMIK {{ Sia::sessionPeriode('nama') }}</b></h3>
	</center>

	<br>
	<table border="0" style="width: 100%" id="tbl">
		<tr>
			<td width="120">NIM</td>
			<td>: {{ $mhs->nim }}</td>
            <td>Program Studi</td>
            <td>: {{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
		</tr>
		<tr>
			<td>Nama Mahasiswa</td>
			<td>: {{ $mhs->nm_mhs }}</td>
            <td>Semester</td>
            <td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai, Session::get('smt_in_nilai')) }}</td>
		</tr>
		<tr>
			
		</tr>
	</table>

	<br>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th width="20px">No.</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Nama matakuliah</th>
                <th>SKS</th>
                <th>Kelas</th>
                <th>Ruang</th>
                <th>Dosen</th>
            </tr>
        </thead>
        <tbody align="center">

            @foreach( $jadwal as $r )
                <?php
                    $dsn = DB::table('dosen_mengajar as dm')
                        ->leftJoin('dosen as d', 'dm.id_dosen', 'd.id')
                        ->select('d.*')
                        ->where('dm.id_jdk', $r->id_jdk)
                        ->first();
                ?>
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ Rmt::hari($r->hari) }}<br>
                        {{ Carbon::parse($r->tgl_ujian)->format('d/m/Y') }}
                    </td>
                    <td>
                        {{ substr($r->jam_masuk,0,5) }}
                    </td>
                    <td align="left">
                        {{ $r->nm_mk }}
                    </td>
                    <td>{{ $r->sks_mk }}</td>
                    <td>{{ $r->kode_kls }}</td>
                    <td>{{ $r->nm_ruangan }}</td>
                    <td align="left"><?= Sia::namaDosen($dsn->gelar_depan, $dsn->nm_dosen, $dsn->gelar_belakang) ?></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>