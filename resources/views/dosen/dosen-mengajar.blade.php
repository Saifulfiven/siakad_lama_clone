@extends('layouts.app')

@section('title','Dosen Mengajar')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Dosen Mengajar
					<div class="pull-right">
						<a href="{{ route('dosen_absensi') }}" target="_blank"
							class="btn btn-primary btn-xs"><i class="fa fa-print"></i>
							Absensi Dosen
						</a>
						<a href="{{ route('dosen_mengajar_cetak') }}?ta={{ Request::get('ta') }}" target="_blank"
							class="btn btn-primary btn-xs"><i class="fa fa-print"></i>
							Cetak Dosen Mengajar
						</a>
						@if ( Request::get('jenis') == 1 )
						<a href="{{ route('jdk') }}"
							class="btn btn-success btn-xs">
							Kembali ke Jadwal Kuliah
						</a>
						@else
						<a href="{{ route('jda') }}"
							class="btn btn-success btn-xs">
							Kembali ke Jadwal Antara
						</a>
						@endif
					</div>
				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="120">Tahun Akademik</td>
								<td width="200">
									<select class="form-custom mw-2" onchange="filter('ta', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Request::get('ta') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
					                    @endforeach
									</select>
								</td>
								<td width="50">Jenis</td>
								<td>
									<select class="form-custom mw-2" onchange="filter('jenis', this.value)">
					                    <option value="1" {{ Request::get('jenis') == '1' ? 'selected' : '' }}>PERKULIAHAN</option>
					                    <option value="2" {{ Request::get('jenis') == '2' ? 'selected' : '' }}>ANTARA</option>
									</select>
								</td>

								<td width="300px">
									<form action="{{ route('dosen_mengajar') }}" method="get">
										<div class="input-group pull-right">
											<input type="hidden" name="ta" value="{{ Request::get('ta') }}">
											<input type="hidden" name="jenis" value="{{ Request::get('jenis') }}">
											<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
											<div class="input-group-btn">
												<a href="{{ route('dosen_mengajar') }}?ta={{ Request::get('ta') }}&jenis={{ Request::get('jenis') }}" class="btn btn-default btn-sm" type="button"><i class="fa fa-times"></i></a>
												<button class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								@if ( $prodi != 61101 )
								<td>
									<a href="{{ route('dosen_mengajar_sk') }}?ta={{ Request::get('ta') }}&jenis={{ Request::get('jenis') }}&all=1" class="btn btn-primary btn-sm pull-right" title="Cetak" target="_blank"><i class="fa fa-print"></i> CETAK SEMUA SK</a>
								</td>
								@endif

							</tr>
						</table>
						
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Nama</th>
										<th>HP</th>
										<th>Alamat</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($dosen as $r)
										<tr>
											<td>{{ $loop->iteration - 1 + $dosen->firstItem() }}</td>
											<td align="left">{{ Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang) }}</td>
											<td>{{ $r->hp }}</td>
											<td align="left">{{ $r->alamat }}</td>
											<td>
												<span class="tooltip-area">

													@if ( $prodi == 61101 )
													
														<a href="javascript::void(0);" class="btn btn-primary btn-xs" title="Cetak" onclick="openMk('{{ $r->id }}', '{{ Request::get('ta') }}', '{{ Request::get('jenis') }}')"><i class="fa fa-print"></i> CETAK SK</a>
													
													@else
													
														<a href="{{ route('dosen_mengajar_sk', ['id' => $r->id]) }}?ta={{ Request::get('ta') }}&prodi={{ $prodi }}&jenis={{ Request::get('jenis') }}&nomor=" class="btn btn-primary btn-xs" title="Cetak" target="_blank"><i class="fa fa-print"></i> CETAK SK</a>
													
													@endif
													
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							@if ( $dosen->total() == 0 )
								&nbsp; Tidak ada data
							@endif

							@if ( $dosen->total() > 0 )
								<div class="pull-left">
									Jumlah data : {{ $dosen->total() }}
								</div>
							@endif

							<div class="pull-right"> 
								{{ $dosen->appends(Request::query())->links() }}
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

	<div id="modal-mk" class="modal fade" tabindex="-1" style="top:30%">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
	        <h4 class="modal-title">Pilih matakuliah</h4>
	    </div>
	    <!-- //modal-header-->
	    <div class="modal-body">
	        <form action="{{ route('dosen_mengajar_sk2') }}" target="_blank" method="post">
	        	{{ csrf_field() }}
				<div id="content-mk"></div>
			</form>
	    </div>
	    <!-- //modal-body-->
	</div>

@endsection

@section('registerscript')
<script>

    function filter(modul, value)
    {
    	if ( modul == 'jenis' ) {
    		window.location.href='?'+modul+'='+value+'&ta={{ Request::get('ta') }}';
    	} else {
    		window.location.href='?'+modul+'='+value+'&jenis={{ Request::get('jenis') }}';
    	}
    }

    function openMk(id_dosen, ta, jenis)
    {
    	$('#modal-mk').modal('show');
    	$('#content-mk').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
    	$.ajax({
    		url : '{{ route('dosen_mengajar_sk_mk') }}',
    		data : { id_dosen: id_dosen, ta: ta, jenis: jenis},
    		success: function(res){
    			$('#content-mk').html(res);
    		},
    		error: function(err){
    			alert('Gagal mengambil matakuliah, muat ulang halaman dan ulangi lagi');
    		}
    	})
    }
</script>
@endsection