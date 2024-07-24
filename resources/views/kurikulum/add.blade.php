@extends('layouts.app')

@section('title','Tambah Kurikulum')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Kurikulum
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('kurikulum_store') }}" id="form-kurikulum" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <div class="row" style="margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table border="0" class="table table-hover table-form">
                                    <tr>
                                        <td width="160px">Nama Kurikulum <span>*</span></td>
                                        <td colspan="5"><input type="text" name="nm_kurikulum" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>Program Studi <span>*</span></td>
                                        <td>
                                            <select class="form-control mw-2" name="prodi">
                                                <option value="">-- Pilih program studi --</option>
                                                @foreach( Sia::listProdi() as $r )
                                                    <option value="{{ $r->id_prodi }}">{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>Mulai Berlaku <span>*</span></td>
                                        <td>
                                            <select class="form-control mw-2" name="berlaku">
                                                <option value="">-- Pilih tahun --</option>
                                                @foreach( Sia::listSemester() as $s )
                                                    <option value="{{ $s->id_smt }}">{{ $s->nm_smt }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                        <label style="margin-bottom: 0;padding-top: 3px;cursor: pointer;">
                                            <input type="radio" name="aktif" id="optionsRadios1" value="1" checked>
                                            Aktif
                                        </label>
                                        &nbsp; &nbsp;  
                                        <label style="margin-bottom: 0;padding-top: 3px;cursor: pointer;">
                                            <input type="radio" name="aktif" id="optionsRadios2" value="0">
                                            Non Aktif
                                        </label>
                                        </td>
                                        <td>SKS Wajib</td>
                                        <td><input type="number" value="0" name="sks_wajib" class="form-control mw-1"></td>
                                        <td>SKS Pilihan</td>
                                        <td><input type="number" value="0" name="sks_pilihan" class="form-control mw-1"></td>
                                    </tr>
                                </table>
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
                    window.location.href='{{ route('kurikulum') }}';
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
    submit('kurikulum');

</script>
@endsection