@extends('layouts.app')

@section('title','Hasil Kuesioner')

@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
		<li class="h-seperate"></li>
		<li><a>HASIL KUESIONER</a></li>
	</ul>
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">

		<div class="row">

			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						<table class="table" border="0">
							<tr>
								<td width="130">Tahun Akademik</td>
								<td width="180">
									<select class="form-custom mw-2" onchange="filter('smt', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('kues_semester') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
					                    @endforeach
									</select>
								</td>
								<td width="10">Prodi</td>
								<td width="180">
									<select class="form-custom mw-2" onchange="filter('prodi', this.value)">
										@foreach( Sia::listProdi() as $pr )
					                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('kues_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
					                    @endforeach
									</select>
								</td>
								<td width="10">Ket</td>
								<td width="180">
									<select class="form-custom mw-2" onchange="filter('jenis', this.value)">
										<option value="MID" {{ Session::get('kues_jenis') == 'MID' ? 'selected':'' }}>MID</option>
										<option value="FINAL" {{ Session::get('kues_jenis') == 'FINAL' ? 'selected':'' }}>FINAL</option>
									</select>
								</td>
								<td></td>
								<td>
									<a href="{{ route('kues_hasil_cetak') }}" target="_blank" class="btn btn-primary btn-sm pull-right"><i class="fa fa-print"></i> Cetak</a>
								</td>
							</tr>
						</table>
					</header>

					<div class="panel-body">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}
						
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="table-data" data-provide="data-table">
								<thead class="custom">
									<tr>
										<th>No.</th>
										<th>Matakuliah</th>
										<th>Kelas /<br>Ruang</th>
										<th>Program Studi</th>
										<th>Dosen</th>
										<th>Skor</th>
										<th>Grade</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									<?php $identifier = '' ?>
									<?php $no = 0 ?>
									@foreach($kuesioner as $r)
										<?php

											$grade = DB::table('kues_hasil as kh')
													->leftJoin('kues as k', 'kh.id_kues', 'k.id')
													->where('k.id_jdk', $r->id)
													->where('kh.penilaian','<>', 0)
													->where('k.id_dosen', $r->id_dosen)
													->sum('kh.penilaian');
											$count_hasil = DB::table('kues_hasil as kh')
													->leftJoin('kues as k', 'kh.id_kues', 'k.id')
													->where('k.id_jdk', $r->id)
													->where('kh.penilaian','<>', 0)
													->where('k.id_dosen', $r->id_dosen)
													->count();
											$dosen = Sia::namaDosen($r->gelar_depan, $r->nm_dosen, $r->gelar_belakang);
											
											if ( $identifier == $dosen.$r->kode_kls ) {
												continue;
											} else {
												$no++;
											}

											$identifier = $dosen.$r->kode_kls;

											$nilai = $grade  == 0 || $count_hasil == 0 ? 0 : $grade/$count_hasil;
										?>
										<tr>
											<td>{{ $no }}</td>
											<td align="left">
												{{ $r->kode_mk }} -
												{{ $r->nm_mk }} ({{ $r->sks_mk }} sks)
											</td>
											<td>{{ $r->kode_kls }} / {{ $r->ruangan }}</td>
											<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
											<td align="left"><?= $dosen ?></td>
											<td>{{ round($nilai,2) }}</td>
											<td>{{ Sia::kuesionerGrade($nilai) }}</td>
											<td>
												<span class="tooltip-area">
													<a href="javascript:;" 
														onclick="detail(
															'{{ $r->id }}',
															'{{ $r->nm_mk }}',
															'{{ $r->id_dosen }}',
															'{{ $dosen }}',
															'{{ $r->kode_kls }}',
															'{{ $r->ruangan }}',
															'{{ $r->jenjang }} {{ $r->nm_prodi }}'
														)" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i></a>
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>

						</div>

					</div>
				</section>
			</div>

		</div>
		<!-- //content > row-->

	</div>
	<!-- //content-->

	<div id="modal-detail" class="modal fade container" data-width="800" style="top: 20% !important" tabindex="-1">
	    <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
	        <h4 id="matakuliah-detail">Detail</h4>
	    </div>
	    <div class="modal-body">
			<div class="ajax-message"></div>

	    	<div class="col-md-12" style="padding-bottom: 40px">
	    		<table>
	                <tr>
	                    <td width="150">Nama Matakuliah</td>
	                    <td width="250">: <span id="matakuliah"></span></td>
	                </tr>
	                <tr>
	                    <td>Nama Dosen</td>
	                    <td>: <span id="dosen"></span></td>
	                </tr>
	                <tr>
	                    <td width="100">Kelas</td>
	                    <td>: <span id="kelas"></span></td>
	                </tr>
	                <tr>
	                    <td width="100">Ruangan</td>
	                    <td>: <span id="ruangan"></span></td>
	                </tr>

	            </table>

	            <div id="content-detail"></div>

            </div>

	    </div>
	</div>

@endsection

@section('registerscript')
<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>
<script>

    $(document).ready(function(){

    	$('table[data-provide="data-table"]').dataTable({
        	// "bFilter": false,
        	// "bLengthChange" : false,
        });

        $(".collapse.in").each(function(){
        	$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
        });
        
        $(".collapse").on('show.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hide.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });

        // $('#nav-mini').trigger('click');

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

    });

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

    function detail(id_jdk, matakuliah, id_dosen, dosen, kelas, ruangan, prodi)
    {
    	$('#modal-detail').modal('show');
    	$('#matakuliah').html(matakuliah);
    	$('#dosen').html(dosen);
    	$('#kelas').html(kelas);
    	$('#ruangan').html(ruangan);

	    $.ajax({
            url: '{{ route('kues_hasil_detail') }}',
            data : { 
            	preventCache : new Date(), 
            	id_jdk:id_jdk, 
            	id_kls: kelas,
            	ruangan: ruangan,
            	id_dosen: id_dosen,
            	dosen: dosen,
            	matakuliah: matakuliah,
            	prodi: prodi
            },
            beforeSend: function( xhr ) {
                $('#content-detail').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            },
            success: function(data){
                $('#content-detail').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
            }
        });
    }
</script>
@endsection