<!DOCTYPE html>
<html>
<head>
    <title>Cetak Potongan biaya kuliah</title>

    <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
    <style>
        @media print  
        {
            .footer{
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">


    <center>
    <h4>Laporan Potongan Biaya Kuliah Mahasiswa<br>
        SEKOLAH TINGGI ILMU EKONOMI (STIE)</h4>

    <h2><b>NOBEL INDONESIA</b></h2>
    </center>

    <div class="garis-1"></div>
    <div class="garis-2"></div>
    <br>

    <table border="0">
        <tr>
            <td>Tahun Akademik</td>
            <td> : {{ $smt->nm_smt }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td> : {{ $prodi }}</td>
        </tr>
    </table>
    
    <br>

    <table border="1" width="100%">
        <thead class="custom">
                <tr>
                    <th width="20px">No.</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Jenis Potongan</th>
                    <th>Jumlah Potongan</th>
                    <th>Ket</th>
                </tr>
        </thead>
        <tbody align="center">
            @foreach($mahasiswa as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td width="100">{{ $r->nim }}</td>
                    <td align="left">{{ $r->nm_mhs }}</td>
                    <td align="left">{{ $r->jenjang .' '. $r->nm_prodi }}</td>
                    <td>{{ $r->jenis_potongan }}</td>
                    <td align="right">{{ 'Rp '.Rmt::rupiah($r->potongan) }}</td>
                    <td align="left">{{ $r->ket }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    @include('keuangan.footer')

</body>
</html>