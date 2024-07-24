@extends('layouts.app')

@section('title','Tambah Dosen')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Dosen
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('dosen_store') }}" id="form-dosen" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= Sia::Textfield('Nama <span>*</span>','nama') ?>
                            <?= Sia::Textfield('Gelar depan','gelar_depan') ?>
                            <?= Sia::Textfield('Gelar belakang','gelar_belakang') ?>
                            <div class="form-group">
                                <label class="control-label">Pendidikan Tertinggi</label>
                                <div>
                                    <select name="pendidikan_tertinggi" class="form-control mw-1">
                                        @foreach( Sia::jenjang() as $val )
                                            <option value="{{ $val }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?= Sia::Textfield('Tempat Lahir','tempat_lahir') ?>
                            <div class="form-group">
                              <label class="control-label">Tanggal Lahir</label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime col-md-10" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_lahir" value="{{ old('tgl_lahir') }}">
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
                                            <input type="radio" {{ old('jenis_kelamin') == 'L' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios1" value="L" checked>
                                            Laki-laki
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" {{ old('jenis_kelamin') == 'P' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios2" value="P">
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
                                            <option value="{{ $ag->id_agama }}">{{ $ag->nm_agama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?= Sia::Textfield('Alamat','alamat') ?>
                            <?= Sia::Textfield('No. Hp','hp') ?>
                            <?= Sia::Textfield('Email','email',false,'email') ?>
                        </div>

                        <div class="col-md-6">
                            <?= Sia::Textfield('NIP','nip') ?>
                            <?= Sia::Textfield('NIDN','nidn') ?>
                            <div class="form-group">
                                <label class="control-label">Program Studi</label>
                                <div>
                                    <select class="form-control" name="prodi">
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach( Sia::listProdi() as $r )
                                            <option value="{{ $r->id_prodi }}">{{ $r->jenjang.' '.$r->nm_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Jenis Dosen <span>*</span></label>
                                <div>
                                    <select class="form-control" name="jenis_dosen">
                                        <option value="">-- Pilih jenis dosen --</option>
                                        @foreach( Sia::jenisDosen() as $r )
                                            <option value="{{ $r }}">{{ $r }} - {{ Sia::jenisDosen($r) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Jabatan Fungsional</label>
                                <div>
                                    <select name="jabatan_fungsional" class="form-control mw-2">
                                        @foreach( Sia::jabatanFungsional() as $key => $val )
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?= Sia::Textfield('Golongan','golongan') ?>

                            <div class="form-group">
                                <label class="control-label">Aktivitas</label>
                                <div>
                                    <select name="aktivitas" class="form-control mw-2">
                                        @foreach( Sia::aktivitasDosen() as $key => $val )
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

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
    });

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $("#btn-submit").attr('disabled','');
                $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(data.msg);
                } else {
                    window.location.href='{{ route('dosen') }}';
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
                showMessage(pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('dosen');

</script>
@endsection