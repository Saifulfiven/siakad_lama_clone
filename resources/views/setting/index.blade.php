@extends('layouts.app')

@section('title','Pengaturan')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Pengaturan Sistem
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
												<th width="150px">ID</th>
												<th>Value</th>
												<th>Aksi</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach( $setting as $r )
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td align="left">{{ $r->id }}</td>
												<td align="left">{{ $r->value }}</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::adminOrAkademik() )
															<a href="javascript::void()" data-id="{{ $r->id }}" data-nama="{{ $r->value }}" class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a>
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

							<h4 id="title-form">Ubah Pengaturan</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}
		                  	<form action="{{ route('set_update') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
		                			{{ csrf_field() }}
		                			<input type="hidden" name="id" id="id">
		                          <label class="control-label">Value <span>*</span></label>
		                          <input type="text" class="form-control" id="value" name="value" min="1" value="{{ old('value') }}" required="">
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
		$('#value').val(div.data('nama'));
		$('#id').val(div.data('id'));
		$('#btn-cancel').show();
	});

	$('#btn-cancel').click(function(){
		$('#value').val('');
		$('#btn-cancel').hide();
	});
</script>
@endsection