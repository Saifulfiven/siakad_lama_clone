@extends('layouts.app')

@section('title','Ubah Soal Kuis')

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>{{ $kuis->judul }}</a></li>
    </ul>
@endsection

@section('content')
    <ol class="breadcrumb">
        <li><a href="{{ route('dsn_jadwal') }}">Matakuliah</a></li>
        <li><a href="{{ route('dsn_lms', ['id' => $r->id]) }}">{{ $r->kode_kls .' - '.$r->nm_mk }}</a></li>
        <li><a href="{{ route('kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}">Kuis: {{ substr($kuis->judul,0, 20) }}</a></li>
        <li class="active">Ubah Soal Kuis</li>
    </ol>

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
                Ubah Soal: <u>{{ $kuis->judul }}</u>

                <a href="{{ route('kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" class="btn btn-success btn-xs pull-right">Kembali</a>
            </header>
            <div class="panel-body" style="padding-top: 13px">

                <form action="{{ route('kuis_update_soal') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                    <input type="hidden" name="kode_mk" value="{{ trim($r->kode_mk) }}">

                    <input type="hidden" name="id_bank_soal" value="{{ $id_soal }}">
                    <input type="hidden" name="id_kuis" value="{{ $kuis->id }}">
                    <input type="hidden" name="id_kuis_soal" value="{{ Request::get('id_kuis_soal') }}">
                    <input type="hidden" name="jenis_soal" value="{{ $soal->jenis_soal }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Jenis Soal</label>
                                    <select class="form-control" disabled="">
                                        <option value="pg" {{ $soal->jenis_soal == 'pg' ? 'selected':'' }}>Pilihan ganda</option>
                                        <option value="es" {{ $soal->jenis_soal == 'es' ? 'selected':'' }}>Essay</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Judul <span>*</span></label>
                                    <input type="text" class="form-control" name="judul" value="{{ $soal->judul }}">
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="form-group">
                                <label class="control-label"> Soal <span>*</span></label>
                                <div>
                                    <textarea cols="10" id="soal" class="form-control" name="soal" rows="12">{{ $soal->soal }}</textarea>
                                </div>
                            </div>
                            
                            <div class="clearfix"></div>

                            <div id="pg">

                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 1</label>
                                        <input type="text" name="jawaban_a" value="{{ $soal->jawaban_a }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 2</label>
                                        <input type="text" name="jawaban_b" value="{{ $soal->jawaban_b }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 3</label>
                                        <input type="text" name="jawaban_c" value="{{ $soal->jawaban_c }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 4</label>
                                        <input type="text" name="jawaban_d" value="{{ $soal->jawaban_d }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 5</label>
                                        <input type="text" name="jawaban_e" value="{{ $soal->jawaban_e }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Kunci Jawaban</label>
                                        <select name="kunci_jawaban" class="form-control">
                                            <option value="">Pilih kunci jawaban</option>
                                            <option value="a" {{ $soal->jawaban_benar == 'a' ? 'selected':'' }}>Pilihan 1</option>
                                            <option value="b" {{ $soal->jawaban_benar == 'b' ? 'selected':'' }}>Pilihan 2</option>
                                            <option value="c" {{ $soal->jawaban_benar == 'c' ? 'selected':'' }}>Pilihan 3</option>
                                            <option value="d" {{ $soal->jawaban_benar == 'd' ? 'selected':'' }}>Pilihan 4</option>
                                            <option value="e" {{ $soal->jawaban_benar == 'e' ? 'selected':'' }}>Pilihan 5</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div id="es" style="display: none">
                                <div class="form-group">
                                    <label class="control-label">Keyword
                                        <span class="petunjuk" title="Kata kunci dari jawaban. Sistem akan secara otomatis memberikan penilaian berdasarkan keyword.<br>
                                                Namun anda masih bisa mengubah penilaian otomatis dari sistem.">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div>
                                        <input type="text" class="form-control" name="keyword" value="{{ $soal->keyword }}" data-role="tagsinput" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <hr>
                            <button class="btn btn-primary btn-sm" id="btn-submit-add" onclick="CKupdate()" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> Simpan</button> &nbsp; &nbsp; 
                            
                            <a href="{{ route('kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" style="margin: 3px 3px" class="btn btn-default btn-sm"><i class="fa fa-list"></i> Batal</a>
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

        @if ( Session::has('success') )
            showSuccess('Berhasil menyimpan soal');
        @endif

        $.get('/ckfinder.php?dsn=<?= Sia::sessionDsn() ?>');

        CKEDITOR.replace( 'soal', {
            startupFocus : false,
            uiColor: '#FFFFFF',
            filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
            filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
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
                    window.location.href="{{ route('kuis_edit_soal') }}/{{ $r->id }}/{{ $kuis->id }}/"+data.id_soal+"?id_kuis_soal={{ Request::get('id_kuis_soal') }}";
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

    function jenisSoal(value)
    {
        if ( value === 'pg' ) {
            $('#pg').show();
            $('#es').hide();
        } else {
            $('#pg').hide();
            $('#es').show();
        }
    }

    jenisSoal('{{ $soal->jenis_soal }}');
</script>
@endsection