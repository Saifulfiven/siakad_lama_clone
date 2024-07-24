@extends('mobile.layouts.app')

@section('title','Tambah Catatan')

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
                Tambah Catatan: <u>{{ $r->nm_mk }}</u>
            </header>
            <div class="panel-body" style="padding-top: 13px">

                <form action="{{ route('dsnm_lms_catatan_store') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">

                    <input type="hidden" name="pertemuan" value="{{ Request::get('prt') }}">
                    <input type="hidden" name="id_dosen" value="{{ Request::get('id_dosen') }}">

                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group">
                                <label class="control-label"> Konten <span>*</span></label>
                                <div>
                                    <textarea cols="10" id="deskripsi" class="form-control" name="konten" rows="12"></textarea>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <a href="{{ route('dsnm_lms', ['id_jdk' => $r->id, 'id_dosen' => Request::get('id_dosen')]) }}" style="margin: 3px 3px" class="btn btn-default btn-sm btn-loading"><i class="fa fa-arrow-left"></i> Batal / Kembali</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit-add" onclick="CKupdate()" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> Simpan</button>
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
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/ckeditor-4-full/ckeditor.js"></script>
<script>

    $(function () {
        'use strict';
        
        $.get('/ckfinder.php?dsn=<?= Request::get('id_dosen') ?>');

        var editor = CKEDITOR.replace( 'deskripsi', {
            startupFocus : true,
            uiColor: '#FFFFFF',
            customConfig: '/resources/assets/plugins/ckeditor-4-full/custom_config.js'
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
                    window.location.href="{{ route('dsnm_lms', ['id_jdk' => $r->id]) }}&id_dosen={{ Request::get('id_dosen') }}";
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

    function CKupdate(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances[instance].updateElement();
    }

</script>
@endsection