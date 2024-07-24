@extends('layouts.app')

@section('title','Isi Komponen')

@section('content')

	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Komponen Isi Kuesioner
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

							Filter Komponen : 
							<select class="custom" style="max-width: 300px" onchange="filterKomponen(this.value)">
								<option value="">Semua</option>
								@foreach( $komponen as $ko )
									<option value="{{ $ko->id }}" {{ Request::get('komponen') == $ko->id ? 'selected':'' }}>{{ $ko->judul }}</option>
								@endforeach
							</select>

							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>Prodi</th>
											<th>Komponen</th>
											<th>Pertanyaan</th>
											<th>Status</th>
											<th>Urutan</th>
											<th width="80px">Tools</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach( $komponen_isi as $r )
											<tr style="font-size: 11px">
												<td>{{ $loop->iteration - 1 + $komponen_isi->firstItem() }}</td>
												<td align="left">{{ str_limit($r->jenjang.' '.$r->nm_prodi, 8) }}</td>
												<td align="left">{{ str_limit($r->judul,20) }}</td>
												<td align="left" width="200">{{ $r->pertanyaan }}</td>
												<td>{{ $r->aktif == 1 ? 'AKTIF':'NON AKTIF' }}</td>
												<td>{{ $r->urutan }}</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::jurusan() )
															<a href="javascript::void()" 
																data-id="{{ $r->id }}"
																data-prodi="{{ $r->id_prodi }}"
																data-judul="{{ $r->judul }}" 
																data-komponen="{{ $r->id_komponen }}" 
																data-urutan="{{ $r->urutan }}"
																data-aktif="{{ $r->aktif }}"
																data-pertanyaan="{{ $r->pertanyaan }}"
																class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
															<a href="{{ route('kues_komponen_isi_delete', ['id' => $r->id]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

							</div>

							{{ count($komponen_isi) == 0 ? 'Belum ada data':'' }}

							{!! $komponen_isi->appends(array_except(Request::query(),'page'))
									->links() !!}
						</div>

						<div class="col-md-4">

							<h4 id="title-form">Tambah Komponen Isi</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}

		                  	<form action="{{ route('kues_komponen_isi_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
	                			{{ csrf_field() }}
	                			<input type="hidden" name="id" id="id-komponen-isi">
		                          
								<label class="control-label">Komponen <span>*</span></label>
								<select name="id_komponen" class="form-control" id="komponen">
									<option value="">--Pilih Komponen--</option>
									@foreach( $komponen as $ko )
										<option value="{{ $ko->id }}">{{ $ko->judul }}</option>
									@endforeach
								</select>

								<label class="control-label">Pertanyaan <span>*</span></label>
								<textarea name="pertanyaan" id="pertanyaan" class="form-control"></textarea>

								<label class="control-label">Status <span>*</span></label>
								<select name="aktif" class="form-control" id="aktif">
									<option value="1">AKTIF</option>
									<option value="0">NON AKTIF</option>
								</select>

								<label class="control-label">Urutan <span>*</span></label>
								<input type="number" class="form-control" id="urutan" name="urutan" value="1" required="">

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
		$('#id-komponen-isi').val(div.data('id'));
		$('#komponen').val(div.data('komponen'));
		$('#urutan').val(div.data('urutan'));
		$('#aktif').val(div.data('aktif'));
		$('#pertanyaan').val(div.data('pertanyaan'));
		$('#title-form').html('Ubah Kelas');
		$('#btn-cancel').show();
		$('#form').attr('action','{{ route('kues_komponen_isi_update') }}');
	});

	$('#btn-cancel').click(function(){
		$('#judul').val('');
		$('#title-form').html('Tambah Kelas');
		$('#form').attr('action','{{ route('kues_komponen_isi_store') }}');
		$('#btn-cancel').hide();
	});

	$('#komponen').change(function(){
		komponen = $(this).val();

		$.get('{{ route('kues_ajax') }}', {komponen:komponen, tipe: 'urut-komponen-isi'}, function(data){
			$('#urutan').val(data);
		});
	});

	function filterProdi(prodi)
	{
		window.location.href = '?prodi='+prodi;
	}

	function filterKomponen(komponen)
	{
		window.location.href = '?komponen='+komponen;
	}
</script>
@endsection