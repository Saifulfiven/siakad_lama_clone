@extends('layouts.app')

@section('title','Profil')

@section('heading')
<link rel="stylesheet" type="text/css" href="{{ url('resources') }}/assets/plugins/signature-pad/assets/jquery.signaturepad.css">
@endsection

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              PROFIL
            </header>
              
            <div class="panel-body" style="padding-top: 20px">

                {{ Rmt::AlertSuccess() }}

                <form action="{{ route('dsn_update_profil') }}" id="form-dosen" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-6">

                            <div style="text-align: center; margin-bottom: 20px">

                                <?php if ( !empty($dsn->foto) ) { ?>
                                    <img id="foto" src="{{ config('app.url-foto-dosen') }}/{{ $dsn->foto }}" width="100">
                                <?php } else { ?>
                                    <img id="foto" src="{{ url('resources') }}/assets/img/avatar.png" width="100">
                                <?php } ?>
                                <br>
                                <br>
                                <button type="button" id="btn-file" name="foto" class="btn btn-sm btn-default">Upload foto</button>
                                
                            </div>

                            <?= Sia::TextfieldEdit('Nama <span>*</span>','nama', $dsn->nm_dosen) ?>
                            <?= Sia::TextfieldEdit('Gelar depan','gelar_depan', $dsn->gelar_depan) ?>
                            <?= Sia::TextfieldEdit('Gelar belakang','gelar_belakang', $dsn->gelar_belakang) ?>
                            <div class="form-group">
                                <label class="control-label">Pendidikan Tertinggi</label>
                                <div>
                                    <select name="pendidikan_tertinggi" class="form-control mw-1">
                                        @foreach( Sia::jenjang() as $val )
                                            <option value="{{ $val }}" {{ $val == $dsn->pendidikan_tertinggi ? 'selected':'' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <?= Sia::TextfieldEdit('Tempat Lahir','tempat_lahir', $dsn->tempat_lahir) ?>
                            <div class="form-group">
                              <label class="control-label">Tanggal Lahir</label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime col-md-10" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_lahir" value="{{ !empty($dsn->tgl_lahir) ? Rmt::formatTgl($dsn->tgl_lahir) : '' }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Jenis Kelamin</label>
                                <div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" {{ $dsn->jenkel == 'L' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios1" value="L">
                                            Laki-laki
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" {{ $dsn->jenkel == 'P' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios2" value="P">
                                            Perempuan
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Agama</label>
                                <div>
                                    <select class="form-control" name="agama">
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach( Sia::listAgama() as $ag )
                                            <option value="{{ $ag->id_agama }}" {{ $dsn->id_agama == $ag->id_agama ? 'selected':'' }}>{{ $ag->nm_agama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?= Sia::TextfieldEdit('Alamat','alamat',$dsn->alamat) ?>
                            <?= Sia::TextfieldEdit('No. Hp','hp',$dsn->hp) ?>
                            <?= Sia::TextfieldEdit('Email','email',$dsn->email,false,'email') ?>

                        </div>

                        <div class="col-md-6">
                            <?= Sia::TextfieldEdit('NIP','nip', $dsn->nip) ?>
                            <?= Sia::TextfieldEdit('NIDN','nidn', $dsn->nidn) ?>
                            <div class="form-group">
                                <label class="control-label">Jenis Dosen <span>*</span></label>
                                <div>
                                    {{ Sia::jenisDosen($dsn->jenis_dosen) }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Jabatan Fungsional</label>
                                <div>
                                    <select name="jabatan_fungsional" class="form-control mw-2">
                                        @foreach( Sia::jabatanFungsional() as $key => $val )
                                            <option value="{{ $key }}" {{ $dsn->jabatan_fungsional == $key ? 'selected': '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?= Sia::TextfieldEdit('Golongan','golongan', $dsn->golongan) ?>

                            <div class="form-group">
                                <label class="control-label">Aktivitas</label>
                                <div>
                                    <select name="aktivitas" class="form-control mw-2">
                                        @foreach( Sia::aktivitasDosen() as $key => $val )
                                            <option value="{{ $key }}" {{ $dsn->aktivitas == $key ? 'selected': '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Data akun</h5>
                                    <hr style="margin-top: 10px;margin-bottom: 10px">

                                    <?= Sia::TextfieldEdit('Username <span>*</span>','username', $dsn->username) ?>
               
                                    <?= Sia::TextfieldEdit('Password','password', '', false, 'password','<p class="help-block">Isi jika ingin mereset password</p>') ?>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Tanda Tangan</h5>
                                    <hr style="margin-top: 10px;margin-bottom: 10px">

                                    @if ( !empty($dsn->ttd) )
                                        <img src="{{ url('/storage') }}/ttd-dosen/{{ $dsn->ttd }}" width="150" id="img-ttd">
                                    @endif

                                    <div class="clearfix"></div>
                                    <button type="button" data-target="#modal-ttd" data-toggle="modal" data-backdrop="static" class="btn btn-theme-inverse btn-xs"><i class="fa fa-pencil"></i> {{ empty($dsn->ttd) ? 'Buat' : 'Ubah' }} Tanda Tangan</button>

                                </div>
                            </div>


                        </div>

                        <div class="col-md-12" style="margin-top: 10px">
                            <hr>
                            <center>
                                <button class="btn btn-primary" id="btn-submit-dosen" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                            </center>
                        </div>

                    </div>

                </form>

                <form action="{{ route('dsn_update_foto') }}" id="form-upload-foto" enctype="multipart/form-data" method="post">
                    {{ csrf_field() }}
                    <input type="file" name="foto" id="inputFile" accept="image/*" style="display:none">
                </form>

            </div>

        </div>
      </div>
    </div>

    <div id="modal-ttd" class="modal fade" data-width="350px" tabindex="-1" style="top:30%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Tanda Tangan Digital</h4>
        </div>
        <!-- //modal-header-->
        <form method="post" action="" class="sigPad" id="form-ttd">
            <div class="modal-body">
                {{ csrf_field() }}
                <ul class="sigNav">
                    <li>Buat tanda tangan</li>
                    <li class="clearButton"><a href="#clear">Bersihkan</a></li>
                </ul>
                <div class="sig sigWrapper">
                    <div class="typed"></div>
                    <canvas class="pad" width="300" height="100"></canvas>
                    <input type="hidden" name="output" class="output">
                </div>
                <p><i>Mohon maksimalkan area yang ada</i></p>
            </div>
            <!-- //modal-body-->
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" data-dismiss="modal" class="btn btn-info">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan tanda tangan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

<script src="{{ url('resources') }}/assets/plugins/signature-pad/numeric-1.2.6.min.js"></script> 
<script src="{{ url('resources') }}/assets/plugins/signature-pad/bezier.js"></script>
<script src="{{ url('resources') }}/assets/plugins/signature-pad/jquery.signaturepad-2.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/signature-pad/assets/json2.min.js"></script>
<!--[if lt IE 9]>
  <script src="{{ url('resources') }}/assets/plugins/signature-pad/assets/flashcanvas.js"></script>
<![endif]-->
<script>
    $(function () {
        'use strict';

        $('#btn-file').click(function(){
            $('#inputFile').trigger('click');
        });


        $("#inputFile").change(function(){
            readURL(this);
            $('#btn-file').html('<i class="fa fa-spin fa-spinner"></i>');
            $('#form-upload-foto').submit();
        });

        var ttd = $('.sigPad').signaturePad({
            drawOnly:true,
            penColour :'#000',
            penWidth : 4,
            bgColour: 'transparent',
            lineTop: 30,
            drawBezierCurves:true,
        });

        $('#form-ttd').submit(function(e){
            
            if ( !ttd.validateForm() ) {
                return false;
            }

            var canvas_img = ttd.getSignatureImage();
            var img_data = canvas_img.replace(/^data:image\/(png|jpg);base64,/,"");

            $('body').modalmanager('loading');

            $.ajax({
                url: '{{ route('dsn_ttd') }}',
                data: { img_data: img_data },
                type: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(respose) {
                    window.location.reload();
                },
                error: function(data, status, message){
                    var respon = parseObj(data.responseJSON);
                    var pesan = '';
                    for ( let i = 0; i < respon.length; i++ ){
                        pesan += "- "+respon[i]+"<br>";
                    }
                    if ( pesan == '' ) {
                        pesan = message;
                    }

                    showMessage2('modul',pesan);
                }
            });

            e.preventDefault();
        });

    });

    function readURL(input) {

      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('#foto').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      };
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('body').modalmanager('loading');
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    $('#btn-file').html('Upload foto');
                    $('#btn-file').removeAttr('disabled');
                    showMessage2(modul, data.msg);
                } else {
                    if ( modul == 'upload-foto' ) {
                        $('body').modalmanager('loading');
                        $('#btn-file').html('Upload foto');
                        $('#btn-file').removeAttr('disabled');
                        showSuccess('Berhasil mengubah foto');
                    } else {
                        window.location.reload();
                    }
                }
            },
            error: function(data, status, message)
            {
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }

                $('#btn-file').html('Upload foto');
                $('#btn-file').removeAttr('disabled');
                showMessage2(modul,pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('dosen');
    submit('upload-foto');

</script>
@endsection