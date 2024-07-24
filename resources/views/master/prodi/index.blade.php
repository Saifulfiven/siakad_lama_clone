@extends('layouts.app')

@section('title','Program Studi')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Program Studi
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
			                                <td width="110px">&nbsp;</td>
			                                @if ( Sia::akademik() )
			                                    <td><button class="btn btn-primary btn-sm pull-right md-ajax-load"  data-toggle="modal" data-target="#modal-tambah"><i class="fa fa-plus"></i> TAMBAH PRODI</button></td>
			                                @endif
			                            </tr>
			                        </table>

			                        <div class="table-responsive">
			                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
			                                <thead class="custom">
			                                    <tr>
			                                        <th width="20px">No.</th>
			                                        <th>Fakultas</th>
			                                        <th>Kode Prodi</th>
			                                        <th>Nama Prodi</th>
                                                    <th>Jenjang</th>
                                                    <th>Gelar</th>
                                                    <th>Kode NIM</th>
                                                    <th>Kajur</th>
                                                    <th>SK Akreditasi</th>
			                                        <th width="85px">Aksi</th>
			                                    </tr>
			                                </thead>
			                                <tbody align="center">
			                                    @foreach( $prodi as $r )
			                                    <tr>
			                                        <td>{{ $loop->iteration }}</td>
			                                        <td align="left">{{ $r->fakultas->nm_fakultas }}</td>
			                                        <td>{{ $r->id_prodi }}</td>
			                                        <td align="left">{{ $r->nm_prodi }}</td>
                                                    <td>{{ $r->jenjang }}</td>
                                                    <td>{{ $r->gelar }}</td>
                                                    <td>{{ $r->kode_nim }}</td>
                                                    <td align="left">{{ $r->ketua_prodi }}</td>
			                                        <td>{{ $r->sk_akreditasi }}</td>
		                                            <td>
		                                                <a href="javascript:void(0)" onclick="edit('{{ $r->id_prodi }}')" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
		                                                <a href="{{ route('m_prodi_delete',['id' => $r->id_prodi])}}" onclick="return confirm('Anda ingin menghapus data ini')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
		                                            </td>
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
        <h4>Tambah prodi</h4>
    </div>
    <div class="modal-body">
        <form action="{{ route('m_prodi_store') }}" id="form-prodi" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id_fakultas" value="{{ Sia::getFakultasUser() }}">

            <div class="table-responsive">
                <table border="0" class="table table-hover table-form">
                    <tr class="pindahan">
                        <td>Kode Prodi <span>*</span></td>
                        <td>
                            <input type="text" name="id_prodi" maxlength="5" value="{{ old('id_prodi') }}" class="form-control mw-1">
                        </td>
                    </tr>
                    <tr>
                        <td>Nama Prodi <span>*</span></td>
                        <td>
                            <input type="text" name="nm_prodi" value="{{ old('nm_prodi') }}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>Jenjang <span>*</span></td>
                        <td>
                            <select class="form-control select-jenis-daftar mw-1" name="jenjang">
                                @foreach( Sia::jenjang() as $jp )
                                    <option value="{{ $jp }}">{{ $jp }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Gelar Akademik <span>*</span></td>
                        <td>
                            <input type="text" name="gelar" value="{{ old('gelar') }}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>Kode NIM <span>*</span>
                        <p style="font-size: 14px">xxxx<span style="color:red;font-size: 14px"> 21 </span>xxxx</p>
                        </td>
                        <td>
                            <input type="text" name="kode_nim" value="{{ old('kode_nim') }}" class="form-control mw-1">
                        </td>
                    </tr>
                    <tr>
                        <td>No. SK Akreditasi <span>*</span></td>
                        <td>
                            <input type="text" name="sk_akreditasi" value="{{ old('sk_akreditasi') }}" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>Ketua Prodi <span>*</span></td>
                        <td>
                            <input type="text" name="ketua_prodi" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td>NIP Ketua Prodi <span>*</span></td>
                        <td>
                            <input type="text" name="nip_ketua_prodi" class="form-control">
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
        <h4>Ubah program studi</h4>
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

    function edit(id)
    {
        $('#form-edit').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
        $('#modal-edit').modal('show');
        $.ajax({
            url: '{{ route('m_prodi_edit') }}/'+id,
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
    submit('prodi');
</script>
@endsection