@extends('layouts.app')

@section('title','Konfirmasi Pembayaran')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Konfirmasi Pembayaran
				</header>

				<div class="panel-body">

					{{ Rmt::AlertError() }}
					{{ Rmt::AlertSuccess() }}

					@if ( $pembayaran->total() == 0 )
						<div class="well bg-theme-inverse">
							<h3>Selamat datang di halaman<b> konfirmasi pembayaran</b></h3>
							<p>Halaman ini digunakan untuk mengonfirmasi pembayaran anda. Bagian keuangan akan memvalidasi pembayaran anda berdasarkan bukti pembayaran yang anda upload.<br><br>
							Klik tombol <b>"+ Konfirmasi Pembayaran"</b> untuk menambahkan konfirmasi pembayaran.</p>
						</div>
					@endif

					<button class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#modal-bayar"><i class="fa fa-plus"></i> Konfirmasi Pembayaran</button>
					<div class="table-responsive">
						<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
							<thead class="custom">
								<tr>
									<th width="20px">No.</th>
									<th>Jenis Bayar</th>
									<th>Tanggal</th>
									<th>Bukti Bayar</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody align="center">
								@foreach($pembayaran as $r)
									<tr>
										<td>{{ $loop->iteration - 1 + $pembayaran->firstItem() }}</td>
										<td align="left">{{ $r->ket }}</td>
										<td>{{ Carbon::parse($r->created_at)->format('d/m/Y') }}</td>
										<td>
											@if ( !empty($r->file) )
												<a href="{{ route('mhs_konfir_view', ['file' => $r->file]) }}" target="_blank"><i class="fa fa-picture-o fa-2x"></i></a>
											@else
												-
											@endif
										</td>
										<td>{{ Rmt::status($r->status) }}</td>
										<td>
											@if ( $r->status != '1' )
												<button class="btn btn-danger btn-xs" onclick="tombolHapus('{{ $r->id }}', 'Anda ingin menghapus konfirmasi pembayaran ini?')">
													<i class="fa fa-times"></i>
												</button>
												<form id="delete-form-{{ $r->id }}" action="{{ route('mhs_konfir_delete') }}" method="POST" style="display: none;">
													<input type="hidden" name="id" value="{{ $r->id }}">
								                    {{ csrf_field() }}
								                </form>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						@if ( $pembayaran->total() == 0 )
							&nbsp; Tidak ada data
						@endif

						@if ( $pembayaran->total() > 0 )
							<div class="pull-left">
								Jumlah data : {{ $pembayaran->total() }}
							</div>
						@endif

						<div class="pull-right"> 
							{{ $pembayaran->appends(Request::query())->links() }}
						</div>

					</div>

				</div>
			</section>
		</div>
		
	</div>
	<!-- //content > row-->
		
</div>
<!-- //content-->

<div id="modal-bayar" class="modal fade"  tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Konfirmasi Pembayaran</h4>
    </div>
    <div class="modal-body">

        <form action="{{ route('mhs_konfir_store') }}" id="form-bayar" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="form-group">
                <label>Upload Bukti Pembayaran</label>
                <input type="file" class="form-control" name="file" accept="image/*">
            </div>

	    	<div class="form-group">
				<label class="control-label">Jenis Pembayaran <span>*</span></label>
				<div>
					<select multiple="multiple" id="jenis-bayar" name="jenis_bayar[]">
						@foreach( Sia::listPembayaran() as $pb )
	                    	<option value="{{ $pb->id_jns_pembayaran }}">{{ $pb->ket }}</option>
	                    @endforeach
					</select>
				</div>
			</div>

            <hr>
        	<button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> KELUAR</button>
            <button type="submit" id="btn-submit-bayar" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
        	<br>
        	<br>
        </form>
    </div>
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
	$(document).ready(function(){

    	$('#jenis-bayar').multiSelect();

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });
    });

    function showMessage(modul,pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit-'+modul).removeAttr('disabled');
        $('#btn-submit-'+modul).html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

	function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('body').modalmanager('loading');
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {

                window.location.reload();
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
                showMessage(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('bayar');
</script>
@endsection