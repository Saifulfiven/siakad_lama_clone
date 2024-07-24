@extends('layouts.app')

@section('title','Tugas Detail')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>{{ $r->kode_kls .' - '.$r->nm_mk }}</a></li>
    </ul>
@endsection

@section('content')
    <ol class="breadcrumb">
        <li><a href="{{ route('dsn_jadwal') }}">Matakuliah</a></li>
        <li><a href="{{ route('dsn_lms', ['id' => $r->id]) }}">{{ $r->kode_kls .' - '.$r->nm_mk }}</a></li>
        <li class="active">Tugas Detail</li>
    </ol>

    <div id="content">
      <div class="row">
        <div class="col-md-12">
            <section class="panel">
                <header class="panel-heading">
                    {{ $tugas->judul }}

                    @if ( $tugas->jenis == 'ujian' )
                        <span class="badge bg-danger">UJIAN</span>
                    @endif
                    
                    <div class="pull-right">
                        <a href="{{ route('dsn_lms_tugas_add', ['id' => $r->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah</a>
                        <a href="{{ route('dsn_lms_tugas_edit', ['id' => $r->id, 'id_tugas' => $tugas->id]) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a>
                        <a href="{{ route('dsn_lms', ['id' => $r->id]) }}" class="btn btn-default btn-xs">Kembali</a>
                    </div>
                </header>
                <div class="panel-body" style="padding-top: 13px">
                    {!! $tugas->deskripsi !!}

                    @if ( !empty($tugas->file) )
                        <div class="icon-resources">
                            <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ Rmt::icon($tugas->file) }}" />
                        </div>
                        <a href="{{ route('dsn_lms_tugas_view_att', ['id_tugas' => $tugas->id, 'id_dosen' => Sia::sessionDsn(), 'file' => $tugas->file]) }}" target="_blank">{{ $tugas->file }}</a>
                    @endif

                    <div class="row">
                        <div class="col-md-6" style="margin-top: 20px;">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td width="200">Jumlah Peserta</td>
                                        <?php
                                            $peserta_undangan = App\PesertaUndangan::where('id_jadwal', $r->id)
                                                ->count(); ?>
                                        <td>: {{ Sia::jmlPesertaKelas($r->id) + $peserta_undangan }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah yang telah mengirim</td>
                                        <td>: {{ Sia::jumlahPengirimTugas($tugas->id) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Mulai</td>
                                        <td>: {{ !empty($tugas->mulai_berlaku) ? Rmt::tgl_indo($tugas->mulai_berlaku).' '.substr($tugas->mulai_berlaku, 11) .' WITA' : 'Tidak terbatas' }} </td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Berakhir</td>
                                        <td>: {{ !empty($tugas->tgl_berakhir) ? Rmt::tgl_indo($tugas->tgl_berakhir).' '.substr($tugas->tgl_berakhir, 11) .' WITA' : 'Tidak terbatas' }} </td>
                                    </tr>
                                    <tr>
                                        <td>Sisa waktu</td>
                                        <td>: 
                                            @if ( !empty($tugas->tgl_berakhir) )
                                                <span id="time-remaining" style="font-size: 18px;"><i class="fa fa-spin fa-spinner"></i></span>
                                            @else
                                                Tidak terbatas
                                            @endif
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>

                        <div class="col-md-5" style="margin-top: 20px">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td colspan="2"><b>Pengaturan Tugas</b></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Pengajuan</td>
                                        <td>{{ Sia::jenisPengiriman($tugas->jenis_pengiriman) }}</td>
                                    </tr>

                                    @if ( $tugas->jenis_pengiriman <> 'text' )
                                        <tr>
                                            <td>Maksimum Ukuran file</td>
                                            <td>{{ Sia::listSize($tugas->max_file_upload) }}</td>
                                        </tr>
                                    @endif

                                    @if ( $tugas->jenis_pengiriman <> 'file' )
                                        <tr>
                                            <td>Minimal Jumlah Karakter</td>
                                            <td>{{ $tugas->min_teks }}</td>
                                        </tr>
                                        <tr>
                                            <td>Maksimal Jumlah Karakter</td>
                                            <td>{{ $tugas->max_teks }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>Batasan Upload</td>
                                        <td>{{ empty($tugas->max_attempt) ? 'Tidak dibatasi': $tugas->max_attempt.' kali' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <a href="{{ route('dsn_lms_tugas_detail', ['id_jadwal' => $r->id, 'id' => $tugas->id]) }}?act=grade" class="btn btn-primary btn-sm">Lihat Kiriman Peserta</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
      </div>
    </div>

    @include('dsn.lms.modal-file', ['formAction' => route('dsn_lms_upload_tmp')])

@endsection

@section('registerscript')

<script>

    $(function () {
        'use strict';

    });

    @if ( !empty($tugas->tgl_berakhir) )

        // Set the date we're counting down to
        var countDownDate = new Date("{{ $tugas->tgl_berakhir }}").getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

          // Get today's date and time
          var now = new Date("{{ Carbon::now()->format('Y-m-d H:i:s') }}").getTime();
            
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
            
          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
          document.getElementById("time-remaining").innerHTML = days + " hari " + hours + " jam "
          + minutes + " menit " + seconds + " detik ";
            
          // If the count down is over, write some text 
          if (distance < 0) {
            clearInterval(x);
            document.getElementById("time-remaining").innerHTML = "<b style='color: red'>EXPIRED</b>";
          }
        }, 1000);
    @endif

</script>
@endsection