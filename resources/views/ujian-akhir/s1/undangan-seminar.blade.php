<!DOCTYPE html>
<html>

<head>
  <title>Undangan Seminar</title>

  <link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/print.css" />
  <style>
    body {
      /*margin: 15mm 15mm 0mm 15mm;*/
      margin: 5mm 10mm 0mm 10mm;
      font-size: 15px;
    }

    table {
      line-height: .8em;
      font-size: 15px;
    }

    table.kop {
      line-height: 1.5em;
    }

    footer {
      page-break-after: always;
    }

    ul {
      list-style: circle;
      padding-left: 20px;
    }

    .lampiran {
      margin-top: 40px;
    }

    .stempel-box {
      position: relative;
    }

    img.stempel {
      width: 120px;
      position: absolute;
      left: -70px;
      top: 40px;
    }
  </style>
</head>

<body onload="window.print()">

  <?php

  if (empty($skripsi)) {
    echo '<h2>Pastikan Tanggal dan Waktu seminar telah diinput.</h2>';
    exit;
  }

  $ujian = Request::get('jenis') == 'P' ? 'Undangan Seminar Proposal' : 'Undangan Seminar Hasil';
  $ujian = Request::get('jenis') == 'S' ? 'Undangan Ujian Tutup' : $ujian;

  $today = Carbon::today()->format('Y-m-d');
  $bulan = Carbon::today()->format('m');;
  $tahun_sk = Carbon::today()->format('Y');
  $jenis = Request::get('jenis') == 1 ? '' : 'Pendek';
  $prodi = DB::table('prodi')->where('id_prodi', 61101)->first();
  ?>
  <div class="kontainer">

    {{-- @include('layouts.kop-s1') --}}
    <img src="{{ url('resources') }}/assets/img/new-kop.png" width="100%">
    <hr>
    <table border="0" style="width: 100%" id="tbl">
      <tr>
        <td width="120">Nomor</td>
        <td width="10">:</td>
        <td><?= Request::get('nomor') ? Request::get('nomor') : '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ' ?>/FTB/ITB-NI/<?= Rmt::romawi($bulan) ?>/<?= $tahun_sk ?></td>
        <td><span style="float: right">Makassar, {{ Rmt::tgl_indo(Carbon::now()->format('Y-m-d')) }}</span></td>
      </tr>
      <tr>
        <td>Lampiran</td>
        <td>:</td>
        <td colspan="2">1 (satu) Berkas</td>
      </tr>
      <tr>
        <td>Perihal</td>
        <td>:</td>
        <td colspan="2"><b><u>{{ $ujian }}</u></b></td>
      </tr>
    </table>

    <br>

    Kepada Yth:<br>
    Para Pembimbing, Penguji dan Mahasiswa Peserta Seminar<br>
    Di-
    <br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Makassar

    <p>Dengan hormat, kami mengundang kehadiran saudara/i untuk menghadiri seminar penelitian yang Insya Allah akan dilaksanakan pada :</p>

    <table width="100%">
      <tr>
        <td width="200">Hari/Tanggal</td>
        <td>:</td>
        <td>{{ Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')) }} / {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}</td>
      </tr>
      <tr>
        <td>Pukul</td>
        <td>:</td>
        <td>{{ $skripsi->pukul }} Wita</td>
      </tr>
      <tr>
        <td>Tempat</td>
        <td>:</td>
        <td>{{ empty($skripsi->ruangan) ? ' Lantai 5': $skripsi->ruangan  }} Kampus {{ config('app.itb_long') }} </td>
      </tr>
      <tr>
        <td colspan="3"><br><b>Yang akan disajikan oleh</b><br></td>
      </tr>
      <tr>
        <td>Nama Mahasiswa / NIM</td>
        <td>:</td>
        <td>{{ $mhs->nm_mhs }} - {{ $mhs->nim }}</td>
      </tr>
      <tr>
        <td>Program Studi</td>
        <td>:</td>
        <td>{{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
      </tr>
      <tr>
        <td valign="top">Pembimbing</td>
        <td valign="top">:</td>
        <td>Lihat Lampiran</td>
      </tr>
      <tr>
        <td valign="top">Penguji</td>
        <td valign="top">:</td>
        <td>Lihat Lampiran</td>
      </tr>
    </table>

    <p>Atas perhatian dan kehadiran Bapak/Ibu kami ucapkan terima kasih.</p>

    <br>

    <div style="float: right;">
      <div class="stempel-box">
        <b>Fakultas Teknologi dan Bisnis (FTB) Nobel Indonesia</b><br> Wakil Dekan I Bidang Akademik<br><br>
        <img src="{{ url('resources/assets/img/ttd-mariah.png') }}" width="125"><br><br>

        <img class="stempel" src="{{ url('resources') }}/assets/img/stempel-itb2.png">

        {{-- <b><u>{{ Sia::option('ketua_1') }}</u></b>
        <br>NIDN. {{ Sia::option('nip_ketua_1') }} --}}
        <b><u>Mariah, SE., M. Pd</u></b>
        <br>NIDN. 0903018002
      </div>
    </div>

    <div style="margin-top: 200px">
      <p style="margin-bottom: 2px"><b>Catatan :</b></p>
      <ul>
        <li>Harap Mahasiswa Peserta Seminar hadir 30 menit sebelum seminar dimulai untuk mengantisipasi perubahan/penyesuaian jadwal dari yang telah ditetapkan.</li>
        <li>Harap Pembimbing dan Penguji hadir 15 menit sebelum seminar dimulai.</li>
        <li>Peserta seminar diwajibkan memakai Jas Almamater.</li>
        <li>Peserta menyiapkan peralatan presentasi dan materi presentasi dalam format power point.</li>
        <li>Undangan diantar ke Pembimbing dan Penguji paling lambat 3 hari sebelum jadwal seminar.</li>
        <li>Dilarang membawa hadiah dan sejenisnya.</li>
      </ul>

      <p style="margin-bottom: 2px"><i><b>Tembusan : </b></i></p>
      <ol style="margin-top: 0">
        <li><i>Ketua Yayasan Pendidikan Nobel Makassar</i></li>
        <li><i>Bendahara Yayasan Pendidikan Nobel Makassar</i></li>
        <li><i>File</i></li>
      </ol>
    </div>
  </div>
  <footer></footer>

  <div class="lampiran">
    <table width="100%">
      <tr>
        <td width="120">Lampiran Surat No.</td>
        <td width="10">:</td>
        <td><?= Request::get('nomor') ? Request::get('nomor') : '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ' ?>/FTB/ITB-NI/<?= Rmt::romawi($bulan) ?>/<?= $tahun_sk ?></td>
      </tr>
      <tr>
        <td>Nama Mahasiswa</td>
        <td>:</td>
        <td>{{ $mhs->nm_mhs }}</td>
      </tr>
      <tr>
        <td>NIM</td>
        <td>:</td>
        <td>{{ $mhs->nim }}</td>
      </tr>
      <tr>
        <td width="200">Hari/Tanggal</td>
        <td>:</td>
        <td>{{ Rmt::hari(Carbon::parse($skripsi->tgl_ujian)->format('N')) }} / {{ Rmt::tgl_indo($skripsi->tgl_ujian) }}</td>
      </tr>
      <tr>
        <td>Pukul</td>
        <td>:</td>
        <td>{{ $skripsi->pukul }} Wita</td>
      </tr>
      <tr>
        <td>Tempat</td>
        <td>:</td>
        <td>{{ empty($skripsi->ruangan) ? ' Lantai 5': $skripsi->ruangan  }} Kampus {{ config('app.itb_long') }} </td>
      </tr>
      <tr>
        <td>Program Studi</td>
        <td>:</td>
        <td>{{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
      </tr>
      <tr>
        <td valign="top" style="line-height: 1.5em">Judul Skripsi</td>
        <td valign="top" style="line-height: 1.5em">:</td>
        <td valign="top" style="line-height: 1.5em">{{ $skripsi->judul_tmp }}</td>
      </tr>
      <tr>
        <td valign="top">Pembimbing</td>
        <td valign="top">:</td>
        <td valign="top">
          @foreach( $penguji as $pe )
          @if ( $pe->jabatan == 'KETUA' || $pe->jabatan == 'SEKRETARIS' )
          <p style="margin-top: 0">{{ $pe->penguji }}</p>
          @endif
          @endforeach

        </td>
      </tr>
      <tr>
        <td valign="top">Penguji</td>
        <td valign="top">:</td>
        <td valign="top">
          @foreach( $penguji as $pe )
          @if ( $pe->jabatan == 'ANGGOTA' || $pe->jabatan == 'ANGGOTA2' )
          <p style="margin-top: 0">{{ $pe->penguji }}</p>
          @endif
          @endforeach

        </td>
      </tr>
    </table>
    <br>

    {{-- <div style="float: right;">
      <div class="stempel-box">
        <b>{{ config('app.itb_long') }}</b> Makassar<br> Wakil Rektor I Bidang Akademik<br><br>
        <img src="{{ url('resources/assets/img/ttd-firman.png') }}" width="150"><br><br>

        <img class="stempel" src="{{ url('resources') }}/assets/img/stempel-itb2.png">

        <b><u>{{ Sia::option('ketua_1') }}</u></b>
        <br>Nip. {{ Sia::option('nip_ketua_1') }}
      </div>
    </div> --}}
    <div style="float: right;">
      <div class="stempel-box">
        <b>Fakultas Teknologi dan Bisnis (FTB) Nobel Indonesia</b><br> Wakil Dekan I Bidang Akademik<br><br>
        <img src="{{ url('resources/assets/img/ttd-mariah.png') }}" width="150"><br><br>

        <img class="stempel" src="{{ url('resources') }}/assets/img/stempel-itb2.png">

        {{-- <b><u>{{ Sia::option('ketua_1') }}</u></b>
        <br>NIDN. {{ Sia::option('nip_ketua_1') }} --}}
        <b><u>Mariah, SE., M. Pd</u></b>
        <br>NIDN. 0903018002
      </div>
    </div>
  </div>
</body>

</html>