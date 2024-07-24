@extends('layouts.app')

@section('title','Pengawas')

@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
		<li class="h-seperate"></li>
		<li><a href="{{ route('jdk') }}">JADWAL KULIAH</a></li>
		<li class="h-seperate"></li>
		<li style="background: #b3f5ef"><a href="{{ route('jdu') }}">JADWAL UJIAN</a></li>
		<li class="h-seperate"></li>
		<li><a href="#">JADWAL ANTARA</a></li>
	</ul>
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						PENGAWAS
						<a href="{{ route('jdu') }}" class="btn btn-success btn-sm pull-right">KE JADWAL UJIAN</a>
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
												<th width="150px">Nama</th>
												<th width="80px">Tools</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach( $pengawas as $r )
											<tr>
												<td>{{ $loop->iteration - 1 + $pengawas->firstItem() }}</td>
												<td align="left">{{ $r->nama }}</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::adminOrAkademik() )
															<a href="javascript::void()" data-id="{{ $r->id }}" data-nama="{{ $r->nama }}" class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
															<a href="{{ route('pengawas_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

								@if ( $pengawas->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $pengawas->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $pengawas->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $pengawas->render() }}
								</div>

							</div>
						</div>

						<div class="col-md-4">

							<h4 id="title-form">Tambah Pengawas</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}
		                  	<form action="{{ route('pengawas_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
		                			{{ csrf_field() }}
		                			<input type="hidden" name="id" id="id_pengawas">
		                          <label class="control-label">Nama Pengawas <span>*</span></label>
		                          <input type="text" class="form-control" id="nm_pengawas" name="nama" min="1" value="{{ old('nama') }}" required="">
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
		$('#nm_pengawas').val(div.data('nama'));
		$('#id_pengawas').val(div.data('id'));
		$('#title-form').html('Ubah Pengawas');
		$('#btn-cancel').show();
		$('#form').attr('action','{{ route('pengawas_update') }}');
	});

	$('#btn-cancel').click(function(){
		$('#nm_pengawas').val('');
		$('#title-form').html('Tambah Pengawas');
		$('#form').attr('action','{{ route('pengawas_update') }}');
		$('#btn-cancel').hide();
	});
</script>
@endsection