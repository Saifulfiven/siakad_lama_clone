@extends('layouts.app')

@section('title','Berita Acara Yudisium')

@section('topMenu')
<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
	<li><a>Berita Acara Yudisium</a></li>
</ul>
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">

		<div class="row">
				
			<div class="col-md-8">
				<section class="panel">
					<div class="panel-body">
						
						<table class="table" border="0">
							<tr><td colspan="4"><a href="{{ route('lk') }}" class="btn btn-xs btn-success pull-right">Kembali ke LULUS / KELUAR</a></td></tr>
							<tr>
								<td width="130">Tahun Akademik</td>
								<td width="200">
									<select class="form-custom mw-2" onchange="filter('smt', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('bay_smt') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
					                    @endforeach
									</select>
								</td>
								<td width="10">Prodi</td>
								<td width="180">
									<select class="form-custom mw-2" onchange="filter('prodi', this.value)">
										@foreach( Sia::listProdi() as $pr )
					                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('bay_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
					                    @endforeach
									</select>
								</td>
							</tr>
						</table>

						<div class="table-responsive">

							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="table-data">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Tgl Yudisium</th>
										<th>Program Studi</th>
										<th>Semester Lulus</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($lulus as $r)
										<tr>
											<td>{{ $loop->iteration }}</td>
											<td>{{ Carbon::parse($r->tgl)->format('d-m-Y') }}</td>
											<td>{{ $r->jenjang .' '. $r->nm_prodi }}</td>
											<td>{{ $r->semester_keluar }}</td>
											<td>
												<div class="tooltip-area">
													<a href="{{ route('lk_berita_acara_yudisium_cetak') }}?prodi={{ $r->id_prodi }}&tgl={{ $r->tgl }}" class="btn btn-primary btn-xs" title="Cetak berita acara" target="_blank"><i class="fa fa-print"></i></a>
												</div>
											</td>
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

@endsection

@section('registerscript')
<script>
	function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }
</script>
@endsection