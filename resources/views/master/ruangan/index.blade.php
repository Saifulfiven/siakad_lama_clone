@extends('layouts.app')

@section('title','Ruangan')

@section('content')

	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Ruangan
					</header>

					<div class="panel-body">

						<div class="col-md-8">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th width="150px">Kode</th>
											<th>Nama Ruangan</th>
											<th width="80px">Tools</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach( $ruangan as $r )
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $r->id }}</td>
												<td>{{ $r->nm_ruangan }}</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::adminOrAkademik() )
															<a href="javascript::void()" 
																data-id="{{ $r->id }}"
																data-nama="{{ $r->nm_ruangan }}"
																class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
															<a href="{{ route('m_ruangan_delete', ['id' => $r->id]) }}?nm_ruangan={{ $r->nm_ruangan }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

							</div>
						</div>

						<div class="col-md-4">

							<h4 id="title-form">Tambah Ruangan</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}

		                  	<form action="{{ route('m_ruangan_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
	                			{{ csrf_field() }}

	                			<label class="control-label">Kode Ruangan <span>*</span></label>
								<input type="text" class="form-control" id="id-ruangan" name="kode" value="{{ old('kode') }}" required="">

								<label class="control-label">Nama Ruangan <span>*</span></label>
								<input type="text" class="form-control" id="nm_ruangan" name="nm_ruangan" value="{{ old('nm_ruangan') }}" required="">


								<button class="btn btn-primary btn-xs pull-right" id="btn-submit" style="margin: 6px 0px"><i class="fa fa-floppy-o"></i> SIMPAN</button>
								<button type="button" class="btn btn-warning btn-xs pull-left" id="btn-cancel" style="margin: 6px 0px;display:none"><i class="fa fa-times"></i> BATAL</button>
							</form>
		                </div>

  					</div>

				</section>
			</div>
				
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

@endsection

@section('registerscript')
<script>
	$('.ubah').click(function(){
		var div = $(this);
		$('#nm_ruangan').val(div.data('nama'));
		$('#id-ruangan').val(div.data('id'));
		$('#id-ruangan').attr('readonly','');
		$('#title-form').html('Ubah Ruangan');
		$('#btn-cancel').show();
		$('#form').attr('action','{{ route('m_ruangan_update') }}');
	});

	$('#btn-cancel').click(function(){
		$('#nm_ruangan').val('');
		$('#title-form').html('Tambah Ruangan');
		$('#id-ruangan').removeAttr('readonly');
		$('#id-ruangan').val('');
		$('#form').attr('action','{{ route('m_ruangan_store') }}');
		$('#btn-cancel').hide();
	});
</script>
@endsection