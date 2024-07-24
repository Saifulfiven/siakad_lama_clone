@extends('mobile.layouts.app')

@section('title','Tugas Detail')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection


@section('content')
    
    <?php $id_dosen = Request::get('id_dosen') ?>

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
                        <a href="{{ route('dsnm_lms_tugas_add', ['id' => $r->id, 'id_dosen' => $id_dosen]) }}" class="btn-loading btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah</a>
                        <a href="{{ route('dsnm_lms_tugas_edit', ['id' => $r->id, 'id_dosen' => $id_dosen, 'id_tugas' => $tugas->id]) }}" class="btn-loading btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a>
                        <a href="{{ route('dsnm_lms', ['id_jdk' => $r->id, 'id_dosen' => $id_dosen]) }}" class="btn-loading btn btn-default btn-xs">Kembali</a>
                    </div>
                </header>
                <div class="panel-body" style="padding-top: 13px">
                    {!! $tugas->deskripsi !!}

                    @if ( !empty($tugas->file) )
                        <div class="icon-resources">
                            <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ Rmt::icon($tugas->file) }}" />
                        </div>
                        <a href="{{ route('m_lms_tugas_view_attach', ['id_tugas' => $tugas->id, 'id_dosen' => $id_dosen, 'file' => $tugas->file]) }}" target="_blank">{{ $tugas->file }}</a>
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
                                        <td>: {{ Rmt::jmlPesertaKelas($r->id) + $peserta_undangan }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah yang telah mengirim</td>
                                        <td>: {{ Rmt::jumlahPengirimTugas($tugas->id) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Mulai</td>
                                        <td>: {{ !empty($tugas->mulai_berlaku) ? Rmt::tgl_indo($tugas->mulai_berlaku).' '.substr($tugas->mulai_berlaku, 11) : 'Tidak terbatas' }} </td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Berakhir</td>
                                        <td>: {{ !empty($tugas->tgl_berakhir) ? Rmt::tgl_indo($tugas->tgl_berakhir).' '.substr($tugas->tgl_berakhir, 11) : 'Tidak terbatas' }} </td>
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
                                        <td width="200">Jenis Pengajuan</td>
                                        <td>: {{ Rmt::jenisPengiriman($tugas->jenis_pengiriman) }}</td>
                                    </tr>

                                    @if ( $tugas->jenis_pengiriman <> 'text' )
                                        <tr>
                                            <td>Maksimum Ukuran file</td>
                                            <td>: {{ Rmt::listSize($tugas->max_file_upload) }}</td>
                                        </tr>
                                    @endif

                                    @if ( $tugas->jenis_pengiriman <> 'file' )
                                        <tr>
                                            <td>Minimal Jumlah Karakter</td>
                                            <td>: {{ $tugas->min_teks }}</td>
                                        </tr>
                                        <tr>
                                            <td>Maksimal Jumlah Karakter</td>
                                            <td>: {{ $tugas->max_teks }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>Batasan Upload</td>
                                        <td>: {{ empty($tugas->max_attempt) ? 'Tidak dibatasi': $tugas->max_attempt.' kali' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <a href="{{ route('dsnm_lms_tugas_detail', ['id_jadwal' => $r->id, 'id' => $tugas->id]) }}?act=grade&id_dosen={{ $id_dosen }}" class="btn btn-primary btn-sm btn-loading">Lihat Kiriman Peserta</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
      </div>
    </div>

    @include('mobile.lms-dsn.modal-file', ['formAction' => route('dsnm_lms_upload_tmp')])

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
          var now = new Date().getTime();
            
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