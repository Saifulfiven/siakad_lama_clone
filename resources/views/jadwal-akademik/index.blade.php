@extends('layouts.app')

@section('title','Jadwal Akademik')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Jadwal Akademik
					</header>

			        <div class="panel-body" style="padding: 3px 3px;">
			            
			            <div class="col-md-12">

			                {{ Rmt::AlertSuccess() }}
			                {{ Rmt::AlertError() }}
			                {{ Rmt::AlertErrors($errors) }}

			                <div class="row">

			                    <div class="col-md-12">

			                        <div class="table-responsive">
			                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
			                                <thead class="custom">
			                                    <tr>
			                                        <th width="20px">No.</th>
			                                        <th>Fakultas</th>
			                                        <th>Awal Pembayaran</th>
			                                        <th>Akhir Pembayaran</th>
                                                    <th>Awal KRS</th>
                                                    <th>Akhir KRS</th>
                                                    <th>Awal Kuliah</th>
                                                    <th>Input nilai SP</th>
			                                        <th width="40px">Aksi</th>
			                                    </tr>
			                                </thead>
			                                <tbody align="center">
			                                    @foreach( $jadwal as $r )
			                                    <tr>
			                                        <td>{{ $loop->iteration }}</td>
			                                        <td align="left">{{ $r->nm_fakultas }}</td>
                                                    <td>{{ Carbon::parse($r->awal_pembayaran)->format('d/m/Y') }}</td>
			                                        <td>{{ Carbon::parse($r->akhir_pembayaran)->format('d/m/Y') }}</td>
                                                    <td>{{ Carbon::parse($r->awal_krs)->format('d/m/Y') }}</td>
			                                        <td>{{ Carbon::parse($r->akhir_krs)->format('d/m/Y') }}</td>
                                                    <td>{{ Carbon::parse($r->awal_kuliah)->format('d/m/Y') }}</td>
                                                    <td><?= $r->input_nilai_sp == 0 ? '<i class="fa fa-ban" style="color: red"></i>' : '<i class="fa fa-check" style="color: green"></i>' ?></td>
                                                    <td>
                                                        @if ( Sia::role('admin') )
		                                                  <a href="javascript:void(0)" onclick="edit('{{ $r->id }}')" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a>
		                                                 @endif
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

<div id="modal-edit" class="modal fade" data-width="400" tabindex="-1" style="top: 20%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Ubah jadwal akademik</h4>
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
            url: '{{ route('ja_edit') }}/'+id,
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
    submit('jadwal');
</script>
@endsection