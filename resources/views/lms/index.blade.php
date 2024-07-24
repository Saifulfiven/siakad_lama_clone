@extends('layouts.app')

@section('title','E-Learning')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						E-Learning
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							<?php
								$fil_prodi = Session::has('lms.prodi') ? '<p>Prodi: '.implode(',', Session::get('lms.prodi')).'</p>':'';
								$fil_smt = '<p>Semester: '.implode(',', Session::get('lms.smt')).'</p>';
							?>
							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="90">
										<button class="btn btn-default btn-sm btn-filter" title="<?= $fil_prodi.$fil_smt ?>" data-toggle="modal" data-target="#modal-filter"><i class="fa fa-filter" <?= Session::has('lms') ? 'style="color: red"':'' ?>></i> Filter</button>
									</td>
									<td>
										<button class="btn bg-primary btn-sm"
											data-toggle="modal"
											data-target="#md-pengunaan">
											<i class="fa fa-bar-chart-o"></i> Penggunaan
										</button>
									</td>

									<td width="300px">
										<form action="{{ route('lms_cari') }}" method="post" id="form-cari">
											<div class="input-group pull-right">
													{{ csrf_field() }}
													<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('lms.cari') }}">
													<div class="input-group-btn">
														<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
														<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
													</div>
											</div>
										</form>
									</td>

								</tr>
							</table>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="table-data">
									<?php $fakultas = Sia::getFakultasUser() ?>
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>Matakuliah</th>
											<th>Smt</th>
											<th>Program Studi</th>
											<th>TA</th>
											<th>Dosen Mengajar</th>
											<th>Peserta</th>
											<!-- <th width="75">Aksi</th> -->
										</tr>
									</thead>
									<tbody align="center">
										@foreach($jadwal as $r)
											<tr>
												<td>{{ $loop->iteration - 1 + $jadwal->firstItem() }}</td>
												<td align="left">
													{{ $r->kode_mk }} -
													{{ $r->nm_mk }} ({{ $r->sks_mk }} sks)
												</td>
												<td>{{ $r->smt }}</td>
												<td>{{ $r->jenjang }} - {{ $r->nm_prodi }}</td>
												<td>{{ $r->id_smt }}</td>
												<td align="left">
													<?php $dosens = explode('|', $r->dosen) ?>
													<?php $id_dosens = explode('|', $r->id_dosen) ?>
													@foreach( $dosens as $key => $val )
														<a href="{{ route('lms_detail', [$id_dosens[$key], $r->id]) }}" class="btn btn-default btn-xs">{{ $val }}</a><br>
													@endforeach
												</td>
												<td>{{ empty($r->terisi) ? '':$r->terisi }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								@if ( $jadwal->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $jadwal->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $jadwal->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $jadwal->render() }}
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

<div id="modal-filter" class="modal fade" data-width="600" style="top: 25%" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Filter</h4>
    </div>
    <div class="modal-body">
        <form action="{{ route('lms_filter') }}" id="form-filter">
        	<div class="form-group">
        		<label class="control-label">Program Studi</label>
        		<div>
        			<select name="prodi[]" class="selectpicker form-control" multiple>
	        			@foreach( Sia::listProdi() as $r )
	                    	<option value="{{ $r->id_prodi }}" {{ Session::has('lms.prodi') && in_array($r->id_prodi,Session::get('lms.prodi')) ? 'selected':'' }}>{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
	                    @endforeach
	                </select>
	            </div>
        	</div>
        	<div class="form-group">
		        <label class="control-label">Periode</label>
		        <div>
			        <select name="smt[]" class="selectpicker form-control" multiple>
			            @foreach( Sia::listSemester() as $smt )
			                <option value="{{ $smt->id_smt }}" {{ Session::has('lms.smt') && in_array($smt->id_smt, Session::get('lms.smt')) ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
			            @endforeach
			        </select>
			    </div>
		    </div>
		    <div class="form-group">
		    	<button class="btn btn-primary btn-block btn-set-filter">Terapkan</button>
		    </div>
        </form>
    </div>
</div>

<div id="md-pengunaan" class="modal fade md-stickTop" tabindex="-1" data-width="900">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
		<h4 class="modal-title"><i class="fa fa-bar-chart-o"></i> Penggunaan E-Learning</h4>
	</div>
	<!-- //modal-header-->
	<div class="modal-body" style="max-height: 600px; overflow-y: scroll;">
		<div id="konten-penggunaan"><center><i class="fa fa-spin fa-spinner"></i></center></div>
	</div>
	<!-- //modal-body-->
</div>
@endsection

@section('registerscript')
<script>

    $(document).ready(function(){

    	$('#md-pengunaan').on('show.bs.modal', function(){

	    	$.ajax({
	    		url: '{{ route('lms_penggunaan') }}',
	    		success: function(result){
	    			setTimeout(() => {
						$('#konten-penggunaan').html(result);
	    			}, 500);
	    		},
	    		error: function(data,status,msg){
	    			alert(msg);
	    		}
	    	});
	    });

        /* Add minus icon for collapse element which is open by default */
        $(".collapse.in").each(function(){
        	$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
        });
        
        /* Toggle plus minus icon on show hide of collapse element */
        $(".collapse").on('show.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hide.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });

        $('#nav-mini').trigger('click');

        $('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

        $('.btn-filter').tooltipster({
		    animation: 'fade',
		    delay: 200,
		    theme: 'tooltipster-punk',
		    contentAsHTML: true
		});

		$('#form-filter').on('submit', function(){
			$('.btn-set-filter').html('<i class="fa fa-spinner fa-spin"></i> Memproses..');
			$('.btn-set-filter').attr('disabled','');
		});
    });

    function filter(value,modul)
    {
    	$('#'+modul+'-'+value).html('<i class="fa fa-spinner fa-spin"></i>');

    	$.ajax({
    		url: '{{ route('jdk_filter') }}',
    		data: { value: value, modul: modul },
    		success: function(result){
    			var param = '{{ Request::has('page') }}';
    			if ( param === '' ) {
    				window.location.reload();
    			} else {
    				window.location.href='{{ route('jdk') }}';
    			}
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
    }

    function getKelasJdk(waktu_kuliah)
    {
    	$('#konten-jdk').html('<center><i class="fa fa-spinner fa-spin fa-2x"></i></center>');

    	$.ajax({
    		url: '{{ route('jdk_ajax') }}',
    		data: { tipe: 'jdk-cetak-s2', waktu_kuliah: waktu_kuliah },
    		success: function(result){
    			$('#konten-jdk').html(result);
    		},
    		error: function(data,status,msg){
    			alert('Gagal mengambil data jadwal, silahkan muat ulang halaman. Message: '+msg);
    		}
    	});
    }

    @if ( in_array(61101, Sia::getProdiUser()) )
    	getKelasJdk('PAGI');
    @endif

</script>
@endsection