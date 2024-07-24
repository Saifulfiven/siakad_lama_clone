@extends('layouts.app')

@section('title','Nilai')

@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
		<li class="h-seperate"></li>
		<li><a>NILAI</a></li>
	</ul>
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">

		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">

					<div class="panel-body">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="120">Tahun Akademik</td>
								<td width="200">
									<select class="form-custom mw-2" onchange="filter('smt', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('nil_semester') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
					                    @endforeach
									</select>
								</td>
								<td width="50">Prodi</td>
								<td width="160">
									<select class="form-custom mw-2" onchange="filter('prodi', this.value)">
										@foreach( Sia::listProdi() as $pr )
					                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('nil_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
					                    @endforeach
									</select>
								</td>
								<td></td>

								@if ( !Sia::jurusan() )
								<td width="50">Jenis</td>
								<td>
									<select class="form-custom mw-2" onchange="filter('jenis', this.value)">
					                    <option value="1" {{ Session::get('nil_jenis') == '1' ? 'selected' : '' }}>PERKULIAHAN</option>
					                    <option value="2" {{ Session::get('nil_jenis') == '2' ? 'selected' : '' }}>ANTARA</option>
									</select>
								</td>
								@endif

								<td width="300px">
									<form action="{{ route('nil') }}" method="get" id="form-cari">
										<div class="input-group pull-right">
											<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
											<div class="input-group-btn">
												<a href="{{ route('nil') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>

							</tr>
						</table>
						
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="table-data">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Kode</th>
										<th>Nama Matakuliah</th>
										<th>SKS</th>
										<th>Kelas/Ruang</th>
										<th>Program Studi</th>
										<th>Semester</th>
										<th>Dosen</th>
										<th>Peserta</th>
										<th>Nilai</th>
										<th colspan="2">Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($jadwal as $r)

										<tr>
											<td>{{ $loop->iteration - 1 + $jadwal->firstItem() }}</td>
											<td>{{ $r->kode_mk }}</td>
											<td align="left">{{ $r->nm_mk }}</td>
											<td>{{ $r->sks_mk }}</td>
											<td>{{ $r->kode_kls }} / {{ $r->nm_ruangan }}</td>
											<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
											<td>{{ $r->smt }}</td>
											<td align="left">{!! $r->dosen !!}</td>
											<td>{{ $r->terisi }}</td>
											<td>{{ $r->nilai }}</td>
											<td>
												<span class="tooltip-area">
													@if ( Sia::adminOrAkademik() || Sia::ketua1() || Sia::jurusan() )
														<a href="{{ route('nil_detail', ['id' => $r->id]) }}" class="btn btn-success btn-xs" title="Lihat Nilai"><i class="fa fa-search-plus"></i></a>
														@if ( Sia::canAction($r->id_smt) && !Sia::jurusan() )
															<a href="{{ route('nil_edit', ['id' => $r->id]) }}?page={{ Request::get('page') }}" class="btn btn-warning btn-xs" title="Input Nilai"><i class="fa fa-pencil"></i></a>
														@endif
														@if ( Sia::jurusan() )
															<a href="{{ route('nil_edit', ['id' => $r->id]) }}?page={{ Request::get('page') }}" class="btn btn-warning btn-xs" title="Input Nilai"><i class="fa fa-pencil"></i></a>
														@endif
													@endif
													<a href="{{ route('nil_cetak', ['id' => $r->id, 'dosen' => $r->dosen]) }}" class="btn btn-primary btn-xs" target="_blank" title="Cetak"><i class="fa fa-print"></i></a>
												</span>
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
								{{ $jadwal->appends(request()->except('page'))->render() }}
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

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

</script>
@endsection