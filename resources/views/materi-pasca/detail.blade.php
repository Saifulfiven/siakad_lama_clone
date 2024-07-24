@extends('layouts.app')

@section('title','Detail Materi Matakuliah')

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading" style="padding-bottom: 20px;">
              {{ $mk->kode_mk }} - {{ $mk->nm_mk }}
                <div class="pull-right">
                    <a href="{{ route('materi') }}" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-list"></i> DAFTAR</a>
                    
                    <a href="#" style="margin: 3px 3px" class="btn btn-primary btn-sm" id="tambah" data-toggle="modal" data-target="#modal-add"><i class="fa fa-plus"></i> TAMBAH</a>
                </div>
            </header>
              
            <div class="panel-body" style="padding-top: 3px">
                {{ Rmt::AlertSuccess() }}

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Judul</th>
                                        <th>File</th>
                                        <th width="200">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody align="center">

                                    <?php 
                                        $data = DB::table('materi_kuliah_pasca')
                                                ->where('kode_mk', $mk->kode_mk)
                                                ->get();
                                        $no = 1;
                                        
                                    ?>

                                    @if ( count($data) > 0 )

                                        @foreach( $data as $res )

                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td align="left">
                                                    <a href="{{ route('materi_download', ['id' => $res->id, 'file' => $res->file_materi]) }}" target="_blank" title="Download">
                                                        {{ $res->judul }}

                                                    </a>
                                                </td>
                                                <td align="left">{{ $res->file_materi }}</td>
                                                <td>
                                                    <a href="javascript:void();" onclick="edit('{{ $res->id }}','{{ $res->judul }}')" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a> &nbsp; 
                                                    <a href="{{ route('materi_delete', ['id' => $res->id]) }}" onclick="return confirm('Anda ingin menghapus materi ini?')" class="btn btn-danger btn-xs"><i class="fa fa-times"></i> Hapus</a>
                                                </td>

                                        @endforeach
                                    
                                    @else
                                        <tr>
                                            <td colspan="4">
                                                Belum ada materi
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

            </div>

        </div>
      </div>
    </div>


    <div id="modal-add" class="modal fade" style="top:30%" tabindex="-1" data-width="600">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Tambah Materi : {{ $mk->kode_mk }} - {{ $mk->nm_mk }}</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <form action="{{ route('materi_store') }}" id="form-matakuliah" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="matakuliah" value="{{ $mk->kode_mk }}">
                    <div class="row">
                        <div class="col-md-12">
                            <?= Sia::Textfield('Judul <span>*</span>','judul',false,'text','mw-4') ?>

                            <div class="form-group">
                                <label class="control-label">File Materi <span>*</span></label>
                                <div>
                                    <input type="file" name="file" class="form-control">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <hr>
                            <a href="javascript:void()" data-dismiss="modal" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-times"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>


                    </div>

                </form>
        </div>
        <!-- //modal-body-->
    </div>

    <div id="modal-edit" class="modal fade" style="top:30%" tabindex="-1" data-width="600">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Ubah Materi : {{ $mk->kode_mk }} - {{ $mk->nm_mk }}</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <form action="{{ route('materi_update') }}" id="form-edit" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id-materi">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Judul <span>*</span></label>
                                <div>
                                    <input type="text" name="judul" id="judul-materi" class="form-control">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <hr>
                            <a href="javascript:void()" data-dismiss="modal" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-times"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>


                    </div>

                </form>
        </div>
        <!-- //modal-body-->
    </div>


    <div id="modal-error" class="modal fade" tabindex="-1" style="top:30%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Terjadi kesalahan</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <div class="ajax-message"></div>
            <hr>
            <center>
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
            </center>
        </div>
        <!-- //modal-body-->
    </div>
@endsection


@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

    <script>
        function edit(id, judul)
        {
            $('#id-materi').val(id);
            $('#judul-materi').val(judul);
            $('#modal-edit').modal('show');
        }

        function showMessage(pesan)
        {
            $('#overlay').hide();
            $('.ajax-message').html(pesan);
            $('#modal-error').modal('show');

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
        submit('matakuliah');
    </script>
@endsection