@extends('layouts.app')

@section('title','Konsentrasi')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Konsentrasi
					</header>

			        <div class="panel-body" style="padding: 3px 3px;">
			            
			            <div class="col-md-12">

			                {{ Rmt::AlertSuccess() }}
			                {{ Rmt::AlertError() }}
			                {{ Rmt::AlertErrors($errors) }}

			                <div class="row">

			                    <div class="col-md-12">
			                        <table border="0" width="100%" style="margin-bottom: 10px">
			                            <tr>
                                            <td width="110">Program Studi</td>
                                            <td>
                                                <select class="form-control mw-2" id="change-prodi">
                                                    <option value="">Semua Program Studi</option>
                                                    @foreach( Sia::listProdi() as $pr )
                                                        <option value="{{ $pr->id_prodi }}" {{ $pr->id_prodi == Request::get('prodi') ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
			                                <td width="110px">&nbsp;</td>
			                                @if ( Sia::adminOrAkademik() )
			                                    <td><button class="btn btn-primary btn-sm pull-right md-ajax-load"  data-toggle="modal" data-target="#modal-tambah"><i class="fa fa-plus"></i> TAMBAH KONSENTRASI</button></td>
			                                @endif
			                            </tr>
			                        </table>

			                        <div class="table-responsive">

			                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
			                                <thead class="custom">
			                                    <tr>
			                                        <th width="20px">No.</th>
			                                        <th>Kode Prodi</th>
                                                    <th>Nama Prodi</th>
                                                    <th>Nama Konsentrasi</th>
			                                        <th>Status</th>
			                                        @if ( Sia::adminOrAkademik() )
			                                            <th>Aksi</th>
			                                        @endif
			                                    </tr>
			                                </thead>
			                                <tbody align="center">
			                                    @foreach( $konsentrasi as $r )
			                                    <tr>
			                                        <td>{{ $loop->iteration }}</td>
			                                        <td>{{ $r->id_prodi }}</td>
                                                    <td>{{ $r->jenjang.' '.$r->nm_prodi }}</td>
                                                    <td align="left">{{ $r->nm_konsentrasi }}</td>
                                                    <td><?= $r->aktif == '1' ? '<i class="fa fa-check"></i>' : '<i class="fa fa-ban" style="color: red"></i></td>' ?></td>
			                                        @if ( Sia::adminOrAkademik() )
			                                            <td>
			                                                <a href="javascript:void(0)" onclick="edit('{{ $r->id_konsentrasi }}')" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp;
			                                                <a href="{{ route('m_konsentrasi_delete',['id' => $r->id_konsentrasi])}}" onclick="return confirm('Anda ingin menghapus data ini')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
			                                            </td>
			                                        @endif
			                                    </tr>
			                                    @endforeach
			                                </tbody>
			                            </table>
			                        </div>

			                    </div>
			                </div>

			            </div>

			        </div>
			    </section>

    		</div>
  		</div>
	</div>

<div id="modal-tambah" class="modal fade" data-width="500" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Tambah Konsentrasi</h4>
    </div>
    <div class="modal-body">
        <form action="{{ route('m_konsentrasi_store') }}" id="form-konsentrasi" method="post">
            {{ csrf_field() }}

            <div class="table-responsive">
                <table border="0" class="table table-hover table-form">
					<tr>
                        <td>Nama Prodi <span>*</span></td>
                        <td>
                            <select class="form-control" name="prodi">
                                <option value="">-- Pilih program studi --</option>
                                @foreach( Sia::listProdi() as $f )
                                    <option value="{{ $f->id_prodi }}">{{ $f->jenjang.' '.$f->nm_prodi }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nama Konsentrasi <span>*</span></td>
                        <td>
                            <input type="text" name="nm_konsentrasi" value="{{ old('nm_konsentrasi') }}" class="form-control">
                        </td>
                    </tr>
                </table>
            </div>
            <hr>
            <button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
        </form>
    </div>
</div>

<div id="modal-edit" class="modal fade" data-width="600" tabindex="-1" style="top: 20%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Ubah Konsentrasi</h4>
    </div>
    <div class="modal-body" id="form-edit">
        
    </div>
</div>

<div id="modal-error" class="modal fade" tabindex="-1">
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

    $(function(){
        $('#change-prodi').change(function(){
            var prodi = $(this).val();
            window.location.href='?prodi='+prodi;
        });
    });

    function edit(id)
    {
        $('#form-edit').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
        $('#modal-edit').modal('show');
        $.ajax({
            url: '{{ route('m_konsentrasi_edit') }}/'+id,
            data: { id: id },
            success: function(data){
                $('#form-edit').html(data);
            },
            error: function(err,data,msg)
            {
                alert(msg)
            }
        });
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
    submit('konsentrasi');
</script>
@endsection