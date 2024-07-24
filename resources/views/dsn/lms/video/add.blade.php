@extends('layouts.app')

@section('title','Tambah Video')

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
        <li class="active">Tambah Video</li>
    </ol>

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
                Tambah Video: <u>{{ $r->nm_mk }}</u>
            </header>
            <div class="panel-body" id="upload" style="padding-top: 13px">

                @if ( $telah_upload > 0 )
                    <div class="alert alert-info">
                        <b>Anda telah mengupload video hari ini</b>.<br>
                         Karena quota yang diberikan youtube kepada kami terbatas, kami hanya memberikan 1 kali kesempatan upload video untuk setiap dosen perhari.<br><br>

                        Namun anda masih bisa menambahkan video dengan menambahkan ID VIDEO dari youtube.
                    </div>

                @endif

                @if ( Session::has('id_video') )
                    <div class="alert alert-info">
                        Masih ada video yang belum selesai
                    </div>
                @endif

                <form action="{{ route('video_store') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id-resource" value="{{ Session::has('id_video') ? Session::get('id_video') : 0 }}">
                    <input type="hidden" id="id-resource-static" value="{{ Session::has('id_video') ? Session::get('id_video') : 0 }}">
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">

                    <input type="hidden" name="pertemuan" value="{{ Request::get('prt') }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Judul <span>*</span></label>
                                <input type="text" class="form-control" name="judul">
                            </div>

                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label class="control-label"> Keterangan (Opsional)</label>
                                <div>
                                    <textarea cols="6" id="ket" class="form-control" name="ket" rows="5"></textarea>
                                </div>
                            </div>

                            <input type="hidden" name="aksi" value="ambil">

                            <div class="col-md-4" id="id-video">
                                <div class="form-group">
                                    <label class="control-label">ID Video Youtube
                                    <span class="petunjuk" title="Buka video anda di youtube. ID Video Youtube di area kotak merah.<br><img src='{{ url('resources') }}/assets/img/id-video.jpg'>">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div>
                                        <input type="text" name="id_video" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                    </div>

                </form>

                
                <div class="row">
      
                    <div class="col-md-12">
                        <hr>
                        <button class="btn btn-primary btn-sm" id="btn-submit-add" ><i class="fa fa-floppy-o"></i> Simpan</button> &nbsp; &nbsp; 
                        <a href="javascript:void(0);" onclick="history.back(-1);" class="btn btn-default btn-sm"><i class="fa fa-list"></i> Batal</a>
                    </div>

                </div>

            </div>

            <div class="panel-body" id="upload-youtube" style="padding-top: 13px;display: none">
                <div class="row">
                    <div class="col-md-12">
                        <center>
                            <div id="spinner-upload">
                                <h2><i class="fa fa-spinner fa-spin"></i><br>Sedang mengupload ke youtube</h2><br>
                                <h4>Proses ini mungkin memerlukan waktu beberapa menit.</h4>
                            </div>
                            <div id="upload-error" style="display: none">
                                <h4>Terjadi kesalahan..</h4>
                                <br>
                                <button class="btn btn-primary"><i class="fa fa-upload"></i> Upload Lagi</button>

                                <hr>

                                <div class="alert alert-info" style="font-size: 12pt">
                                    <b>Catatan: </b>Apabila anda gagal pada tahap ini, sistem  akan secara otomatis mengupload video anda ke youtube. <br>Video anda akan segera ditampilkan apabila sistem telah selesai menguploadnya, silahkan menunggu. <br>Sistem melakukan pemeriksaan video yang gagal setiap 10 menit.
                                </div>
                            </div>
                        </center>
                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>

@endsection

@section('registerscript')

<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    Dropzone.options.dropzone = {
        maxFilesize: 200,
        dictDefaultMessage: 'Seret file ke sini atau klik',
        acceptedFiles: ".mp4,.api,.mkv",
        uploadprogress: function(response)
        {
            $('.progress-shine').show();
            $('.progress-bar').attr('style','width: '+response.upload.progress+'%');

        },
        drop: function(file)
        {
            $("#btn-submit-add").attr('disabled','');
        },
        success: function(file, response) 
        {
            var id = response.id;
            $('#id-resource').val(id);
            $('#id-resource-static').val(id);
            $('#form-upload').hide();
            $('#content-video').html(response.file);
            $('#uploaded-video').show();

            $('.progress-shine').hide();
            $('.progress-bar').attr('style','width: 0%');
            $("#btn-submit-add").removeAttr('disabled');
        },
        error: function(file, response)
        {
            $('.progress-shine').hide();
            $('.progress-bar').attr('style','width: 0%');
            $("#btn-submit-add").removeAttr('disabled');
            showMessage2('',response);
            this.removeAllFiles();
            return false;
        }
    };

    $(function () {
        'use strict';

        $('#btn-submit-add').click(function(){
            $('#form-add').submit();
        });

        var options = {
            beforeSend: function() 
            {
                $('#caplet-overlay').show();
                $("#btn-submit-add").attr('disabled','');
                $("#btn-submit-add").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage2('add', data.msg);
                } else {
                    if ( data.aksi === 'upload' ) {

                        $('#caplet-overlay').hide();
                        $('#upload').hide();
                        $('#upload-youtube').show();
                        uploadToYoutube(data.id, data.judul, data.file);

                    } else {
                        window.location.href="{{ route('dsn_lms', ['id' => $r->id]) }}";
                    }
                }
            },
            error: function(data, status, message)
            {
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                $("#close-error").show();
                showMessage2('add', pesan);
            }
        }; 

        $('#form-add').ajaxForm(options);
    });

    function uploadToYoutube(id,judul,file)
    {
        $('#upload-error').hide();
        $('#spinner-upload').show();

        $.ajax({
            url: '{{ url('/') }}/google/upload-youtube.php',
            type: 'POST',
            data: { id: id, dosen: '{{ Sia::sessionDsn('nama') }}', file: file, judul: judul, dsn: '{{ Sia::sessionDsn() }}' },
            success: function(result){
                console.log('Result: '+result);

                if ( result == 'Sukses' ) {

                    window.location.href="{{ route('dsn_lms', ['id' => $r->id]) }}?success=1";
                    
                } else {

                    $('#upload-error').show();
                    $('#spinner-upload').hide();
                    $('#upload-error button').attr('onclick','uploadToYoutube("'+id+'","'+judul+'","'+file+'")');
                    $('#upload-error h4').html('Terjadi kesalahan...<br>Ulangi lagi');
                }
            },
            error: function(data,status,msg){
                $('#upload-error').show();
                $('#spinner-upload').hide();
                $('#upload-error button').attr('onclick','uploadToYoutube("'+id+'","'+judul+'","'+file+'")');
                $('#upload-error h4').html('Terjadi kesalahan...<br>'+msg);
                console.log(msg);
            }
        });
    }

    function cekVideoId(id)
    {
        $.ajax({
            url: '{{ route('video_cek_id') }}',
            data: { id: id },
            success: function(result){
                return true;
            },
            error: function(data,status,msg){
                return false;
            }
        });
    }
    
    function aksiVideo(val){
        var id_vid = $('#id-resource-static').val();

        if ( val === 'upload' ) {
            $('#id-video').hide();
            $('#file-video').show();
            $('#id-resource').val(id_vid);
        } else {
            $('#id-resource').val(0);
            $('#id-video').show();
            $('#file-video').hide();
        }
    }

</script>
@endsection