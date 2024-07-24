@extends('layouts.app')

@section('title','Mahasiswa Lulus / Keluar')

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
		<li style="background: #b3f5ef"><a href="{{ url('lulus-keluar') }}">Lulus/Keluar Siakad</a></li>
		<li class="h-seperate"></li>
		<li><a href="{{ route('fdr_lk') }}">Lulus/Keluar Feeder</a></li>
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
													<?php if ( Session::has('lk_prodi') || Session::has('lk_angkatan') || Session::has('lk_status')  || Session::has('lk_ta') || Session::has('lk_search') || Session::has('lk_jns_daftar') || Session::has('lk_jenkel') || Session::has('lk_ta_masuk') || Session::has('lk_jns_daftar') ) { ?>
													<a href="{{ route('lk_filter') }}" class="btn btn-warning btn-xs" title="Hapus&nbsp;Filter"><i class="fa fa-filter"></i></a>
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
										            <div id="programStudi" class="panel-collapse collapse {{ Session::has('lk_prodi') ? 'in':'' }}">
										                <div class="panel-body">
										                    @foreach( Sia::listProdi() as $r )
										                    	<a href="javascript:void(0)" id="prodi-{{ $r->id_prodi }}" onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }} {{ $r->nm_prodi }}</a>
											                    <?php
										                    	if ( Session::has('lk_prodi') && in_array($r->id_prodi,Session::get('lk_prodi')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseTa"><span class="glyphicon glyphicon-plus"></span> Semester Lulus / Keluar</a>
										                </h4>
										            </div>
										            <div id="collpaseTa" class="panel-collapse collapse {{ Session::has('lk_ta') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::listSemester() as $ta )
									                    	<a href="javascript:void(0);" id="ta-{{ $ta->id_smt }}" onclick="filter({{ $ta->id_smt }},'ta')">{{ $ta->nm_smt }}</a>
									                    	<?php
									                    	if ( Session::has('lk_ta') && in_array($ta->id_smt,Session::get('lk_ta')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collpasePin"><span class="glyphicon glyphicon-plus"></span> PIN</a>
										                </h4>
										            </div>
										            <div id="collpasePin" class="panel-collapse collapse {{ Session::has('lk_pin') ? 'in':'' }}">
										                <div class="panel-body">
									                    	<a href="javascript:void(0);" id="pin-pin" onclick="filter('pin','pin')">PIN</a>
									                    	<?php
									                    	if ( Session::has('lk_pin') && in_array('pin',Session::get('lk_pin')) ) { ?>
									                    		<i class="filter fa fa-filter"></i>
									                    	<?php } ?>
									                    	<br>
									                    	<a href="javascript:void(0);" id="pin-nonpin" onclick="filter('nonpin','pin')">NON-PIN</a>
									                    	<?php
									                    	if ( Session::has('lk_pin') && in_array('nonpin',Session::get('lk_pin')) ) { ?>
									                    		<i class="filter fa fa-filter"></i>
									                    	<?php } ?>
									                    	<br>
										                </div>
										            </div>
										        </div>

										        <div class="panel panel-info">
										            <div class="panel-heading">
										                <h4 class="panel-title">
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseJenisKeluar"><span class="glyphicon glyphicon-plus"></span> Jenis Keluar</a>
										                </h4>
										            </div>
										            <div id="collapseJenisKeluar" class="panel-collapse collapse {{ Session::has('lk_status') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::jenisKeluar() as $res )
									                    	<a href="javascript:void(0)" id="status-{{ $res->id_jns_keluar }}" onclick="filter('{{ $res->id_jns_keluar }}','status')">{{ $res->ket_keluar }}</a>
									                    	<?php
										                    	if ( Session::has('lk_status') && in_array($res->id_jns_keluar,Session::get('lk_status')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collpaseTaMasuk"><span class="glyphicon glyphicon-plus"></span> Semester Masuk</a>
										                </h4>
										            </div>
										            <div id="collpaseTaMasuk" class="panel-collapse collapse {{ Session::has('lk_ta_masuk') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::listSemester() as $ta )
									                    	<a href="javascript:void(0);" id="ta_masuk-{{ $ta->id_smt }}" onclick="filter({{ $ta->id_smt }},'ta_masuk')">{{ $ta->nm_smt }}</a>
									                    	<?php
									                    	if ( Session::has('lk_ta_masuk') && in_array($ta->id_smt,Session::get('lk_ta_masuk')) ) { ?>
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
										            <div id="collpaseAngkatan" class="panel-collapse collapse {{ Session::has('lk_angkatan') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::listAngkatan() as $a )
									                    	<a href="javascript:void(0);" id="angkatan-{{ $a }}" onclick="filter({{ $a }},'angkatan')">{{ $a }}</a>
									                    	<?php
									                    	if ( Session::has('lk_angkatan') && in_array($a,Session::get('lk_angkatan')) ) { ?>
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
										            <div id="collpaseJenisDaftar" class="panel-collapse collapse {{ Session::has('lk_jns_daftar') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::jenisDaftar() as $jd )
									                    	<a href="javascript:void(0);" id="jns_daftar-{{ $jd->id_jns_pendaftaran }}" onclick="filter({{ $jd->id_jns_pendaftaran }},'jns_daftar')">{{ $jd->nm_jns_pendaftaran }}</a>
									                    	<?php
									                    	if ( Session::has('lk_jns_daftar') && in_array($jd->id_jns_pendaftaran,Session::get('lk_jns_daftar')) ) { ?>
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
										                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseJenkel"><span class="glyphicon glyphicon-plus"></span> Jenis Kelamin</a>
										                </h4>
										            </div>
										            <div id="collapseJenkel" class="panel-collapse collapse {{ Session::has('lk_jenkel') ? 'in':'' }}">
										                <div class="panel-body">
										                	@foreach( Sia::jenisKelamin() as $a )
									                    	<a href="javascript:void(0)" id="jenkel-{{ $a['id'] }}" onclick="filter('{{ $a['id'] }}','jenkel')">{{ $a['nama'] }}</a>
									                    	<?php
										                    	if ( Session::has('lk_jenkel') && in_array($a['id'],Session::get('lk_jenkel')) ) { ?>
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

							<div class="table-responsive">

								<table border="0" width="100%" style="margin-bottom: 10px">
									<tr>
										<td width="80">
											<a href="{{ route('lk_berita_acara_yudisium') }}" class="btn btn-theme btn-sm">Berita Acara Yudisium</a>
										</td>
										<td width="90">
											<a href="{{ route('lk_print') }}" target="_blank" class="btn btn-primary btn-sm pull-right"><i class="fa fa-print"></i> CETAK</a>
										</td>
										<td width="90">
											<a href="{{ route('lk_ekspor') }}" target="_blank" class="btn btn-success btn-sm pull-right"><i class="fa fa-download"></i> EKSPOR</a>
										</td>
										<td width="145">
											<a href="javascript:void(0)" data-toggle="modal" data-target="#md-buku-wisuda" class="btn btn-success btn-sm pull-right"><i class="fa fa-search-plus"></i> Data Buku Wisuda</a>
										</td>
										<td></td>
										<td width="300px">
											<form action="{{ route('lk_cari') }}" method="post" id="form-cari">
												<div class="input-group pull-right">
														{{ csrf_field() }}
														<input type="text" class="form-control input-sm" name="q" value="{{ Session::get('lk_search') }}">
														<div class="input-group-btn">
															<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
															<button class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
														</div>
												</div>
											</form>
										</td>
										@if (Sia::akademik())
											<td width="110px" align="center">
												<a href="{{ route('lk_add') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> TAMBAH</a>
											</td>
                    @endif
									</tr>
								</table>
							</div>

							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" id="table-data" class="table table-bordered table-striped table-hover">
									<thead class="custom">
											<tr>
												<th width="20px">No.</th>
												<th>NIM</th>
												<th width="200px">Nama</th>
												<th>Prodi</th>
												<th>Semester</th>
												<th>Jenis Keluar</th>
												<th>Tgl Keluar</th>
												<th>PIN</th>
												<th>Aksi</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach($mahasiswa as $r)
											<tr>
												<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
												<td>{{ $r->nim }}</td>
												<td align="left">{{ $r->gelar_depan }} {{ trim($r->nm_mhs) }} {{ !empty($r->gelar_belakang) ? ', '.$r->gelar_belakang : '' }}</td>
												<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
												<td>{{ $r->semester_keluar }}</td>
												<td>{{ $r->ket_keluar }}</td>
												<td>{{ Carbon::parse($r->tgl_keluar)->format('d-m-Y') }}</td>
												<td>{{ $r->pin }}</td>
												<td>
													<span class="tooltip-area">
														<a href="{{ route('lk_detail', ['id' => $r->id_mhs_reg])}}" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i></a> 
														<a href="{{ route('lk_sk_lulus', ['id' => $r->id_mhs_reg])}}?nomor=" class="btn btn-default btn-xs" target="_blank" title="Surat Ket Lulus"><i class="fa fa-print"></i></a> 
														@if ( Sia::akademik() )
															<a href="{{ route('lk_edit', ['id' => $r->id_mhs_reg])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a>

															 <a href="{{ route('lk_delete', ['id' => $r->id_mhs_reg])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
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
				
				<a href="#" data-toggle="modal" data-target="#md-buku-wisuda">Show</a>
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

<div id="md-buku-wisuda" class="modal fade md-stickTop" tabindex="-1" data-width="500">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
		<h4 class="modal-title"><i class="fa fa-people"></i> Data Buku Wisuda</h4>
	</div>
	<!-- //modal-header-->
	<div class="modal-body">
		<p>Tentukan rentang tanggal lulus wisudawan yang akan diambil.</p>
		<p><b>Cat:</b> Filter yang diterapkan di halaman ini (Lulus Leluar) juga akan diterapkan.</p>
		<form action="{{ route('lk_html') }}" method="get" target="_blank">
			<div class="form-group col-md-6">
        		<label class="control-label">Ambil dari Tanggal</label>
        		<div>
        			<input type="date" name="tgl_mulai" class="form-control mw-2" required>
	            </div>
        	</div>
        	<div class="form-group col-md-6">
        		<label class="control-label">Sampai Tanggal</label>
        		<div>
        			<input type="date" name="tgl_akhir"  class="form-control mw-2" value="{{ Carbon::today()->format('Y-m-d') }}">
	            </div>
        	</div>
        	<div class="col-md-12">
	            <button class="btn btn-sm btn-primary pull-right">Tampilkan</button>
        	</div>
		</form>
		<br>
		&nbsp;
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

</script>
@endsection