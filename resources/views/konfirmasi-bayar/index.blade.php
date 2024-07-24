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
					<select onchange="filter('smt', this.value)" id="filter-smt">
						@foreach( Sia::listSemester() as $smt )
							<option value="{{ $smt->id_smt }}" {{ Session::get('konfir.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
						@endforeach
					</select>
				</header>

				<div class="panel-body">

					{{ Rmt::AlertError() }}
					{{ Rmt::AlertSuccess() }}

					<table border="0" width="100%" style="margin-bottom: 10px">
						<tr>
							<td width="180">
								<select class="form-custom mw-2" onchange="filter('jenis_bayar', this.value)" id="list-prodi">
									<option value="all" {{ Session::get('konfir.jenis_bayar') == 'all' ? 'selected':'' }}>All Jenis Bayar</option>
									@foreach( Sia::listPembayaran() as $pb )
				                    	<option value="{{ $pb->id_jns_pembayaran }}" {{ Session::get('konfir.jenis_bayar') == $pb->id_jns_pembayaran ? 'selected' : '' }}>{{ $pb->ket }}</option>
				                    @endforeach
								</select>
							</td>
							<td width="220">
								<select class="form-custom mw-2" onchange="filter('prodi', this.value)">
									<option value="all">Semua Prodi</option>
									@foreach( Sia::listProdi() as $pr )
				                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('konfir.prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
				                    @endforeach
								</select>
							</td>

							<td width="">
								<select class="form-custom mw-2" onchange="filter('status', this.value)">
									<option value="all" {{ Session::get('konfir.status') == 'all' ? 'selected':'' }}>ALL STATUS BAYAR</option>
									<option value="99" {{ Session::get('konfir.status') == '0' ? 'selected':'' }}>Belum di Validasi</option>
									<option value="1" {{ Session::get('konfir.status') == '1' ? 'selected':'' }}>Disetujui</option>
									<option value="2" {{ Session::get('konfir.status') == '2' ? 'selected':'' }}>Ditolak</option>
								</select>
							</td>

							<td width="300px">
								<form action="{{ route('keu_konfir_cari') }}" id="form-cari">
									<div class="input-group pull-right">
										<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('konfir.cari') }}">
										<div class="input-group-btn">
											<a href="{{ route('keu_konfir_cari') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
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
									<th>Mahasiswa</th>
									<th>Jenis Bayar</th>
									<th>Tanggal</th>
									<th>Bukti Bayar</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody align="center">
								@foreach($mahasiswa as $r)
									
									<?php $nm_mhs =  $r->nim.' - '. $r->nm_mhs ?>

									<tr>
										<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
										<td align="left">{{ $nm_mhs }}</td>
										<td>{{ $r->ket }}</td>
										<td>{{ Carbon::parse($r->created_at)->format('d/m/Y') }}</td>
										<td>
											@if ( !empty($r->file) )
												<a href="{{ route('mhs_konfir_view', ['file' => $r->file]) }}" target="_blank">
													<!-- <i class="fa fa-picture-o fa-2x"></i> -->
													<?php $icon = Rmt::icon($r->file); ?>
                                                    <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
												</a>
											@else
												-
											@endif
										</td>
										<td>{{ Rmt::status($r->status) }}</td>
										<td>
											@if ( $r->status == 0 || $r->status == 2)
												<button class="btn btn-success btn-sm"
													onclick="proses('{{ $r->id }}', '{{ $nm_mhs }}','{{ $r->ket }}')">Setujui</button>
												&nbsp; 

												@if ( $r->status == 0 )
													<button class="btn btn-danger btn-sm" onclick="tombolHapus('{{ $r->id }}', 'Anda ingin menolak konfirmasi pembayaran ini?')">
														Tolak
													</button>
													<form id="delete-form-{{ $r->id }}" action="{{ route('keu_konfir_store') }}" method="POST" style="display: none;">
														<input type="hidden" name="id" value="{{ $r->id }}">
														<input type="hidden" name="tolak" value="1">
									                    {{ csrf_field() }}
									                </form>
								                @endif
								            @else

								            	-

								            @endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						@if ( $mahasiswa->total() == 0 )
							&nbsp; Tidak ada data
						@endif

						@if ( $mahasiswa->total() > 0 )
							<div class="pull-left">
								Jumlah data : {{ $mahasiswa->total() }}
							</div>
						@endif

						<div class="pull-right"> 
							{{ $mahasiswa->appends(Request::query())->links() }}
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

        <form action="{{ route('keu_konfir_store') }}" id="form-bayar" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id" id="id-kbd">

            <table class="table" width="100%" border="0">
	            <tr>
	        		<td style="padding: 10px 0">Mahasiswa</td>
	        		<td id="mahasiswa"></td>
	        	</tr>
	        	<tr>
	        		<td style="padding: 10px 0">Jenis Pembayaran</td>
	        		<td id="jenis-bayar"></td>
	        	</tr>
	        	<tr>
	        		<td>Jumlah Bayar</td>
	        		<td>
	        			<input type="text" name="jml_bayar" class="form-control">
	        		</td>
	        	</tr>
	        </table>

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

    	$('#nav-mini').trigger('click');

    	var ta = $('#filter-smt option:selected').text();
    	$('.ta').html(ta);

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

		$(document).on( "keyup", 'input[name="jml_bayar"]', function( event ) {

            var selection = window.getSelection().toString();
            if ( selection !== '' ) {
                return;
            }
            
            if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
                return;
            }
            
            
            var $this = $( this );
            
            var input = $this.val();

            var input = input.replace(/[\D\s\._\-]+/g, "");
            input = input ? parseInt( input, 10 ) : 0;

            $this.val( function() {
                return ( input === 0 ) ? "" : input.toLocaleString();
            } );
        });
    });


	function proses(id_kbd, mhs, jenis_bayar)
	{
		$('#id-kbd').val(id_kbd);
		$('#mahasiswa').html(mhs);
		$('#jenis-bayar').html(jenis_bayar);
		$('#modal-bayar').modal('show');
	}

    function filter(modul, value)
    {
    	window.location.href='{{ route('keu_konfir_filter') }}?modul='+modul+'&val='+value;
    }

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