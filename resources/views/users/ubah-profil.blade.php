@extends('layouts.app')

@section('title','Ubah Profil')

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Ubah Profil
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('users_update_profil') }}" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
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
                                <label class="control-label">Email</label>
                                <div>
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Password</label>
                                <div>
                                    <input type="text" class="form-control" name="password" placeholder="Kosongkan jika tak mengganti password">
                                </div>
                            </div>

                            <hr>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
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
    });
    </script>
@endsection