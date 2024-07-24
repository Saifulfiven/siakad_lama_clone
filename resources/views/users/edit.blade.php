@extends('layouts.app')

@section('title','Ubah Pengguna')

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              <b>Ubah Pengguna</b>
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('users_update') }}" id="form-user" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <div class="row">
                        <div class="col-md-7">

                            {{ Rmt::alertErrors($errors) }}
                            {{ Rmt::AlertError() }}
                            {{ Rmt::AlertSuccess() }}

                             <div class="form-group">
                                <label class="control-label">Nama Lengkap <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control" name="nama" value="{{ $user->nama }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Username <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control" name="username" value="{{ $user->username }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email <span>*</span></label>
                                <div>
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" required="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Level <span>*</span></label>
                                <div>
                                    @if ( $user->level == 'mahasiswa' )
                                        <input type="hidden" name="level" value="{{ $user->level }}">
                                        Mahasiswa
                                    @else
                                        <select name="level" class="form-control mw-2" required="">
                                            @foreach( Sia::listLevelUser() as $val )
                                                <?php if ( $val == 'mahasiswa' ) continue ?>
                                                <option value="{{ $val }}" {{ $user->level == $val ? 'selected':'' }}>{{ ucfirst($val) }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Bisa Menaikkan Semester <span>*</span></label>
                                <div>
                                    <select name="naik_smt" class="form-control mw-2" required="">
                                        <option value="0" {{ $user->naik_smt == 0 ? 'selected':'' }}>Tidak</option>
                                        <option value="1" {{ $user->naik_smt == 1 ? 'selected':'' }}>Bisa</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <a href="{{ route('users') }}" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-times"></i> KEMBALI</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label"> Jurusan </label>
                                <div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <ul class="iCheck" data-style="square" data-color="green">
                                                @foreach( Sia::listProdi() as $pr )
                                                <li>
                                                    <input type="checkbox" id="{{ $pr->id_prodi }}" name="jurusan[]" value="{{ $pr->id_prodi }}" {{ count($role) > 0 && in_array($pr->id_prodi, $role) ? 'checked':'' }}>
                                                    <label for="{{ $pr->id_prodi }}">{{ $pr->jenjang .' '.$pr->nm_prodi }}</label>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div><!-- //row-->
                                </div>
                            </div><!-- //form-group-->

                            <div class="form-group">
                                <label class="control-label">Password</label>
                                <div>
                                    <input type="text" class="form-control" name="password" placeholder="Kosongkan jika tak mengganti password">
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
    <script>
    $(function () {
        'use strict';

        $('#jenis-pengguna').change(function(){
            var val = $(this).val();
            if ( val == 'biasa' ) {
                $('#wewenang').show();
                $('#cabang').show();
            } else {
                $('#cabang').hide();
                $('#wewenang').hide();
            }
        });

        $('form#form-user').submit(function(){
            var btn = $('#btn-submit');
            btn.html('MENYIMPAN...');
            btn.prop('disabled', true);
        });
    });
    </script>
@endsection