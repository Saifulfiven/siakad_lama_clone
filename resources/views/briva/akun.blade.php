@extends('layouts.app')

@section('title','Akun BRIVA Mahasiswa')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Akun BRIVA Mahasiswa
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
                                            <td width="220">
                                                <select class="form-control mw-2" id="change-prodi" onchange="filter('prodi', this.value)">
                                                    <option value="">Semua Program Studi</option>
                                                    @foreach( Sia::listProdi() as $pr )
                                                        <option value="{{ $pr->id_prodi }}" {{ $pr->id_prodi == Request::get('prodi') ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control mw-2" id="change-angkatan" onchange="filter('angkatan', this.value)">
                                                    <option value="">Semua Angkatan</option>
                                                    @foreach( Sia::listAngkatan() as $a )
                                                        <option value="{{ $a }}" {{ $a == Request::get('angkatan') ? 'selected':'' }}>{{ $a }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
			                                <td width="110px">&nbsp;</td>
                                            <td width="300px">
                                                <form action="" id="form-cari">
                                                    <div class="input-group pull-right">
                                                        <input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
                                                        <div class="input-group-btn">
                                                            <a href="{{ route('briva_akun') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
                                                            <button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
			                            </tr>
			                        </table>

			                        <div class="table-responsive">

			                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
			                                <thead class="custom">
			                                    <tr>
			                                        <th width="20px">No.</th>
                                                    <th>NIM</th>
                                                    <th>Nama</th>
                                                    <th>Prodi</th>
			                                        <th>No. BRIVA</th>
                                                    <th>Cust. Code</th>
			                                        <th>Aksi</th>
			                                    </tr>
			                                </thead>
			                                <tbody>
			                                    @foreach( $briva as $r )
			                                    <tr>
			                                        <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $r->nim }}</td>
                                                    <td>{{ $r->nm_mhs }}</td>
			                                        <td>{{ $r->nm_prodi }} ({{ $r->jenjang }})</td>
			                                        <td align="center">{{ env('brivaNo') }}</td>
                                                    <td>{{ $r->cust_code }}</td>
		                                            <td align="center">
		                                                <a href="{{ route('briva_delete',['id' => $r->id_mhs_reg])}}" onclick="return confirm('Anda ingin menghapus data ini')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
		                                            </td>
			                                    </tr>
			                                    @endforeach
			                                </tbody>
			                            </table>
                                        @if ( $briva->total() == 0 )
                                            &nbsp; Tidak ada data
                                        @endif

                                        @if ( $briva->total() > 0 )
                                            <div class="pull-left">
                                                Jumlah data : {{ $briva->total() }}
                                            </div>
                                        @endif

                                        <div class="pull-right"> 
                                            {{ $briva->render() }}
                                        </div>
			                        </div>
			                    </div>
			                </div>

			            </div>

			        </div>
			    </section>

    		</div>
  		</div>
	</div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    $(function(){

    });
    
    function filter(modul, value)
    {
        if ( modul == 'angkatan' ) {
            window.location.href='?'+modul+'='+value+'&prodi={{ Request::get('prodi') }}'; 
        } else {
            window.location.href='?'+modul+'='+value+'&angkatan={{ Request::get('angkatan') }}'; 
        }
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