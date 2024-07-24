@extends('layouts.app')

@section('title','Edit Dosen')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Ubah Dosen
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>
                {{ Rmt::AlertSuccess() }}

                <form action="{{ route('dosen_update') }}" id="form-dosen" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $dsn->id }}">
                    <input type="hidden" name="id_user" value="{{ $dsn->id_user }}">
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('dosen') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> KEMBALI</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
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

                            <div class="form-group">
                                <label class="control-label">Status</label>
                                <div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" {{ $dsn->aktif == '1' ? 'checked':'' }} name="aktif" id="optionsRadios1" value="1">
                                            Ya
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" {{ $dsn->aktif == '0' ? 'checked':'' }} name="aktif" id="optionsRadios2" value="0">
                                            Tidak
                                        </label>
                                    </div>
                                    <p class="help-block">Jika Tidak maka dosen ini tidak akan ditampilkan di pencarian dosen saat membuat jadwal kuliah</p>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <?= Sia::TextfieldEdit('NIP','nip', $dsn->nip) ?>
                            <?= Sia::TextfieldEdit('NIDN','nidn', $dsn->nidn) ?>
                            <div class="form-group">
                                <label class="control-label">Program Studi</label>
                                <div>
                                    <select class="form-control" name="prodi">
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach( Sia::listProdi() as $r )
                                            <option value="{{ $r->id_prodi }}" {{ $r->id_prodi == $dsn->id_prodi ? 'selected':'' }}>{{ $r->jenjang.' '.$r->nm_prodi }}</option>
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
                                            <option value="{{ $r }}" {{ $dsn->jenis_dosen == $r ? 'selected': '' }}>{{ $r }} - {{ Sia::jenisDosen($r) }}</option>
                                        @endforeach
                                    </select>
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
                                    
                                    @if ( Sia::role('admin') )
                                        <?= Sia::TextfieldEdit('Password','password', '', false, 'password','<p class="help-block">Isi jika ingin mereset password</p>') ?>
                                    @endif
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
                    window.location.reload();
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