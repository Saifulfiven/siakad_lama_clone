<!DOCTYPE html>
<html>
<head>
	<title>Cetak Absensi Ujian</title>

	<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
	<style>

		body {
			margin: 0mm 5mm 5mm 5mm;
            font-size: 13px;
		}
		.hidden{
	    display:none;
		}

		.border-top {
			border-top: 1px dotted #999
		}

		.kontainer {
			margin-top: {{ Sia::option('margin_kertas_kop') }}mm;
		}

        @media print  
        {
            .footer{
                page-break-inside: avoid;
            }
        }

	</style>
</head>
<body onload="window.print()">

<div class="kontainer">

	<p>Pada hari ini, {{ Rmt::hari($jdu->hari) }} {{ Rmt::tgl_indo($jdu->tgl_ujian) }} 
        bertempat di ruang {{ $jdu->nm_ruangan }} {{ Config::get('pt') }} telah dilaksanakan
        {{ $jdu->jenis_ujian == 'UTS' ? 'Ujian Tengah Semester' : 'Ujian Akhir Semester' }} :

	<br>
	<table border="0" style="width: 100%" id="tbl">
		<tr>
			<td width="70">Jurusan</td>
			<td>: {{ $jdu->id_prodi }} - {{ $jdu->nm_prodi }} ({{ $jdu->jenjang }})</td>

            <td width="65">Hari/Jam</td>
            <td>: {{ Rmt::hari($jdu->hari) }} / {{ substr($jdu->jam_masuk, 0,5) }} - {{ substr($jdu->jam_selesai, 0,5) }}</td>
		</tr>
		<tr>
			<td>Mata Kuliah</td>
			<td>: {{ $jdu->kode_mk }} - {{ $jdu->nm_mk }}</td>
            <td>Kelas</td>
            <td>: {{ $jdu->kode_kls }}</td>
		</tr>
		<tr>
			<td>Nama Dosen</td>
            <td>: {{ $jdu->dosen }}</td>
            <td>Ruangan</td>
            <td>: {{ $jdu->nm_ruangan }}</td>
		</tr>
        <tr>
            <td>Semester</td>
            <td>: {{ $jdu->smt }}</td>
            <td>Jml Peserta</td>
            <td>: {{ $jdu->jml_peserta }}</td>
        </tr>
	</table>

	<br>

    <table width="100%" border="1" class="table table-bordered table-striped">
        <thead class="custom">
            <tr>
                <th width="10">No</th>
                <th width="80">NIM</th>
                <th>Nama Mahasiswa</th>
                @if ( $jdu->jenis_ujian == 'UTS' )
                    <th>Tanda Tangan</th>
                    <th>Teori</th>
                    <th>Praktek</th>
                @else
                    <th>Tanda Tangan</th>
                    <th>Nilai</th>
                @endif
            </tr>
        </thead>

        <tbody>
            <?php $no = 1 ?>
            <?php foreach( $peserta_ujian as $r ) { ?>
                <tr>
                    <td align="center"><?= $no++ ?></td>   
                    <td><?= $r->nim ?></td> 
                    <td><?= $r->nm_mhs ?></td>
                    @if ( $jdu->jenis_ujian == 'UTS' )
                        <td></td>
                        <td></td>
                        <td></td>
                    @else
                        <td></td>
                        <td align="center" width="160">
                            @foreach( $skala_nilai as $sn )
                                {{ $sn->nilai_huruf }} {{ $loop->last ? '' : '&nbsp; &nbsp; &nbsp;' }} 
                            @endforeach
                        </td>
                    @endif
                </tr>
            <?php } ?>  
        </tbody>
    </table>

    <div class="footer">

        <br>
        <br>

        <table border="0" width="100%">
            <tr>
                <td><b>Pengawas Ujian</b></td>
                <td align="center">Makassar, {{ Rmt::hari($jdu->hari) }} {{ Rmt::tgl_indo($jdu->tgl_ujian) }}<br>
                Dosen Pengasuh</td>
            </tr>
            <tr>
                <td>1. &nbsp;....................................................</td>
                <td></td>
            <tr>
                <td>2. &nbsp;....................................................</td>
                <td></td>
            </tr>
            <tr>
                <td>Catatan : </td>
                <td align="center">{{ $jdu->dosen }}</td>
            </tr>
        </table>

    </div>
</div>

</body>
</html>