@extends('layouts.app')

@section('title','Detail Mahasiswa Lulus/Keluar')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-8">
          <section class="panel">
            <header class="panel-heading">
              Detail Mahasiswa Lulus/Keluar
              <button onclick="window.history.back()" class="btn btn-success btn-xs pull-right">KEMBALI</button>
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <table class="table table-hover" width="100%">
                    <tr>
                        <td width="150">Mahasiswa</td>
                        <td>: {{ $mhs->nim.' '.$mhs->nm_mhs }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Keluar</td>
                        <td>: {{ $mhs->ket_keluar }}</td>
                    </tr>
                    <tr>
                        <td>Semester Lulus/Keluar</td>
                        <td>: {{ $mhs->nm_smt }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Keluar</td>
                        <td>: {{ Carbon::parse($mhs->tgl_keluar)->format('d-m-Y') }}</td>
                    </tr>
                    @if ( $mhs->id_jenis_keluar == 1 )
                        <tr>
                            <td>SK Yudisium</td>
                            <td>: {{ $mhs->sk_yudisium }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal SK Yudisium</td>
                            <td>: {{ Carbon::parse($mhs->tgl_sk_yudisium)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td>IPK</td>
                            <td>: {{ number_format($mhs->ipk,2) }}</td>
                        </tr>
                        <tr>
                            <td>PIN</td>
                            <td>: {{ $mhs->pin }}</td>
                        </tr>
                        <tr>
                            <td>Seri Ijazah</td>
                            <td>: {{ $mhs->seri_ijazah }}</td>
                        </tr>
                        <tr>
                            <td>Judul Skripsi</td>
                            <td>: {{ $mhs->judul_skripsi }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>Keterangan Keluar</td>
                            <td>: {{ $mhs->ket }}</td>
                        </tr>
                    @endif
                </table>

            </div>

        </div>
      </div>
    </div>
@endsection