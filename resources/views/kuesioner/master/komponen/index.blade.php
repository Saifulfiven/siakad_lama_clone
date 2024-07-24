@extends('layouts.app')

@section('title','Komponen')

@section('content')

	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Komponen Kuesioner
					</header>

					<div class="panel-body">

						<div class="col-md-8">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}

							Filter Prodi : 
							<select class="custom" onchange="filterProdi(this.value)">
								<option value="">Semua</option>
								@foreach( Sia::listProdi() as $pr )
									<option value="{{ $pr->id_prodi }}" {{ Request::get('prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
								@endforeach
							</select>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th width="150px">Prodi</th>
											<th>Judul</th>
											<th>Jenis</th>
											<th>Urutan</th>
											<th>Aktif</th>
											<th width="80px">Tools</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach( $komponen as $r )
											<tr>
												<td>{{ $loop->iteration - 1 + $komponen->firstItem() }}</td>
												<td align="left">{{ $r->jenjang.' '.$r->nm_prodi }}</td>
												<td align="left"><a href="{{ route('kues_komponen_isi') }}?komponen={{ $r->id }}">{{ $r->judul }}</a></td>
												<td>{{ $r->jenis == 'pg' ? 'PILIHAN':'ISIAN' }}</td>
												<td>{{ $r->urutan }}</td>
												<td>{{ $r->aktif == 1 ? 'AKTIF':'NON AKTIF' }}</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::jurusan() )
															<a href="javascript::void()" 
																data-id="{{ $r->id }}"
																data-prodi="{{ $r->id_prodi }}"
																data-judul="{{ $r->judul }}" 
																data-jenis="{{ $r->jenis }}"
																data-urutan="{{ $r->urutan }}"
																data-aktif="{{ $r->aktif }}"
																class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
															<a href="{{ route('kues_komponen_delete', ['id' => $r->id]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							
							{{ count($komponen) == 0 ? 'Belum ada data':'' }}

							{!! $komponen->appends(array_except(Request::query(),'page'))
									->links() !!}
						</div>

						<div class="col-md-4">

							<h4 id="title-form">Tambah Komponen</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}

		                  	<form action="{{ route('kues_komponen_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
	                			{{ csrf_field() }}
	                			<input type="hidden" name="id" id="id-komponen">
		                        
		                        <label class="control-label">Judul <span>*</span></label>
								<input type="text" class="form-control" id="judul" name="judul" value="{{ old('judul') }}" required="">

								<label class="control-label">Program Studi <span>*</span></label>
								<select name="id_prodi" class="form-control" id="prodi">
									<option value="">--Pilih Program Studi--</option>
									@foreach( Sia::listProdi() as $pr )
										<option value="{{ $pr->id_prodi }}">{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
									@endforeach
								</select>

								<label class="control-label">Jenis <span>*</span></label>
								<select name="jenis" class="form-control" id="jenis">
									<option value="pg">PILIHAN</option>
									<option value="essay">ISIAN</option>
								</select>

								<label class="control-label">Status <span>*</span></label>
								<select name="aktif" class="form-control" id="aktif">
									<option value="1">AKTIF</option>
									<option value="0">NON AKTIF</option>
								</select>

								<label class="control-label">Urutan <span>*</span></label>
								<input type="number" class="form-control" id="urutan" name="urutan" value="{{ $urutan }}" required="">

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
		$('#judul').val(div.data('judul'));
		$('#id-komponen').val(div.data('id'));
		$('#prodi').val(div.data('prodi'));
		$('#aktif').val(div.data('aktif'));
		$('#jenis').val(div.data('jenis'));
		$('#urutan').val(div.data('urutan'));
		$('#title-form').html('Ubah Komponen');
		$('#btn-cancel').show();
		$('#form').attr('action','{{ route('kues_komponen_update') }}');
	});

	$('#btn-cancel').click(function(){
		$('#judul').val('');
		$('#title-form').html('Tambah Komponen');
		$('#form').attr('action','{{ route('kues_komponen_store') }}');
		$('#btn-cancel').hide();
	});

	$('#prodi').change(function(){
		prodi = $(this).val();

		$.get('{{ route('kues_ajax') }}', {prodi:prodi, tipe: 'urut-komponen'}, function(data){
			$('#urutan').val(data);
		});
	});

	function filterProdi(prodi)
	{
		window.location.href = '?prodi='+prodi;
	}
</script>
@endsection