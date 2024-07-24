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
		<li><a>Aktivitas Kuliah / Capaian perkuliahan</a></li>
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
												<?php if ( Session::has('akm_prodi') || Session::has('akm_angkatan') || Session::has('akm_status')  || Session::has('akm_ta') || Session::has('akm_search') ) { ?>
												<a href="{{ route('akm_filter') }}" class="btn btn-warning btn-xs" title="Hapus&nbsp;Filter"><i class="fa fa-filter"></i></a>
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
										            <div id="programStudi" class="panel-collapse collapse {{ Session::has('akm_prodi') ? 'in':'' }}">
										                <div class="panel-body">
										                    @foreach( Sia::listProdi() as $r )
										                    	<a href="javascript:void(0)" id="prodi-{{ $r->id_prodi }}" onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }} {{ $r->nm_prodi }}</a>
											                    <?php
										                    	if ( Session::has('akm_prodi') && in_array($r->id_prodi,Session::get('akm_prodi')) ) { ?>
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
										            <div id="collpaseTa" class="panel-collapse collapse {{ Session::has('akm_ta') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::listSemester() as $ta )
									                    		<a href="javascript:void(0);" id="ta-{{ $ta->id_smt }}" onclick="filter({{ $ta->id_smt }},'ta')">{{ $ta->nm_smt }}</a>
										                    	<?php
										                    	if ( Session::has('akm_ta') && in_array($ta->id_smt,Session::get('akm_ta')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseAngkatan"><span class="glyphicon glyphicon-plus"></span> Angkatan</a>
										                </h4>
										            </div>
										            <div id="collpaseAngkatan" class="panel-collapse collapse {{ Session::has('akm_angkatan') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::listAngkatan() as $a )
									                    	<a href="javascript:void(0);" id="angkatan-{{ $a }}" onclick="filter({{ $a }},'angkatan')">{{ $a }}</a>
									                    	<?php
									                    	if ( Session::has('akm_angkatan') && in_array($a,Session::get('akm_angkatan')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseJenisDaftar"><span class="glyphicon glyphicon-plus"></span> Jenis Pendaftaran</a>
										                </h4>
										            </div>
										            <div id="collpaseJenisDaftar" class="panel-collapse collapse {{ Session::has('akm_jns_daftar') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::jenisDaftar() as $jd )
									                    	<a href="javascript:void(0);" id="jns_daftar-{{ $jd->id_jns_pendaftaran }}" onclick="filter({{ $jd->id_jns_pendaftaran }},'jns_daftar')">{{ $jd->nm_jns_pendaftaran }}</a>
									                    	<?php
									                    	if ( Session::has('akm_jns_daftar') && in_array($jd->id_jns_pendaftaran,Session::get('akm_jns_daftar')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseStatusMahasiswa"><span class="glyphicon glyphicon-plus"></span> Status Mahasiswa</a>
										                </h4>
										            </div>
										            <div id="collapseStatusMahasiswa" class="panel-collapse collapse {{ Session::has('akm_status') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::statusAkmMhs() as $res )
									                    	<a href="javascript:void(0)" id="status-{{ $res->id_stat_mhs }}" onclick="filter('{{ $res->id_stat_mhs }}','status')">{{ $res->nm_stat_mhs }}</a>
									                    	<?php
										                    	if ( Session::has('akm_status') && in_array($res->id_stat_mhs,Session::get('akm_status')) ) { ?>
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
									<td></td>
									<td width="115px">
										<a href="{{ route('akm_cetak') }}" class="btn btn-sm btn-default pull-right" target="_blank"><i class="fa fa-print"></i> CETAK</a>
									</td>

									<td width="125px">
										<a href="{{ route('akm_excel_feeder') }}" class="btn btn-sm btn-default pull-right" target="_blank"><i class="fa fa-download"></i> EXCEL FEEDER</a>
									</td>
									
									<td width="75px">
										<a href="{{ route('akm_hitung_sp') }}" class="btn btn-sm btn-info pull-right">AKM SP</a>
									</td>

									{{-- @if ( Sia::akademik() ) --}}
										<td width="110px" align="center">
											<a href="{{ route('akm_add') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> TAMBAH</a>
										</td>
										<td width="115px">
											<a href="{{ route('akm_hitung') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH MASSAL</a>
										</td>
									{{-- @endif --}}

								</tr>
							</table>

							<hr>

							<div class="col-md-8"></div>
							<div class="col-md-4" style="padding-right: 0">
								<div class="pull-right">
									<form action="{{ route('akm_cari') }}" method="post" id="form-cari">
										<div class="input-group pull-right">
												{{ csrf_field() }}
												<input type="text" class="form-control input-sm" name="q" value="{{ Session::get('akm_search') }}">
												<div class="input-group-btn">
														<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
														<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
										</div>
									</form>
								</div>
							</div>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" id="table-data" class="table table-bordered table-striped table-hover">
									<thead class="custom">
											<tr>
												<th width="20px">No.</th>
												<th>NIM</th>
												<th>Nama</th>
												<th>Prodi</th>
												<th>Semester</th>
												<th>Status</th>
												<th>IPS</th>
												<th>IPK</th>
												<th>SKS Smstr</th>
												<th>SKS Total</th>
												<th width="70">Aksi</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach($mahasiswa as $r)
											<tr>
												<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
												<td>{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
												<td align="left">{{ $r->nm_smt }}</td>
												<td>{{ $r->nm_stat_mhs }}</td>
												<td>{{ $r->ips }}</td>
												<td>{{ $r->ipk }}</td>
												<td>{{ $r->sks_smt }}</td>
												<td>{{ $r->sks_total }}</td>
												<td>
													<span class="tooltip-area">
													@if ( Sia::adminOrAkademik() && Sia::canAction($r->id_smt) )
														<a href="{{ route('akm_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> 
														 <a href="{{ route('akm_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
													@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								@if ( $mahasiswa->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $mahasiswa->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $mahasiswa->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $mahasiswa->render() }}
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
    		url: '{{ route('akm_filter') }}',
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