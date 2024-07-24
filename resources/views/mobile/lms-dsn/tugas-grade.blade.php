@extends('mobile.layouts.app')

@section('title','Tugas Detail')

@section('content')
    
    <?php $id_dosen = Request::get('id_dosen'); ?>

    <div id="content">
      <div class="row">
        <div class="col-md-12">

            {{ Rmt::alertError() }}

          <section class="panel">
            <header class="panel-heading">
                {{ $tugas->judul }}
                <a href="javascript:;" onclick="goBack()" class="btn btn-default btn-xs pull-right">Kembali</a>
                <!-- <a href="{{ route('dsnm_lms_tugas_detail', ['id_jadwal' => $r->id, 'id' => $tugas->id, 'id_dosen' => $id_dosen]) }}" class="btn btn-default btn-xs pull-right">Kembali</a> -->
                
            </header>
            <div class="panel-body" style="padding-top: 13px">
                {!! $tugas->deskripsi !!}

                @if ( !empty($tugas->file) )
                    <div class="icon-resources">
                        <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ Rmt::icon($tugas->file) }}" />
                    </div>
                    <a href="{{ route('m_lms_tugas_view_attach', ['id_tugas' => $tugas->id, 'id_dosen' => $id_dosen, 'file' => $tugas->file]) }}" target="_blank">{{ $tugas->file }}</a>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-left">
                            @if ( $tugas->jenis_pengiriman == 'text' )
                                <button onclick="alert('Tugas ini adalah online text, silahkan lihat jawaban pada kolom JAWABAN pada table di bawah ini')" class="btn btn-primary">Download Semua File</button>
                            @else
                                <a href="{{ route('dsnm_lms_tugas_download', ['id_jadwal' => $r->id, 'id_tugas' => $tugas->id, 'judul' => $r->nm_mk.' - '.$tugas->judul]) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-download"></i>Download Semua File</a>
                            @endif
                        </div>

                        <div class="pull-right">
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-panduan"><i class="fa fa-question-circle"></i> Petunjuk Penilaian</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Peserta Kelas</th>
                                        <th>File</th>
                                        <th>Jawaban</th>
                                        <th>Tgl kirim</th>
                                        <th>Nilai</th>
                                        <th width="200">Komentar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach( $peserta_kelas as $ps )
                                        <?php

                                            $peserta = $ps->nim.' - '. trim($ps->nm_mhs);

                                            $jawab = DB::table('lms_jawaban_tugas')
                                                    ->where('id_tugas', $tugas->id)
                                                    ->where('id_peserta', $ps->id_mhs_reg)
                                                    ->first();

                                            $comment = !empty($jawab) && !empty($jawab->comment) ? $jawab->comment : '';

                                            $icon = !empty($jawab) && !empty($jawab->file) ? Rmt::icon($jawab->file) : '';

                                            $nilai = !empty($jawab) && !empty($jawab->nilai) ? $jawab->nilai : '';
                                            
                                            $jawaban = !empty($jawab) && !empty($jawab->jawaban) ? $jawab->jawaban : '';

                                            $tgl_kirim = !empty($jawab) && !empty($jawab->tgl_kumpul) ? Rmt::format_tgl($jawab->tgl_kumpul, 'd/m/Y') : '';
                                            $jam_kirim = !empty($jawab) && !empty($jawab->tgl_kumpul) ? substr($jawab->tgl_kumpul,11) : '';

                                            // Keterlambatan
                                            $status = false;
                                            if ( !empty($jawab) && !empty($jawab->tgl_kumpul) ) {
                                                if ( !empty($tugas->tgl_berakhir) && ($jawab->tgl_kumpul > $tugas->tgl_berakhir) ) {
                                                    $status = true;
                                                }
                                            } 

                                        ?>
                                        <tr<?= $status ? ' style="background: #f2dede"':'' ?>>
                                            <td align="center">{{ $loop->iteration }}</td>
                                            <td>{{ $peserta }}</td>
                                            <td>
                                                @if ( !empty($icon) )
                                                    <div class="icon-resources">
                                                        <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                                    </div>
                                                    <a href="{{ route('dsnm_lms_tugas_download_single', $tugas->id) }}?id_jadwal={{ $r->id }}&nim={{ $ps->nim }}&nama={{ $ps->nm_mhs }}&file={{ $jawab->file }}" target="_blank">{{ str_limit($jawab->file,20) }}</a>
                                                @endif
                                            </td>
                                            <td align="center">
                                                <span class="tooltip-area">

                                                    @if ( in_array($tugas->jenis_pengiriman, ['text','all']) && !empty($jawaban) )
                                                        <button class="btn btn-primary btn-sm" title="Lihat jawaban peserta" onclick="jawaban('{{ $jawab->id }}', '{{ $peserta }}')"><i class="fa fa-comment"></i></button>
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                            <td align="center"><span<?= $status ? ' style="color: #a94442"':'' ?>>{{ $tgl_kirim.' '.$jam_kirim }}</span></td>
                                            <td align="center">
                                                <a href="#" class="nilai" data-type="text" data-pk="{{ $ps->id_mhs_reg }}" data-title="Masukkan Nilai (Rentang 1 - 100)">{{ $nilai }}</a>
                                            </td>
                                            <td>
                                                <a href="#" class="komentar" data-type="textarea" data-pk="{{ $ps->id_mhs_reg }}" data-title="Masukkan Komentar">{{ $comment }}</a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>


    <div id="modal-panduan" class="modal fade" data-width="700" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Panduan memberikan penilaian</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <iframe class="tscplayer_inline" style="width: 100%;height: 500px" name="tsc_player" src="{{ url('storage') }}/panduan/input-nilai-lms/input-nilai-lms_player.html" scrolling="no" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        </div>
    </div>

    <div id="modal-jawaban" class="modal fade" data-width="700" tabindex="-1" style="top: 20%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title"></h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">

        </div>
    </div>

@endsection

@section('registerscript')

<link href="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.css" rel="stylesheet"/>
<script src="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.min.js"></script>
<script>

    $(function () {
        'use strict';

        $('.nilai').editable({
            url: '{{ route('dsnm_lms_tugas_grade') }}',
            name: 'nilai',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                params.id_tugas = '<?= $tugas->id ?>';
                return params;
            },
            success: function(response, newValue) {
                showSuccess('Berhasil menyimpan data');
            },
            error: function(response,value)
            {
                console.log(JSON.stringify(response));
                var respon = parseObj(response.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = response.statusText;
                }
                showMessage2('', pesan);
            }
        });

        $('.komentar').editable({
            url: '{{ route('dsnm_lms_tugas_grade') }}',
            name: 'comment',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                params.id_tugas = '<?= $tugas->id ?>';
                return params;
            },
            success: function(response, newValue) {
                showSuccess('Berhasil menyimpan data');
            },
            error: function(response,value)
            {
                var respon = parseObj(response.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2('', pesan);
            }
        });
        
    });

    function jawaban(id_jawaban, peserta)
    {
        $('#modal-jawaban').modal('show');
        $('#modal-jawaban .modal-body').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
        $('#modal-jawaban .modal-title').html('Detail Jawaban <b>'+peserta+'</b>');

        $.ajax({
            url: '{{ route('dsnm_lms_tugas_jawaban') }}',
            data : { 
                preventCache : new Date(), 
                id_jawaban: id_jawaban
            },
            success: function(data){
                $('#modal-jawaban .modal-body').html(data);
                $('#modal-jawaban').modal('show');
            },
            error: function(data,status,msg){
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = msg;
                }
                showMessage2('',pesan);
            }
        });
        
    }
</script>
@endsection