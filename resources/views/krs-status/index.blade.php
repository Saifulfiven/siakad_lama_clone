@extends('layouts.app')

@section('title','Status KRS')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Status KRS
				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="180">
									<select class="form-custom mw-2" onchange="filter('smt', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('statusKrs.smt') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
					                    @endforeach
									</select>
								</td>

								<td width="150">
									<select class="form-custom mw-2" onchange="filter('angkatan', this.value)">
										<option value="all">All Angkatan</option>
										@foreach( Sia::listAngkatan() as $a )
											<option value="{{ $a }}" {{ Session::get('statusKrs.angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
					                    @endforeach
									</select>
								</td>

								<td width="160">
									<select class="form-custom input-sm" onchange="filter('prodi', this.value)">
										<option value="all">All Prodi</option>
										@foreach( Sia::listProdi() as $pr )
											<option value="{{ $pr->id_prodi }}" {{ Session::get('statusKrs.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
										@endforeach
									</select>
								</td>
								<td>
									<select class="form-custom input-sm" onchange="filter('status', this.value)">
										<option value="all">All Status</option>
										<option value="1" {{ Session::get('statusKrs.status') == '1' ? 'selected':'' }}>Terkunci</option>
										<option value="0" {{ Session::get('statusKrs.status') == '0' ? 'selected':'' }}>Terbuka</option>
									</select>
								</td>

								<td width="280px">
									<form action="{{ route('status_krs_cari') }}" method="post" id="form-cari">
										<div class="input-group pull-right">
											{{ csrf_field() }}
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('statusKrs.cari') }}">
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
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>NIM</th>
										<th>Nama</th>
										<th>Prodi</th>
										<th>Status KRS</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($mahasiswa as $r)
										<tr>
											<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
											<td align="left">{{ $r->nim }}</td>
											<td align="left">{{ $r->nm_mhs }}</td>
											<td>{{ $r->jenjang.' '.$r->nm_prodi }}</td>
											<td>{{ $r->status_krs == 1 ? 'Terkunci' : 'Terbuka' }}</td>
											<td>
												<span class="tooltip-area">
												@if ( Sia::adminOrAkademik() && Sia::canAction(Session::get('statusKrs.smt')) )

													@if ( $r->status_krs == 0 )
														<a href="{{ route('status_krs_update', ['id' => $r->id, 'status' => 1])}}" class="btn btn-warning btn-xs" title="Kunci" onclick="return confirm('Anda ingin MENGUNCI KRS mahasiswa ini?')"><i class="fa fa-lock"></i></a>
													@else
														<a href="{{ route('status_krs_update', ['id' => $r->id, 'status' => 0, 'id_mhs_reg' => $r->id_mhs_reg])}}" class="btn btn-primary btn-xs" title="Buka" onclick="return confirm('PERHATIAN.!!! Seluruh data KRS {{ Sia::sessionPeriode('nama') }} pada mahasiswa ini akan dihapus. Anda tetap ingin membuka KRS mahasiswa ini? ')"><i class="fa fa-unlock"></i></a>
													@endif
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
        window.location.href = '{{ route('status_krs_filter') }}?modul='+modul+'&val='+value;
    }
</script>
@endsection