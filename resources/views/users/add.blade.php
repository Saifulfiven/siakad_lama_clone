@extends('layouts.app')

@section('title','Tambah Pengguna')

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              <b>Tambah Pengguna</b>
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('users_store') }}" id="form-user" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-7">

                            {{ Rmt::alertErrors($errors) }}
                            {{ Rmt::AlertError() }}

                            <div class="form-group">
                                <label class="control-label">Nama Lengkap <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control" name="nama" value="{{ old('nama') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Username <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control" name="username" value="{{ old('username') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Password <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control" name="password" value="{{ old('password') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email <span>*</span></label>
                                <div>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Level <span>*</span></label>
                                <div>
                                    <select name="level" class="form-control mw-2" required="" onchange="levelChange(this.value)">
                                        @foreach( Sia::listLevelUser() as $val )
                                            @if ( ($val == 'mahasiswa') || $val == 'dosen' )
                                                <?php continue ?>
                                            @endif
                                            <option value="{{ $val }}" {{ old('level') == $val ? 'selected':'' }}>{{ ucfirst($val) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Bisa Menaikkan Semester <span>*</span></label>
                                <div>
                                    <select name="naik_smt" class="form-control mw-2" required="">
                                        <option value="0" {{ old('naik_smt') == 0 ? 'selected':'' }}>Tidak</option>
                                        <option value="1" {{ old('naik_smt') == 1 ? 'selected':'' }}>Bisa</option>
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <a href="{{ route('users') }}" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-times"></i> KEMBALI</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label"> Fakultas</label>
                                <div>
                                    <select name="fakultas" class="form-control" onchange="getProdi(this.value)" id="get-prodi" required="">
                                        <option value="x">--Pilih Fakultas</option>
                                        @foreach( Sia::fakultas() as $fa )
                                            <option value="{{ $fa->id }}">{{ $fa->nm_fakultas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"> Jurusan </label>
                                <div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <ul class="iCheck"  data-style="square" data-color="green">
                                                Pilih prodi dahulu
                                            </ul>
                                        </div>
                                    </div><!-- //row-->
                                </div>
                            </div><!-- //form-group-->
                        </div>

                    </div>


                </form>

            </div>

        </div>
      </div>
    </div>
@endsection

@section('registerscript')
    <script>
    $(function () {
        'use strict';

        $('form#form-user').submit(function(){
            var btn = $('#btn-submit');
            btn.html('MENYIMPAN...');
            btn.prop('disabled', true);
        });
    });

    function getProdi(fakultas)
    {
        $('.iCheck').html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: '?fakultas='+fakultas,
            success: function(result){
                $('.iCheck').html(result);
            },
            error: function(data,status,msg){
                alert(msg);
            }
        });
    }

    function levelChange(value)
    {
        if ( (value == 'ketua') || (value == 'personalia') || (value == 'pengawas') ) {
            getProdi('all');
            $('#get-prodi').attr('disabled','');
            $('#get-prodi').removeAttr('required');
        } else {
            $('#get-prodi').attr('required','');
            $('#get-prodi').removeAttr('disabled');
            $('#get-prodi').val('x');
            $('.iCheck').html('Pilih fakultas dahulu');
        }
    }
    </script>
@endsection