@extends('layouts.app')

@section('title','Semester')

@section('content')

	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Semester
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
											<th width="150px">Id Smt</th>
											<th>Nama Semester</th>
											<th>Ket Smt</th>
											<th>Status</th>
											<th width="80px">Tools</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach( $semester as $r )
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $r->id_smt }}</td>
												<td>{{ $r->nm_smt }}</td>
												<td>{{ $r->smt == 1 ? 'GANJIL':'GENAP' }}</td>
												<td>
													<div class="col-sm-12 iSwitch flat-switch">
														<div class="switch switch-mini">
															<input type="checkbox" value="{{ $r->id_smt }}" {{ $r->aktif == 1 ? 'checked':'' }}>
														</div>
													</div>
												</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::adminOrAkademik() )
															<a href="javascript::void()" 
																data-id="{{ $r->id_smt }}"
																data-nama="{{ $r->nm_smt }}" 
																data-ket="{{ $r->smt }}"
																class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
															<a href="{{ route('m_semester_delete', ['id' => $r->id_smt]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
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

							<h4 id="title-form">Tambah Kelas</h4>
							<br>
		                  	{{ Rmt::alertErrors($errors) }}

		                  	<form action="{{ route('m_semester_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
	                			{{ csrf_field() }}

								<label class="control-label">ID Semester <span>*</span></label>
								<input type="text" class="form-control" id="id_smt" name="id_smt" value="{{ old('id_smt') }}" required="">

								<label class="control-label">Nama Semester <span>*</span></label>
								<input type="text" class="form-control" id="nm_semester" name="nm_semester" value="{{ old('nm_semester') }}" required="">
								
								<label class="control-label">Keterangan <span>*</span></label>
								<select name="ket" class="form-control" id="ket">
									<option value="1">GANJIL</option>
									<option value="2">GENAP</option>
								</select>

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
	$(function(){
		// $('.status').change(function(e){
		$('.switch').on('switch-change', function (e, data) {
			id = data.el.context.defaultValue;
			$.ajax({
	    		url: '{{ route('m_semester_update_status') }}',
	    		data : {id:id,status:data.value},
	    		error: function(data,status,msg){
	    			alert(msg);
	    		}
	    	});
		});

		$('.ubah').click(function(){
			var div = $(this);
			$('#nm_semester').val(div.data('nama'));
			$('#id_smt').val(div.data('id'));
			$('#id_smt').attr('readonly','');
			$('#ket').val(div.data('ket'));
			$('#title-form').html('Ubah Semester');
			$('#btn-cancel').show();
			$('#form').attr('action','{{ route('m_semester_update') }}');
		});

		$('#btn-cancel').click(function(){
			$('#nm_semester').val('');
			$('#id_smt').removeAttr('readonly','');
			$('#id_smt').val('');
			$('#title-form').html('Tambah Semester');
			$('#form').attr('action','{{ route('m_semester_store') }}');
			$('#btn-cancel').hide();
		});
	});
</script>
@endsection