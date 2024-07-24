@extends('layouts.app')

@section('title','Materi Perkuliahan Pascasarjana')

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
						Materi Perkuliahan Pascasarjana
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td>
									</td>

									<td width="300px">
										<form action="{{ route('materi_cari') }}" method="post" id="form-cari">
											<div class="input-group pull-right">
												{{ csrf_field() }}
												<input type="text" class="form-control input-sm" name="q" value="{{ Session::get('materi_search') }}">
												<div class="input-group-btn">
													<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
													<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>
									@if ( Sia::adminOrAkademik() )
										<td width="110px">
											<a href="{{ route('materi_add') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
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
												<th>Jumlah Materi</th>
												<th>Aksi</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach($matakuliah as $r)
											<?php
												$jml_materi = DB::table('materi_kuliah_pasca')
														->where('kode_mk', $r->kode_mk)
														->count(); ?>
											<tr>
												<td>{{ $loop->iteration - 1 + $matakuliah->firstItem() }}</td>
												<td align="left"><a href="{{ route('materi_detail', ['kode_mk' => $r->kode_mk])}}">{{ $r->kode_mk }}</a></td>
												<td align="left">{{ $r->nm_mk }}</td>
												<td>{{ $r->sks_mk }}</td>
												<td>{{ $jml_materi }}</td>
												<td>
													<span class="tooltip-area">
													@if ( Sia::adminOrAkademik() )
														<a href="{{ route('materi_detail', ['kode_mk' => $r->kode_mk])}}" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i> Lihat Materi</a>
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