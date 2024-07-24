@extends('layouts.app')

@section('title','Matakuliah')

@section('heading')
	<style type="text/css">
    .panel-title{
      font-size: 12px;
    }
    .panel-group {
    	margin-bottom: 2px;
    }
    .panel-group .panel-heading {
    	padding: 0 5px;
    }
    .panel-group .panel-body {
		  padding: 3px 3px 3px 15px !important;
		  max-height: 200px !important;
		  overflow-y: scroll !important;
		}
    a[data-toggle="collapse"] {
    	color: #222;
    }

</style>
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
			
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Matakuliah
					</header>

					<div class="panel-body">

						<div class="col-md-3" style="padding-left: 0">
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
									<thead class="custom">
										<tr><th>
												FILTER 
												<span class="tooltip-area pull-right">
													<?php if ( Session::has('mk_prodi') || Session::has('mk_jenis') || Session::has('mk_kelompok')) { ?>
													<a href="{{ route('matakuliah_filter') }}" class="btn btn-warning btn-xs" title="Hapus&nbsp;Filter"><i class="fa fa-filter"></i></a>
													<?php } ?>
												</span>
										</th></tr>
									</thead>
									<tbody>
										<tr><td>

											<!-- Filer -->

											<div class="panel-group" id="accordion">
									        <div class="panel panel-info">
									            <div class="panel-heading">
									                <h4 class="panel-title">
									                    <a data-toggle="collapse" data-parent="#accordion" href="#programStudi"><span class="glyphicon glyphicon-plus"></span> Program Studi</a>
									                </h4>
									            </div>
									            <div id="programStudi" class="panel-collapse collapse {{ Session::has('mk_prodi') ? 'in':'' }}">
									                <div class="panel-body">
									                    @foreach( Sia::listProdi() as $r )
									                    	<a href="javascript:void(0)" id="prodi-{{ $r->id_prodi }}" onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }} {{ $r->nm_prodi }}</a>
										                    <?php
									                    	if ( Session::has('mk_prodi') && in_array($r->id_prodi,Session::get('mk_prodi')) ) { ?>
									                    		<i class="filter fa fa-filter"></i>
									                    	<?php } ?>
									                    	<br>
									                    @endforeach
									                </div>
									            </div>
									        </div>
									        <div class="panel panel-info">
									            <div class="panel-heading">
									                <h4 class="panel-title">
									                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseJenis"><span class="glyphicon glyphicon-plus"></span> Jenis Matakuliah</a>
									                </h4>
									            </div>
									            <div id="collpaseJenis" class="panel-collapse collapse {{ Session::has('mk_jenis') ? 'in':'' }}">
									                <div class="panel-body">
									                	@foreach( Sia::jenisMatakuliah('array') as $key => $value )
								                    	<a href="javascript:void(0);" id="jenis-{{ $key }}" onclick="filter('{{ $key }}','jenis')">{{ $value }}</a>
								                    	<?php
								                    	if ( Session::has('mk_jenis') && in_array($key,Session::get('mk_jenis')) ) { ?>
								                    		<i class="filter fa fa-filter"></i>
								                    	<?php } ?>
								                    	<br>
								                    @endforeach
									                </div>
									            </div>
									        </div>
									        <div class="panel panel-info">
									            <div class="panel-heading">
									                <h4 class="panel-title">
									                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseKelompok"><span class="glyphicon glyphicon-plus"></span> Kelompok Matakuliah</a>
									                </h4>
									            </div>
									            <div id="collpaseKelompok" class="panel-collapse collapse {{ Session::has('mk_kelompok') ? 'in':'' }}">
									                <div class="panel-body">
									                	@foreach( Sia::kelompokMatakuliah('array') as $key => $value )
								                    	<a href="javascript:void(0);" id="kelompok-{{ $key }}" onclick="filter('{{ $key }}','kelompok')">{{ $value }}</a>
								                    	<?php
								                    	if ( Session::has('mk_kelompok') && in_array($key,Session::get('mk_kelompok')) ) { ?>
								                    		<i class="filter fa fa-filter"></i>
								                    	<?php } ?>
								                    	<br>
								                    @endforeach
									                </div>
									            </div>
									        </div>
									    </div>

										</td></tr>
									</tbody>
								</table>
							</div>
						</div>

						<div class="col-md-9">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
										<td>
											<button class="btn btn-success btn-sm"  data-toggle="modal" data-target="#modal-ekspor"><i class="fa fa-print"></i> EKSPOR</button>
										</td>

									<td width="300px">
										<form action="{{ route('matakuliah_cari') }}" method="post" id="form-cari">
											<div class="input-group pull-right">
													{{ csrf_field() }}
													<input type="text" class="form-control input-sm" name="q" value="{{ Session::get('mk_search') }}">
													<div class="input-group-btn">
															<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
															<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
													</div>
											</div>
										</form>
									</td>
									@if ( Sia::adminOrAkademik() )
										<td width="110px">
											<a href="{{ route('matakuliah_add') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
										</td>
									@endif

								</tr>
							</table>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
											<tr>
												<th width="20px">No.</th>
												<th>Kode MK</th>
												<th>Nama Matakuliah</th>
												<th>SKS</th>
												<th>Program Studi</th>
												<th>Jenis MK</th>
												<th>Kelompok MK</th>
												<th>Aksi</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach($matakuliah as $r)
											<tr>
												<td>{{ $loop->iteration - 1 + $matakuliah->firstItem() }}</td>
												<td align="left"><a href="{{ route('matakuliah_detail', ['id' => $r->id])}}">{{ $r->kode_mk }}</a></td>
												<td align="left">{{ $r->nm_mk }}</td>
												<td>{{ $r->sks_mk }}</td>
												<td align="left">{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
												<td>{{ Sia::jenisMatakuliah($r->jenis_mk) }}</td>
												<td align="left">{{ Sia::kelompokMatakuliah($r->kelompok_mk) }}</td>
												<td>
													<span class="tooltip-area">
													@if ( Sia::adminOrAkademik() )
														@if ( $r->terpakai == 0 )
															<a href="{{ route('matakuliah_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a>
															<a href="{{ route('matakuliah_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@else
															@if ( Sia::adminOrAkademik() )
																<a href="{{ route('matakuliah_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a>
															@endif
														@endif
													@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								@if ( $matakuliah->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $matakuliah->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $matakuliah->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $matakuliah->render() }}
								</div>

							</div>
						</div>
					</div>
				</section>
			</div>
			
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

	<div id="modal-ekspor" class="modal fade" style="top:30%" tabindex="-1" data-width="300">
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Ekspor Mahasiswa</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<center>
				<a href="{{ route('matakuliah_excel') }}" class="btn btn-sm btn-primary"><i class="fa fa-file-text"></i> EXCEL</a>&nbsp; 
				<a href="{{ route('matakuliah_print') }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> CETAK</a>
			</center>
		</div>
		<!-- //modal-body-->
	</div>

@endsection

@section('registerscript')
<script>

    $(document).ready(function(){
        $(".collapse.in").each(function(){
        	$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
        });
        
        $(".collapse").on('show.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hide.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });

        $('#nav-mini').trigger('click');

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });
    });

    function filter(value,modul)
    {
    	$('#'+modul+'-'+value).html('<i class="fa fa-spinner fa-spin"></i>');

    	$.ajax({
    		url: '{{ route('matakuliah_filter') }}',
    		data: { value: value, modul: modul },
    		success: function(result){
    			var param = '{{ Request::has('page') }}';
    			if ( param === '' ) {
    				window.location.reload();
    			} else {
    				window.location.href='{{ route('mahasiswa') }}';
    			}
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	})
    }

</script>
@endsection