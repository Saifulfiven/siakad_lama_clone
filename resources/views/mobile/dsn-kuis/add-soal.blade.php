@extends('mobile.layouts.app')

@section('title','Tambah Soal Kuis')

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
                Tambah Soal: <u>{{ $kuis->judul }}</u>

                <a href="{{ route('m_kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" class="btn-loading btn btn-success btn-xs pull-right">Kembali</a>
            </header>
            <div class="panel-body" style="padding-top: 13px">
                
                <form action="{{ route('m_kuis_soal_store') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                    <input type="hidden" name="kode_mk" value="{{ trim($r->kode_mk) }}">

                    <input type="hidden" name="id_kuis" value="{{ $kuis->id }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Jenis Soal</label>
                                    <select name="jenis_soal" class="form-control" onchange="jenisSoal(this.value)">
                                        <option value="pg">Pilihan ganda</option>
                                        <option value="es">Essay</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Judul <span>*</span></label>
                                    <input type="text" class="form-control" name="judul">
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="form-group">
                                <label class="control-label"> Soal <span>*</span></label>
                                <div>
                                    <textarea cols="10" id="soal" class="form-control" name="soal" rows="12"></textarea>
                                </div>
                            </div>
                            
                            <div class="clearfix"></div>

                            <div id="pg">

                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 1</label>
                                        <input type="text" name="jawaban_a" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 2</label>
                                        <input type="text" name="jawaban_b" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 3</label>
                                        <input type="text" name="jawaban_c" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 4</label>
                                        <input type="text" name="jawaban_d" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Pilihan 5</label>
                                        <input type="text" name="jawaban_e" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4" style="padding-left: 0">
                                    <div class="form-group">
                                        <label class="control-label">Kunci Jawaban</label>
                                        <select name="kunci_jawaban" class="form-control">
                                            <option value="">Pilih kunci jawaban</option>
                                            <option value="a">Pilihan 1</option>
                                            <option value="b">Pilihan 2</option>
                                            <option value="c">Pilihan 3</option>
                                            <option value="d">Pilihan 4</option>
                                            <option value="e">Pilihan 5</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div id="es" style="display: none">
                                <div class="form-group">
                                    <label class="control-label">Keyword
                                        <span class="petunjuk" title="Kata kunci dari jawaban. Sistem akan secara otomatis memberikan penilaian berdasarkan keyword.<br>
                                        Namun anda masih bisa mengubah penilaian otomatis dari sistem.<br>
                                        NB: Tekan ENTER untuk memisahkan setiap keyword">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div>
                                        <input type="text" class="form-control" name="keyword" data-role="tagsinput" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <hr>
                            <button class="btn btn-primary btn-sm" id="btn-submit-add" onclick="CKupdate()" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> Simpan</button> &nbsp; &nbsp; 
                            <a href="{{ route('m_kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" style="margin: 3px 3px" class="btn-loading btn btn-default btn-sm"><i class="fa fa-list"></i> Batal</a>
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

        $.get('/ckfinder.php?dsn=<?= Session::get('m_id_dosen') ?>');

        CKEDITOR.replace( 'soal', {
            startupFocus : false,
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
                    window.location.reload();
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
</script>
@endsection