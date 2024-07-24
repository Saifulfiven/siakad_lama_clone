@extends('mobile.layouts.app')

@section('title','Ubah Materi')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
                Ubah Materi: <u>{{ $r->nm_mk }}</u>
            </header>
            <div class="panel-body" style="padding-top: 13px">
                
                <?php $id_bm = $materi->id_bm ?>

                <form action="{{ route('dsn_lms_materi_update') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                    <input type="hidden" name="id_bank_materi" id="id_bank_materi" value="{{ $materi->id_bm }}">
                    <input type="hidden" name="id_materi" value="{{ $materi->id }}">
                    <input type="hidden" name="nama_file" id="nama-file" value="{{ $materi->file }}">
                    <input type="hidden" class="sumber_materi" name="sumber_materi" value="dokumen">

                    <input type="hidden" name="pertemuan" value="{{ Request::get('prt') }}">
                    <input type="hidden" name="id_dosen" value="{{ Request::get('id_dosen') }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Judul <span>*</span></label>
                                <input type="text" class="form-control" name="judul" value="{{ $materi->judul }}">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Pilih File <span>*</span></label>
                                <br>
                                
                                <div class="input-group" data-target="#modal-file" data-toggle="modal">
                                    <input type="text" class="form-control" id="file_materi" disabled="" value="{{ $materi->file }}">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button"><i class="fa fa-file"></i> Ganti File</button>
                                    </span>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label"> Deskripsi (Opsional)</label>
                                <div>
                                    <textarea cols="10" class="form-control" name="deskripsi" rows="4">{{ $materi->deskripsi }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <a href="javascript:;" onclick="goBack()" style="margin: 3px 3px" class="btn btn-default btn-sm btn-loading"><i class="fa fa-arrow-left"></i> Batal / Kembali</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit-add" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> Simpan</button> &nbsp; &nbsp; 
                        </div>

                    </div>

                </form>

            </div>

        </div>
      </div>
    </div>

    @include('mobile.lms-dsn.modal-file', ['formAction' => route('dsnm_lms_upload_tmp')])

@endsection

@section('registerscript')

<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    Dropzone.options.dropzone = {
        maxFilesize: 16,
        dictDefaultMessage: 'Seret file ke sini atau klik',
        acceptedFiles: fileAccept(),
        success: function(file, response) 
        {
            pilih('<?= $id_bm ?>', file.name, 'upload');
            this.removeAllFiles();
        },
        error: function(file, response)
        {
            showMessage2('',response);
            this.removeAllFiles();
            return false;
        }
    };

    $(function () {
        'use strict';

        $('#modal-file').on('hidden.bs.modal', function(){
            $('#tab1').trigger('click');
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

    function pilih(id, file, sumber)
    {
        
        $('.sumber_materi').val(sumber);
        $('#id_bank_materi').val(id);

        $('#file_materi').val(file);
        $('#nama-file').val(file);

        $('#modal-file').modal('hide');
        $('#tab1').trigger('click');
    }

</script>
@endsection