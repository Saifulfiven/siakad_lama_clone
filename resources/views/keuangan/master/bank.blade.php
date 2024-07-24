@extends('layouts.app')

@section('title','Bank')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Bank
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
												<th width="20px">ID Bank</th>
												<th width="150px">Nama Bank</th>
												<th width="80px">Tools</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach( $bank as $r )
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $r->id }}</td>
												<td align="left">{{ $r->nm_bank }}</td>
												<td>
													<span class="tooltip-area">
														<a href="javascript::void()" data-id="{{ $r->id }}" data-nama="{{ $r->nm_bank }}" class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
														<a href="{{ route('ms_bank_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

							</div>
						</div>

						<div class="col-md-4">

							<h4 id="title-form">Tambah Bank</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}
		                  	<form action="{{ route('ms_bank_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
		                			{{ csrf_field() }}
		                			<input type="hidden" name="id" id="id-bank">
		                          <label class="control-label">Nama Bank <span>*</span></label>
		                          <input type="text" class="form-control" id="nm_bank" name="nm_bank" min="1" value="{{ old('nm_bank') }}" required="">
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
		$('#nm_bank').val(div.data('nama'));
		$('#id-bank').val(div.data('id'));
		$('#title-form').html('Ubah Bank');
		$('#btn-cancel').show();
		$('#form').attr('action','{{ route('ms_bank_update') }}');
	});

	$('#btn-cancel').click(function(){
		$('#nm_bank').val('');
		$('#title-form').html('Tambah Bank');
		$('#form').attr('action','{{ route('ms_bank_store') }}');
		$('#btn-cancel').hide();
	});
</script>
@endsection