@extends('layouts.app')

@section('title','Biaya Kuliah')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Biaya Kuliah
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							
							<div class="table-responsive">
								<table border="0">
									<tr>
										<td>Tahun</td>
										<td>: 
											<select class="form-custom mw-2" onchange="filter('tahun', this.value)" id="list-prodi">
												<option value="">Semua</option>
												@for( $i = 2016; $i <= date('Y'); $i++ )
							                    	<option value="{{ $i }}" {{ Request::get('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
							                    @endfor
											</select>
										</td>
										<td>Prodi</td>
										<td>: 
											<select class="form-custom mw-2" onchange="filter('prodi', this.value)" id="list-prodi">
												<option value="">Semua</option>
												@foreach( Sia::listProdi() as $pr )
							                    	<option value="{{ $pr->id_prodi }}" {{ Request::get('prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
							                    @endforeach
											</select>
										</td>
									</tr>
								</table>
								<br>
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
											<tr>
												<th width="20px">No.</th>
												<th>Prodi</th>
												<th>Tahun</th>
												<th>SPP</th>
												<th>BPP</th>
												<th>Seragam</th>
												<th>Lainnya</th>
												<th width="80px">Tools</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach( $biaya as $r )
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td align="left">{{ $r->prodi->jenjang }} {{ $r->prodi->nm_prodi }}</td>
												<td>{{ $r->tahun }}</td>
												<td align="left">{{ Rmt::rupiah($r->spp) }}</td>
												<td align="left">{{ Rmt::rupiah($r->bpp) }}</td>
												<td align="left">{{ Rmt::rupiah($r->seragam) }}</td>
												<td align="left">{{ Rmt::rupiah($r->lainnya) }}</td>
												<td>
													<span class="tooltip-area">
														<a href="javascript::void()" onclick="ubah('{{ $r->tahun }}','{{ $r->prodi->id_prodi }}')" class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a>
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

							</div>
						</div>

  					</div>

				</section>
			</div>
				
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

	<div id="modal-edit" class="modal fade" data-width="500" tabindex="-1" style="top: 40% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Ubah Biaya Kuliah</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="col-md-12">
		        <form action="{{ route('ms_biaya_update') }}" id="form-biaya" method="post">

		        	<div id="data-edit">
		        		<center><br><br><br><i class="fa fa-spinner fa-spin"></i><br><br><br></center>
		        	</div>

		            <hr>
		        	<button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> KELUAR</button>
		            <button type="submit" id="btn-submit-bayar" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
		        	<br>
		        	<br>
		        </form>
		    </div>
	    </div>
	</div>

@endsection

@section('registerscript')
<script>
    $(function(){

		$(document).on( "keyup", '.biaya', function( event ) {

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
	        });
	    });
	 });
	            	
	function ubah(tahun, id_prodi)
	{
		$('#modal-edit').modal('show');
		$('#data-edit').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
		
    	$.ajax({
    		url: '{{ route('ms_biaya_edit') }}',
    		data: { tahun: tahun, id_prodi: id_prodi },
    		success: function(result){
    			$('#data-edit').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
	}

	function filter(modul,value)
	{
		var url;
		if ( modul == 'tahun' ) {
			url = '?tahun='+value+'&prodi={{ Request::get('prodi')}}';
		} else {
			url = '?tahun={{ Request::get('tahun')}}&prodi='+value;
		}
		window.location.href=url;
	}
</script>
@endsection