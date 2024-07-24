@extends('layouts.app')

@section('title','Kuis : Jawaban Peserta')

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
        <li><a href="{{ route('kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}">Detail Kuis</a></li>
        <li class="active">Jawaban</li>
    </ol>

    <div id="content">
      <div class="row">
        <div class="col-md-12">

            {{ Rmt::alertError() }}

          <section class="panel">
            <header class="panel-heading">
                {{ $kuis->judul }}
                <a href="{{ route('kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" class="btn btn-default btn-sm pull-right">Kembali</a>
            </header>
            <div class="panel-body" style="padding-top: 13px">
                {!! $kuis->ket !!}
                <hr>

                <div class="row">
                    <div class="col-md-12">

                      <!--   <div class="pull-right">
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-panduan"><i class="fa fa-question-circle"></i> Petunjuk Penilaian</button>
                        </div> -->
                        <div class="alert alert-info">
                            Klik pada nama mahasiswa untuk melihat jawaban atau mengubah penilaian (apabila soal essay)
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Peserta Kelas</th>
                                        <th>Dikerjakan</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach( $peserta_kelas as $ps )
                                        <?php

                                            $peserta = $ps->nim.' - '. trim($ps->nm_mhs);

                                            $nilai = DB::table('lmsk_kuis_hasil as kh')
                                                    ->leftJoin('lmsk_kuis_soal as ks', 'kh.id_kuis_soal', 'ks.id')
                                                    ->leftJoin('lmsk_kuis as k', 'k.id', 'ks.id_kuis')
                                                    ->where('k.id', $kuis->id)
                                                    ->where('kh.id_peserta', $ps->id_mhs_reg)
                                                    ->sum('kh.penilaian');

                                            $dikerjakan = Sia::telahMengerjakanKuis($ps->id_mhs_reg, $kuis->id);

                                        ?>
                                        <tr>
                                            <td align="center">{{ $loop->iteration }}</td>
                                            <td><a href="{{ route('kuis_jawaban_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id, 'id_peserta' => $ps->id_mhs_reg]) }}">{{ $peserta }}</a></td>
                                            <td align="center">
                                                <?= $dikerjakan > 0 ? '<i class="fa fa-check" style="color: green"' : '<i class="fa fa-ban" style="color: red"' ?>
                                            </td>
                                            <td align="center">
                                                {{ number_format($nilai,2) }}
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

    @include('dsn.lms.modal-file', ['formAction' => route('dsn_lms_upload_tmp')])

@endsection

@section('registerscript')

<link href="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.css" rel="stylesheet"/>
<script src="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.min.js"></script>
<script>

    $(function () {
        'use strict';

        $('.nilai').editable({
            url: '{{ route('dsn_lms_tugas_grade') }}',
            name: 'nilai',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                params.id_tugas = '<?= $kuis->id ?>';
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
            url: '{{ route('dsn_lms_tugas_grade') }}',
            name: 'comment',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                params.id_tugas = '<?= $kuis->id ?>';
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
            url: '{{ route('dsn_lms_tugas_jawaban') }}',
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