@extends('layouts.app')

@section('title','Jadwal Antara')

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
	@include('jadwal-antara.top-menu')
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						
						Jadwal Antara

						<div class="pull-right">
							<a href="{{ route('dosen_mengajar') }}?ta={{ Sia::sessionPeriode() }}&jenis=2" class="btn btn-theme btn-xs">Daftar Dosen Mengajar</a>
						</div>

					</header>

					<div class="panel-body">

						<div class="col-md-2" style="padding-left: 0">
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
									<thead class="custom">
										<tr><th>
												FILTER 
												<span class="tooltip-area pull-right">
													<?php if ( Session::has('jda_prodi') || Session::has('jda_search') || Session::has('jda_smt') || Session::has('jda_ket')) { ?>
													<a href="{{ route('jda_filter') }}" class="btn btn-warning btn-xs" title="Hapus&nbsp;Filter"><i class="fa fa-filter"></i></a>
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
									            <div id="programStudi" class="panel-collapse collapse {{ Session::has('jda_prodi') ? 'in':'' }}">
									                <div class="panel-body">
									                    @foreach( Sia::listProdi() as $r )
									                    	<a href="javascript:void(0)" id="prodi-{{ $r->id_prodi }}" onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }} {{ $r->nm_prodi }}</a>
										                    <?php
									                    	if ( Session::has('jda_prodi') && in_array($r->id_prodi,Session::get('jda_prodi')) ) { ?>
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
									                    <a data-toggle="collapse" data-parent="#accordion" href="#smt"><span class="glyphicon glyphicon-plus"></span> Semester</a>
									                </h4>
									            </div>
									            <div id="smt" class="panel-collapse collapse {{ Session::has('jda_smt') ? 'in':'' }}">
									                <div class="panel-body">
									                    @foreach( Sia::listSemesterAntara() as $smt )
									                    	<a href="javascript:void(0)" id="smt-{{ $smt->id_smt }}" onclick="filter({{ $smt->id_smt }},'smt')">{{ $smt->nm_smt }}</a>
										                    <?php
									                    	if ( Session::has('jda_smt') && in_array($smt->id_smt,Session::get('jda_smt')) ) { ?>
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

						<div class="col-md-10">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td>
										<a href="{{ route('jda_print') }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> CETAK</a>
									</td>

									<td width="300px">
										<form action="{{ route('jda_cari') }}" method="post" id="form-cari">
											<div class="input-group pull-right">
													{{ csrf_field() }}
													<input type="text" class="form-control input-sm" name="q" value="{{ Session::get('jda_search') }}">
													<div class="input-group-btn">
														<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
														<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
													</div>
											</div>
										</form>
									</td>
										<td width="110px">
											@if ( Sia::isGenap() )
												<a href="{{ route('jda_add') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
											@else
												<a href="javascript:;" onclick="return confirm('Jadwal antara hanya boleh ditambah pada periode genap')" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
											@endif
										</td>

								</tr>
							</table>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="table-data">
									<thead class="custom">
											<tr>
												<th width="20px">No.</th>
												<th>Waktu</th>
												<th>Matakuliah</th>
												<th>Kelas /<br>Ruang</th>
												<th>Program Studi</th>
												<th>Tahun Akademik</th>
												<th>Dosen Mengajar</th>
												<th>Terisi</th>
												<th>Aksi</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach($jadwal as $r)
											<tr>
												<td>{{ $loop->iteration - 1 + $jadwal->firstItem() }}</td>
												<td>
													{{ empty($r->hari) ? '-': Rmt::hari($r->hari) }}<br>
													{{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}
												</td>
												<td align="left"><a href="{{ route('jda_detail', ['id' => $r->id])}}">
													{{ $r->kode_mk }} <br>
													{{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</a>
												</td>
												<td>{{ $r->kode_kls }}<br>{{ $r->nm_ruangan }}</td>
												<td>{{ $r->jenjang }}<br>{{ $r->nm_prodi }}</td>
												<td>{{ $r->nm_smt }}</td>
												<td align="left"><?= $r->dosen ?></td>
												<td>{{ empty($r->terisi) ? '':$r->terisi }}</td>
												<td>
													<div class="btn-group">
														<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">Aksi</button>
														<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
														<ul class="dropdown-menu pull-right align-xs-right" role="menu">
															@if ( Auth::user()->level == 'akademik' || Auth::user()->level == 'admin')
																<li><a href="{{ route('jda_cetak_absen_mhs') }}?id={{ $r->id }}" target="_blank">Cetak Absen Mahasiswa</a> 
																<li><a href="{{ route('jda_cetak_absen_dosen') }}?id={{ $r->id }}" target="_blank">Cetak Absen Dosen</a> 
																<li><a href="{{ route('jda_cetak_daftar_nilai') }}?id={{ $r->id }}" target="_blank">Cetak Daftar Nilai</a> 
																@if ( Sia::canAction($r->id_smt) )
																	<li class="divider"></li>
																	<li><a href="{{ route('jda_edit', ['id' => $r->id])}}">Ubah</a>
																	<li><a href="{{ route('jda_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')">Hapus</a>
																@endif
															@endif
														</ul>
													</div>
												</td>
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
    		url: '{{ route('jda_filter') }}',
    		data: { value: value, modul: modul },
    		success: function(result){
    			var param = '{{ Request::has('page') }}';
    			if ( param === '' ) {
    				window.location.reload();
    			} else {
    				window.location.href='{{ route('jda') }}';
    			}
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	})
    }

</script>
@endsection