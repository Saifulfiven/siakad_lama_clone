@extends('layouts.app')

@section('title','Kelas')

@section('content')

	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						KELAS
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
											<th width="150px">Prodi</th>
											<th width="150px">Nama Kelas</th>
											<th width="150px">Ket</th>
											<th width="80px">Tools</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach( $kelas as $r )
											<tr>
												<td>{{ $loop->iteration - 1 + $kelas->firstItem() }}</td>
												<td align="left">{{ $r->jenjang.' '.$r->nm_prodi }}</td>
												<td>{{ $r->nm_kelas }}</td>
												<td>{{ $r->ket }}</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::adminOrAkademik() )
															<a href="javascript::void()" 
																data-id="{{ $r->id }}"
																data-prodi="{{ $r->id_prodi }}"
																data-nama="{{ $r->nm_kelas }}" 
																data-ket="{{ $r->ket }}"
																class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
															<a href="{{ route('m_kelas_delete', ['id' => $r->id]) }}?nm_kelas={{ $r->nm_kelas }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

								{!! $kelas->links() !!}

							</div>
						</div>

						<div class="col-md-4">

							<h4 id="title-form">Tambah Kelas</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}

		                  	<form action="{{ route('m_kelas_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
	                			{{ csrf_field() }}
	                			<input type="hidden" name="id" id="id-kelas">
		                          
								<label class="control-label">Program Studi <span>*</span></label>
								<select name="prodi" class="form-control" id="prodi">
									<option value="">--Pilih Program Studi--</option>
									@foreach( Sia::listProdi() as $pr )
										<option value="{{ $pr->id_prodi }}">{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
									@endforeach
								</select>

								<label class="control-label">Keterangan <span>*</span></label>
								<select name="ket" class="form-control" id="ket">
									<option value="PAGI">PAGI</option>
									<option value="SIANG">SIANG</option>
									<option value="MALAM">MALAM</option>
								</select>

								<label class="control-label">Nama Kelas <span>*</span></label>
								<input type="text" class="form-control" id="nm_kelas" name="nm_kelas" min="1" value="{{ old('nm_kelas') }}" required="">


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
		$('#nm_kelas').val(div.data('nama'));
		$('#id-kelas').val(div.data('id'));
		$('#prodi').val(div.data('prodi'));
		$('#ket').val(div.data('ket'));
		$('#title-form').html('Ubah Kelas');
		$('#btn-cancel').show();
		$('#form').attr('action','{{ route('m_kelas_update') }}');
	});

	$('#btn-cancel').click(function(){
		$('#nm_kelas').val('');
		$('#title-form').html('Tambah Kelas');
		$('#form').attr('action','{{ route('m_kelas_store') }}');
		$('#btn-cancel').hide();
	});
</script>
@endsection