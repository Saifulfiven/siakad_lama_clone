@extends('mobile.layouts.app')

@section('title','Tambah Tugas')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
                Tambah Tugas: <u>{{ $r->nm_mk }}</u>
            </header>
            <div class="panel-body" style="padding-top: 13px">

                <form action="{{ route('dsnm_lms_tugas_store') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                    <input type="hidden" name="id_bank_materi" id="id_bank_materi">
                    <input type="hidden" name="nama_file" id="nama-file">
                    <input type="hidden" class="sumber_file" name="sumber_file" value="">

                    <input type="hidden" name="pertemuan" value="{{ Request::get('prt') }}">
                    <input type="hidden" name="id_dosen" value="{{ Request::get('id_dosen') }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Jenis
                                        <span class="petunjuk" title="Tugas : Tugas matakuliah, tidak dipengaruhi oleh pembayaran.<br>Ujian : UTS / UAS yang dipengaruhi oleh pembayaran">
                                            <i class="fa fa-question-circle"></i>
                                        </span> <span></span>
                                    </label>
                                    <select name="jenis" class="form-control">
                                        <option value="tugas">Tugas</option>
                                        <option value="ujian">Ujian</option>
                                    </select>

                                </div>
                            </div>

                            <div class="col-md-8" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Judul <span>*</span></label>
                                    <input type="text" class="form-control" name="judul">
                                </div>
                            </div>

                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Pengiriman dimulai tanggal
                                        <span class="petunjuk" title="Peserta tidak akan dapat mengirimkan sebelum tanggal ini.<br>
                                         Jika kosong peserta akan dapat mengirimkan tugas segera.">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="row">
                                        <div class="input-group date col-md-12 tgl-tugas" data-picker-position="bottom-left">
                                            <input type="text" class="form-control" name="mulai_berlaku" value="{{ Carbon::now()->format('d F Y h:i') }}">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">
                                        Tanggal Jatuh Tempo
                                        <span class="petunjuk" title="Jika diisi,<br>
                                            Pengajuan masih akan diizinkan setelah tanggal ini, <br>
                                            tetapi tugas yang diajukan setelah tanggal ini ditandai sebagai terlambat.<br>
                                            Kosongkan jika tidak ada tanggal jatuh tempo.">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="row">
                                        <div class="input-group date tgl-tugas col-md-12" data-picker-position="bottom-left">
                                            <input type="text" class="form-control" name="tgl_berakhir">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Batas Akhir Upload
                                        <span class="petunjuk" title="Jika diaktifkan, <br>
                                            Pengiriman tugas tidak akan bisa dilakukan <br>setelah tanggal yang ditentukan.<br>
                                            Kosongkan jika tidak ingin dibatasi.">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="row">
                                        <div class="input-group date tgl-tugas col-md-12" data-picker-position="bottom-left">
                                            <input type="text" class="form-control" name="tgl_tutup">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="form-group">
                                <label class="control-label"> Deskripsi (Opsional)</label>
                                <div>
                                    <textarea cols="10" id="deskripsi" class="form-control" name="deskripsi" rows="12"></textarea>
                                </div>
                            </div>

                            <div class="panel-group" id="accordion">
                                <div class="panel panel-shadow">
                                    <header class="panel-heading" style="padding:0px;background: #ededed">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#pengaturan"><i class="collapse-caret fa fa-angle-up"></i> Pengaturan Lainnya</a>
                                    </header>
                                    <div id="pengaturan" class="panel-collapse collapse in">
                                        <div class="panel-body" style="max-height: 600px">

                                            <div class="col-md-3" style="padding-left: 0">
                                                <div class="form-group">
                                                    <label class="control-label">Jenis Pengajuan
                                                        <span class="petunjuk" title="<ul><li><b>Hanya upload file:</b> Peserta hanya bisa mengupload file.</li>
                                                            <li><b>Hanya Online Text: </b>Peserta mengisi tugas secara online melalui editor yang disediakan.</li>
                                                            <li><b>Online dan Upload file: </b>Peserta mengisi jawaban secara online dan atau mengupload jawaban.</li></ol>">
                                                            <i class="fa fa-question-circle"></i>
                                                        </span>
                                                    </label>
                                                    <br>
                                                    
                                                    <select name="jenis_pengiriman" class="form-control" onchange="jenisPengiriman(this.value)">
                                                        <option value="file">Hanya Upload File</option>
                                                        <option value="text">Hanya Online Text</option>
                                                        <option value="all">Online Text dan Upload File</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3" style="padding-left: 0">
                                                <div class="form-group">
                                                    <label class="control-label">Maksimal Ukuran File
                                                        <span class="petunjuk" title="Maksimal ukuran file yang bisa diupload oleh peserta.">
                                                            <i class="fa fa-question-circle"></i>
                                                        </span>
                                                    </label>
                                                    <br>
                                                    
                                                    <select name="max_file_upload" class="form-control max_file_upload">
                                                        @foreach( Rmt::listSize() as $key => $val )
                                                            <option value="{{ $key }}">{{ $val }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3" style="padding-left: 0">
                                                <div class="form-group">
                                                    <label class="control-label">Minimal Jumlah Karakter
                                                        <span class="petunjuk" title="Jumlah Karakter minimal yang bisa diinput peserta.">
                                                            <i class="fa fa-question-circle"></i>
                                                        </span>
                                                    </label>
                                                    <input type="number" class="form-control mw-2 minimal_kata" placeholder="0" name="minimal_kata" disabled="">
                                                </div>
                                            </div>

                                            <div class="col-md-3" style="padding-left: 0">
                                                <div class="form-group">
                                                    <label class="control-label">Maksimal Jumlah Karakter
                                                        <span class="petunjuk" title="Jumlah Karakter makasimal yang bisa diinput peserta.">
                                                            <i class="fa fa-question-circle"></i>
                                                        </span>
                                                    </label>
                                                    <input type="number" class="form-control mw-2 maksimal_kata" placeholder="Tidak terbatas" name="maksimal_kata" disabled="">
                                                </div>
                                            </div>

                                            <div class="clearfix"></div>

                                            <div class="col-md-3" style="padding-left: 0">
                                                <div class="form-group">
                                                    <label class="control-label">Batasan Upload</label>
                                                        <span class="petunjuk" title="Jumlah maksimal peserta bisa mengupload tugas.">
                                                            <i class="fa fa-question-circle"></i>
                                                        </span>
                                                    </label>
                                                    <br>
                                                    
                                                    <select name="max_attempt" class="form-control">
                                                        <option value="">Tidak terbatas</option>
                                                        @for( $i = 1; $i <= 5; $i++ )
                                                            <option value="{{ $i }}">{{ $i }} kali</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12" style="padding-left: 0">
                                                <div class="form-group">
                                                    <label class="control-label">File Pendukung (Opsional)</label>
                                                    <br>
                                                    
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="file_tugas" disabled="">
                                                        <span class="input-group-btn">
                                                            <a href="javascript:;" class="btn btn-default remove-file" onclick="removeFile()" style="display: none"><i class="fa fa-times"></i></a>
                                                            <button class="btn btn-default" type="button" data-target="#modal-file" data-toggle="modal"><i class="fa fa-file"></i> Pilih File</button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <a href="javascript:;" onclick="goBack()" style="margin: 3px 3px" class="btn btn-default btn-sm btn-loading"><i class="fa fa-arrow-left"></i> Batal / Kembali</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit-add" onclick="CKupdate()" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> Simpan</button>
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
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/ckeditor-4-full/ckeditor.js"></script>
<script>

    Dropzone.options.dropzone = {
        maxFilesize: 8,
        dictDefaultMessage: 'Seret file ke sini atau klik',
        acceptedFiles: fileAccept(),
        success: function(file, response) 
        {
            pilih('', file.name, 'upload');
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

        $('.tgl-tugas').datetimepicker({
            bornIn:"#main",
            weekStart: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            format: "dd MM yyyy hh:ii"
        });

        $.get('/ckfinder.php?dsn=<?= Request::get('id_dosen') ?>');

        CKEDITOR.replace( 'deskripsi', {
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

    function pilih(id, file, sumber)
    {
        $('.remove-file').show();

        $('.sumber_file').val(sumber);
        $('#id_bank_materi').val(id);

        $('#file_tugas').val(file);
        $('#nama-file').val(file);

        $('#modal-file').modal('hide');
        $('#tab1').trigger('click');
    }

    function removeFile()
    {
        $('.remove-file').hide();
        $('.sumber_file').val('');
        $('#id_bank_materi').val('');

        $('#file_tugas').val('');
        $('#nama-file').val('');
    }

    function jenisPengiriman(value)
    {
        if ( value === 'text' ) {
            $('.max_file_upload').attr('disabled','');
            $('.minimal_kata').removeAttr('disabled');
            $('.maksimal_kata').removeAttr('disabled');
        } else if ( value === 'file' ) {
            $('.max_file_upload').removeAttr('disabled','');
            $('.minimal_kata').attr('disabled','');
            $('.maksimal_kata').attr('disabled','');
        } else {
            $('.max_file_upload').removeAttr('disabled');
            $('.minimal_kata').removeAttr('disabled');
            $('.maksimal_kata').removeAttr('disabled');
        }
    }

</script>
@endsection