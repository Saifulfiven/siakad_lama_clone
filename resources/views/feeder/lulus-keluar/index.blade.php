@extends('layouts.app')

@section('title','Lulus/Keluar Feeder')

@section('heading')
	<style type="text/css">
		.dataTables_scrollBody {
			overflow: none;
		}
	</style>
@endsection


@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
	<li class="h-seperate"></li>
	<li><a href="{{ url('lulus-keluar') }}">Lulus/Keluar Siakad</a></li>
	<li class="h-seperate"></li>
	<li style="background: #b3f5ef"><a href="{{ route('fdr_lk') }}">Lulus/Keluar Feeder</a></li>
</ul>
@endsection

@section('content')

	<div id="content">

		<div class="row">
			
			@if ( !isset($error) )
			<div class="col-md-12">
				<section class="panel">

					<div class="panel-body">

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<div class="table-responsive">

								<table border="0" width="100%" style="margin-bottom: 10px">
										<td width="90">
											<a href="#" data-toggle="modal" data-target="#md-filter" class="btn btn-sm btn-warning">
												<i class="fa fa-filter"></i> Filter
											</a>
										</td>
										<td>
											@foreach( Session::get('flk_periode') as $val )
												<a href="{{ route('fdr_lk_resetfilter', ['reset' => 'flk_periode', 'value' => $val]) }}">
													<span class="label bg-theme-inverse"><i class="fa fa-filter"></i> {{ $val }}</span>
												</a>
											@endforeach

											@if ( Session::has('flk_prodi') && is_array(Session::get('flk_prodi')) )
												@foreach( Session::get('flk_prodi') as $val )
													<a href="{{ route('fdr_lk_resetfilter', ['reset' => 'flk_prodi', 'value' => $val]) }}">
														<span class="label bg-theme-inverse"><i class="fa fa-filter"></i> {{ Feeder::nmProdi($val) }}</span>
													</a>
												@endforeach
											@endif

											@if ( Session::has('flk_jenis_keluar') && is_array(Session::get('flk_jenis_keluar')) )

												<?php $jenis_keluar = [] ?>
												@foreach( Sia::jenisKeluar() as $jk )
													<?php $jenis_keluar[$jk->id_jns_keluar] = $jk->ket_keluar; ?>
												@endforeach

												@foreach( Session::get('flk_jenis_keluar') as $val )
													<a href="{{ route('fdr_lk_resetfilter', ['reset' => 'flk_jenis_keluar', 'value' => $val]) }}">
														<span class="label bg-theme-inverse"><i class="fa fa-filter"></i> {{ $jenis_keluar[$val] }}</span>
													</a>
												@endforeach
											@endif
										</td>
										<td width="300px">
											<form action="{{ route('fdr_lk_cari') }}" method="get" id="form-cari">
												<div class="input-group pull-right">
													{{ csrf_field() }}
													<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('flk_cari') }}">
													<div class="input-group-btn">
														<a href="{{ route('fdr_lk_cari') }}" class="btn btn-default btn-sm" id="reset-cari"><i class="fa fa-times"></i></a>
														<button class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
													</div>
												</div>
											</form>
										</td>
										<td width="90">
											<a href="{{ route('fdr_lk_ekspor') }}" id="btn-ekspor" target="_blank" class="btn btn-success btn-sm pull-right"><i class="fa fa-download"></i> EKSPOR</a>
										</td>
									</tr>
								</table>
							</div>
							<hr>

							<table class="table table-bordered table-hover" id="data-table">
				                <thead class="custom">
				                    <th>No</th>
				                    <th>NIM</th>
				                    <th>Nama</th>
				                    <th>Program Studi</th>
				                    <th>Jenis Keluar</th>
				                    <th>Tgl Keluar</th>
				                    <th>Periode keluar</th>
				                </thead>
				               
				            </table>

						</div>
					</div>
				</section>
			</div>

			@else
				<div class="col-md-8 col-md-offset-2">
					<div class="alert alert-danger">
						<h4>Terjadi kesalahan</h4>
						<br>
						<p>{{ $error }}</p>
				</div>
			@endif
				
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

	<div id="md-filter" class="modal fade md-stickTop" tabindex="-1" data-width="500">
		<div class="modal-header">
			<a href="{{ route('fdr_lk_resetfilter') }}" class="btn btn-sm btn-theme pull-right">Reset Filter</a>
			<h4 class="modal-title"><i class="fa fa-people"></i> Filter</h4>
		</div>
		<!-- //modal-header-->
		<form action="{{ route('fdr_lk_filter') }}" method="get" id="form-filter">
			<div class="modal-body">
				<div class="form-group col-md-12">
	        		<label class="control-label">Program Studi</label>
	        		<div>
	        			<select name="prodi[]" class="selectpicker form-control" multiple>
	        				<option value="">Semua</option>
	        				@foreach( Feeder::nmProdi() as $key => $pr )
                                <option value="{{ $key }}" {{ is_array(Session::get('flk_prodi')) && in_array($key, Session::get('flk_prodi')) ? 'selected':'' }}>{{ $pr }}</option>
                            @endforeach
	        			</select>
		            </div>
	        	</div>
				<div class="form-group col-md-12">
	        		<label class="control-label">Periode</label>
	        		<div>
	        			<select name="periode[]" class="selectpicker form-control" multiple>
							@foreach( Sia::listSemester() as $smt )
	        					<option value="{{ $smt->id_smt }}" {{ is_array(Session::get('flk_periode')) && in_array($smt->id_smt, Session::get('flk_periode')) ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
	        				@endforeach
						</select>
					</div>
	        	</div>
	        	<div class="form-group col-md-12">
	        		<label class="control-label">Jenis Keluar</label>
	        		<div>
	        			<select name="jenis_keluar[]" class="selectpicker form-control" multiple>
	        				<option value="">Semua</option>
	        				@foreach( Sia::jenisKeluar() as $jk )
                                <option value="{{ $jk->id_jns_keluar }}" {{ is_array(Session::get('flk_jenis_keluar')) && in_array($jk->id_jns_keluar, Session::get('flk_jenis_keluar')) ? 'selected':'' }}>{{ $jk->ket_keluar }}</option>
                            @endforeach
	        			</select>
		            </div>
	        	</div>
	        	<div class="col-md-12">
		            
	        	</div>
			</div>

			<div class="modal-footer" style="border-top: none">
				<button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Tutup</button>
				
				<button class="btn btn-sm btn-primary pull-right" id="btn-submit-filter">Tampilkan</button>
			</div>
		</form>
		<!-- //modal-body-->
	</div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/dataTables.bootstrap.js"></script>


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

        $.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 
		    alert(message);
		};

        $('#data-table').dataTable({
        	bFilter: false,
       		bLengthChange: false,
            "aoColumnDefs": [
		      { "sClass": "text-center", "aTargets": [ 0,3,4,5,6 ] },
		    ],
        	"sAjaxSource": '{{ route('fdr_lk_data') }}',
        	"fnServerData": function ( sSource, aaData, fnCallback ) {
				$.ajax( {
					"dataType": 'json',
					"type": "get",
					"url": sSource,
					"data": aaData,
					"success": fnCallback,
					"timeout": 30000,
					"error": function (data) {
						alert('Error: '+data.responseText);
						console.log(data);
					}
				} );
			},
	    })

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

        // getData();
        submit();
    });

    function getData()
    {
        $('#konten').html('<center><i class="fa fa-spinner fa-spin"></i> Loading...');
        $('#btn-ekspor').hide();

        $.ajax({
    		url: '{{ route('fdr_lk_data') }}',
    		success: function(result){
    			$('#btn-ekspor').show();
				$('#konten').html(result);
    		},
    		error: function(data,status,msg){
    			if ( typeof data.responseText !== 'undefined' ) {
    				var pesan = data.responseText;
    			} else {
    				var pesan = msg;
    			}

    			$('#konten').html('<center>'+pesan+'</center>');
    		}
    	});
    }

    function filter(value,modul)
    {
    	$('#'+modul+'-'+value).html('<i class="fa fa-spinner fa-spin"></i>');
    	$.ajax({
    		url: '{{ route('lk_filter') }}',
    		data: { value: value, modul: modul },
    		success: function(result){
    			@if ( Request::get('page') > 1 )
    				window.location.href="<?= route('lk') ?>";
    			@else
    				window.location.reload();
    			@endif
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	})
    }

    function submit()
    {
		var options = {
            beforeSend: function() 
            {
                $('body').modalmanager('loading');

            	$('#konten').html('<center><i class="fa fa-spinner fa-spin"></i> Loading...');
            	$('#btn-ekspor').hide();
                $("#btn-submit-filter").attr('disabled','');
                $("#btn-submit-filter").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Loading...");
            },
            success:function(data, status, message) {
                $('#btn-submit-filter').removeAttr('disabled');
                $('#btn-submit-filter').html('Tampilkan');
                $('#md-filter').modal('hide');

                window.location.reload();
            },
            error: function(data, status, message)
            {
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2('filter', pesan);
            }
        }; 

        $('#form-filter').ajaxForm(options);
    }
</script>
@endsection