@extends('mobile.layouts.app')

@section('title','Ubah Video')

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
                Ubah Video: <u>{{ $r->nm_mk }}</u>
                <div class="pull-right">
                    <a href="{{ route('dsnm_lms', ['id_jdk' => $r->id, 'id_dosen' => Session::get('m_id_dosen')]) }}" class="btn-loading btn btn-success btn-xs">Kembali</a>
                </div>
            </header>
            <div class="panel-body" style="padding-top: 13px">

                <form action="{{ route('m_video_update') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $video->id }}">
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">

                    <input type="hidden" name="pertemuan" value="{{ Request::get('prt') }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Judul <span>*</span></label>
                                <input type="text" class="form-control" name="judul" value="{{ $video->judul }}">
                            </div>

                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label class="control-label"> Keterangan (Opsional)</label>
                                <div>
                                    <textarea cols="6" id="ket" class="form-control" name="ket" rows="5">{{ $video->ket }}</textarea>
                                </div>
                            </div>

                            
                        </div>
                        <div class="col-md-4" id="id-video">
                            <div class="form-group">
                                <label class="control-label">ID Video Youtube
                                <span class="petunjuk" title="ID Video Youtube di area kotak merah.<br><img src='{{ url('resources') }}/assets/img/id-video.jpg'>">
                                        <i class="fa fa-question-circle"></i>
                                    </span>
                                </label>
                                <div>
                                    <input type="text" name="id_video" class="form-control" value="{{ $video->video_id }}">
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                    
                        <div class="col-md-12">
                            <hr>
                            <button class="btn btn-primary btn-sm" id="btn-submit-add" ><i class="fa fa-floppy-o"></i> Simpan</button> &nbsp; &nbsp; 
                            <a href="{{ route('dsn_lms', ['id' => $r->id]) }}" class="btn btn-default btn-sm"><i class="fa fa-list"></i> Batal</a>
                        </div>

                    </div>
                </form>

            </div>

        </div>
      </div>
    </div>

@endsection

@section('registerscript')

<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

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
                    window.location.href="{{ route('dsnm_lms') }}?id_jdk={{ $r->id }}&id_dosen={{ Session::get('m_id_dosen') }}";
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