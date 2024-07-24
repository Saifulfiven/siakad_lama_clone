@extends('layouts.app')

@section('title','Laporan KRS Mahasiswa')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Laporan KRS Mahasiswa
					{{-- <div class="pull-right"> --}}
						{{-- <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-ekspor"><i class="fa fa-print"></i> EKSPOR</button> --}}
						
					{{-- </div> --}}

				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="125">
									<select class="form-control input-sm" onchange="filter('prodi', this.value)">
										<option value="all">All Prodi</option>
										@foreach( Sia::listProdi() as $pr )
											<option value="{{ $pr->id_prodi }}" {{ Session::get('mhs_krs.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
										@endforeach
									</select>
								</td>
								<td width="150" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('smt', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('mhs_krs.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
										@endforeach
									</select>
								</td>
								<td width="130" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('jalur', this.value)">
										<option value="all">All Jalur</option>
										<option value="online" {{ Session::get('mhs_krs.jalur') == 'online' ? 'selected':'' }}>Online</option>
										<option value="manual" {{ Session::get('mhs_krs.jalur') == 'manual' ? 'selected':'' }}>Manual</option>
									</select>
								</td>
								<td style="padding-left: 13px">
									@if ( count(Session::get('mhs_krs')) > 0 )
										<span class="tooltip-area">
											<a href="{{ route('mhs_krs_lap_filter') }}?remove=1" class="btn btn-xs btn-warning" title="Reset Filter"><i class="fa fa-filter"></i></a>
										</span>
									@endif
								</td>
								<td width="250px">
									<form action="{{ route('mhs_krs_lap_cari') }}" method="get" id="form-cari">
										<div class="input-group pull-right">
											{{ csrf_field() }}
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('mhs_krs.cari') }}">
											<div class="input-group-btn">
												<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								<td width="80">
									<a href="{{ route('mhs_krs_lap_excel') }}" class="btn btn-sm btn-success pull-right">EXCEL</a>
								</td>

							</tr>
						</table>
						
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Nama</th>
										<th>NIM</th>
										<th>Kelas</th>
										<th>SKS Diprogram</th>
										<th>Jalur</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($mahasiswa as $r)
										<tr>
											<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
											<td align="left">
												<a href="{{ route('mahasiswa_detail', ['id' => $r->id_mhs]) }}" target="_blank">{{ $r->nm_mhs }}</a>
											</td>
											<td>{{ $r->nim }}</td>
											<td>{{ $r->kode_kelas }}</td>
											<td>
												@if ( empty($r->sks_diprogram) && $r->jalur == 'online' )
													<small>KRS Belum di Alokasikan oleh Akademik</small>
												@else 
													{{ $r->sks_diprogram }}
												@endif
											</td>
											<td>{{ strtoupper($r->jalur) }}</td>
											<td>
												<span class="tooltip-area">
													<a href="{{ route('mhs_krs_lap_cetak', ['id' => $r->id_mhs_reg])}}?nama={{ trim($r->nm_mhs) }}&nm_periode={{ $r->nm_smt }}" target="_blank" class="btn btn-primary btn-xs" title="Lihat / Cetak"><i class="fa fa-print"></i></a> &nbsp; &nbsp; 
													@if ( Sia::role('admin|akademik') && $r->jalur == 'online' )
														<a href="{{ route('mhs_krs_lap_rollback', ['id' => $r->id])}}" onclick="return confirm('Anda ingin mahasiswa ini melakukan KRS Ulang')" class="btn btn-warning btn-xs" title="Ulang KRS"><i class="fa fa-refresh"></i></a> &nbsp; &nbsp; 
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


<div id="modal-ekspor" class="modal fade" style="top:30%" tabindex="-1" data-width="300">
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Ekspor</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<center>
				<a href="" class="btn btn-sm btn-primary"><i class="fa fa-file-text"></i> EXCEL</a>&nbsp; 
				<a href="" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> CETAK</a>
			</center>
		</div>
		<!-- //modal-body-->
</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    $(document).ready(function(){
    	$('#nav-mini').trigger('click');

        $('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });
    });

    function filter(modul, value)
    {
        window.location.href = '{{ route('mhs_krs_lap_filter') }}?modul='+modul+'&val='+value;
    }
</script>
@endsection