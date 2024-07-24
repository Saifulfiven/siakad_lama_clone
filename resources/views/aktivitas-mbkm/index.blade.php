@extends('layouts.app')

@section('title','Aktivitas Perkuliahan')

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


@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
		<li class="h-seperate"></li>
		<li><a>Aktivitas MBKM</a></li>
	</ul>
@endsection

@section('content')

	<div id="overlay"></div>

	<div id="content">

		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">

					<div class="panel-body">

						<div class="col-md-3" style="padding-left: 0">
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
									<thead class="custom">
										<tr><th>
											FILTER 
											<span class="tooltip-area pull-right">
												<?php if ( Session::has('mbkm_prodi') || Session::has('mbkm_angkatan') || Session::has('mbkm_status')  || Session::has('mbkm_ta') || Session::has('mbkm_search') ) { ?>
												<a href="{{ route('mbkm_filter') }}" class="btn btn-warning btn-xs" title="Hapus&nbsp;Filter"><i class="fa fa-filter"></i></a>
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
										            <div id="programStudi" class="panel-collapse collapse {{ Session::has('mbkm_prodi') ? 'in':'' }}">
										                <div class="panel-body">
										                    @foreach( Sia::listProdi() as $r )
										                    	<a href="javascript:void(0)" id="prodi-{{ $r->id_prodi }}" onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }} {{ $r->nm_prodi }}</a>
											                    <?php
										                    	if ( Session::has('mbkm_prodi') && in_array($r->id_prodi,Session::get('mbkm_prodi')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseTa"><span class="glyphicon glyphicon-plus"></span> Tahun Akademik</a>
										                </h4>
										            </div>
										            <div id="collpaseTa" class="panel-collapse collapse {{ Session::has('mbkm_ta') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::listSemester() as $ta )
									                    		<a href="javascript:void(0);" id="ta-{{ $ta->id_smt }}" onclick="filter({{ $ta->id_smt }},'ta')">{{ $ta->nm_smt }}</a>
										                    	<?php
										                    	if ( Session::has('mbkm_ta') && in_array($ta->id_smt,Session::get('mbkm_ta')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseJenis"><span class="glyphicon glyphicon-plus"></span> Jenis Aktivitas</a>
										                </h4>
										            </div>
										            <div id="collpaseJenis" class="panel-collapse collapse {{ Session::has('mbkm_jenis') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::jenisMbkm() as $a )
										                    	<a href="javascript:void(0);" id="jenis-{{ $a->id }}" onclick="filter({{ $a->id }},'jenis')">{{ $a->nm_aktivitas }}</a>
										                    	<?php
										                    	if ( Session::has('mbkm_jenis') && in_array($a->id, Session::get('mbkm_jenis')) ) { ?>
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

							<?php $level = Auth::user()->level; ?>

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td></td>

									<td width="300">
										<form action="{{ route('mbkm_cari') }}" method="post" id="form-cari">
											<div class="input-group">
												{{ csrf_field() }}
												<input type="text" class="form-control input-sm" name="q" value="{{ Session::get('mbkm_search') }}">
												<div class="input-group-btn">
													<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
													<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>

									@if ( in_array($level, ['akademik', 'admin', 'jurusan']) )
										<td width="110px" align="center">
											<a href="{{ route('mbkm_add') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> TAMBAH</a>
										</td>
									@endif

								</tr>
							</table>

							
							<div class="table-responsive">

								<table cellpadding="0" cellspacing="0" border="0" id="table-data" class="table table-bordered table-striped table-hover">
									<thead class="custom">
											<tr>
												<th width="20px">No.</th>
												<th>Program Studi</th>
												<th>Semester</th>
												<th>Jenis</th>
												<th>Judul</th>
												<th width="70">Aksi</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach($aktivitas as $r)
											<tr>
												<td>{{ $loop->iteration - 1 + $aktivitas->firstItem() }}</td>
												<td align="left"><a href="{{ route('mbkm_detail', ['id' => $r->id]) }}">{{ $r->jenjang }} {{ $r->nm_prodi }}</a></td>
												<td>{{ $r->id_smt }}</td>
												<td>{{ $r->nm_aktivitas }}</td>
												<td align="left">{{ $r->judul_aktivitas }}</td>
												<td>

													<span class="tooltip-area">
													@if ( in_array($level, ['akademik', 'admin', 'jurusan']) )
														<a href="{{ route('mbkm_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> 
														 <a href="{{ route('mbkm_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
													@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								@if ( $aktivitas->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $aktivitas->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $aktivitas->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $aktivitas->render() }}
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
    		url: '{{ route('mbkm_filter') }}',
    		data: { value: value, modul: modul },
    		success: function(result){
    			window.location.reload();
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
    }

</script>
@endsection