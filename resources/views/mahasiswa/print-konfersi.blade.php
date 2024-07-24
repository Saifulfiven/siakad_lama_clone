<!DOCTYPE html>
<html>
<head>
	<title>Cetak Nilai Transfer</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
</head>
<body onload="window.print()">


<div class="col-md-12">
	<center>
	<h4>NILAI KONFERSI<br>
		SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

	<h2><b>NOBEL INDONESIA</b></h2>
	</center>

	<div class="garis-1"></div>
	<div class="garis-2"></div>
	<br>

	<table border="0" class="table" width="100%" id="tbl">
		<tr>
			<td width="150">NIM</td>
			<td>: {{ Session::get('konfersi_data')[1] }}</td>
			<td>Program Studi</td>
            <td>: {{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
		</tr>
		<tr>
			<td>Nama Mahasiswa</td>
			<td>: {{ $mhs->nm_mhs }}</td>
			<td>Angkatan</td>
			<td>: {{ substr($mhs->semester_mulai, 0, 4) }}</td>
		</tr>
	</table>

	<br>

    <table border="1" width="100%">
        <thead class="custom">
	        <tr>
	            <th rowspan="2" style="width:5%">No.</th>
	            <th colspan="4">Nilai PT Asal </th>
	            <th colspan="5">Konversi Nilai PT Baru (diakui)</th>
	        </tr>
	        <tr>
	          <th>Kode MK</th>
	          <th>Nama MK</th>
	          <th>SKS</th>
	          <th>Nilai<br>Huruf</th>
	          <th>Kode MK</th>
	          <th>Nama MK</th>
	          <th>SKS</th>
	          <th>Nilai <br>Huruf</th>
	          <th>Nilai<br>Angka</th>
	        </tr>
        </thead>

        <tbody align="center">
            @if ( $nilai->count() > 0 )

                <?php $tot_sks_t = 0 ?>
                <?php $tot_sks_diakui = 0 ?>

                @foreach( $nilai as $r )
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $r->kode_mk_asal }}</td>
                        <td>{{ $r->nm_mk_asal }}</td>
                        <td>{{ $r->sks_asal }}</td>
                        <td>{{ $r->nilai_huruf_asal }}</td>
                        <td>{{ $r->kode_mk }}</td>
                        <td>{{ $r->nm_mk }}</td>
                        <td>{{ $r->sks_mk }}</td>
                        <td>{{ $r->nilai_huruf_diakui }}</td>
                        <td>{{ number_format($r->nilai_indeks,2) }}</td>
                    </tr>
                    <?php $tot_sks_t += $r->sks_asal ?>
                    <?php $tot_sks_diakui += $r->sks_mk ?>
                @endforeach

                <tr>
                    <td colspan="3">Jumlah SKS</td>
                    <td>{{ $tot_sks_t }}</td>
                    <td colspan="3"></td>
                    <td>{{ $tot_sks_diakui }}</td>
                    <td colspan="3"></td>
                </tr>

            @else
                <tr><td colspan="11">Belum ada data</td></tr>
            @endif
        </tbody>
    </table>

	<footer></footer>
</div>

</body>
</html>