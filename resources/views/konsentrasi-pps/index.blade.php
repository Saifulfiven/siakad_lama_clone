@extends('layouts.app')

@section('title','Pilihan Konsentrasi Mahasiswa')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Pilihan Konsentrasi Mahasiswa &nbsp; 
					<!-- <select class="form-custom" onchange="filter('smt', this.value)">
						@foreach( Sia::listSemester() as $smt )
							<option value="{{ $smt->id_smt }}" {{ Session::get('konsentrasi.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
						@endforeach
					</select> -->

					<div class="pull-right">
						<button class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-ekspor"><i class="fa fa-print"></i> EKSPOR</button>
					</div>

				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="125" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('prodi', this.value)">
										<option value="all">All Prodi</option>
										@foreach( Sia::listProdi() as $pr )
											<option value="{{ $pr->id_prodi }}" {{ Session::get('konsentrasi.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
										@endforeach
									</select>
								</td>
								<td width="100" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('kelas', this.value)">
										<option value="all">All Kelas</option>
										{{-- @foreach( Sia::kelasPemilihanKonsentrasi(Session::get('konsentrasi.smt')) as $kls ) --}}
										{{-- @foreach( Sia::kelasPemilihanKonsentrasi(Sia::sessionPeriode()) as $kls )
											<option value="{{ $kls->kelas }}" {{ Session::get('konsentrasi.kelas') == $kls->kelas ? 'selected':'' }}>{{ $kls->kelas }}</option>
										@endforeach --}}
										@foreach( Sia::listKelasKonsentrasi() as $key => $val )
                                            @foreach( range('A', $val) as $bag )
                                                <option value="{{ $key }}-{{ $bag }}" {{ Session::get('konsentrasi.kelas') == "$key-$bag" ? 'selected':'' }}>{{ $key }}-{{ $bag }}</option>
                                            @endforeach
                                            <!-- <option value="XII-G1">XII-G1</option>
                                            <option value="XII-H1">XII-H1</option>
                                            <option value="XII-H2">XII-H2</option> -->
                                        @endforeach
									</select>
								</td>
								{{-- {{ dd(Sia::sessionPeriode()) }} --}}
								<td width="250" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('konsentrasi', this.value)">
										<option value="all">Semua Konsentrasi</option>
										@foreach( Sia::listKonsentrasi(61101) as $kon )
											<option value="{{ $kon->id_konsentrasi }}" {{ Session::get('konsentrasi.konsentrasi') == $kon->id_konsentrasi ? 'selected':'' }}>{{ $kon->nm_konsentrasi }}</option>
										@endforeach
									</select>
								</td>
								<td style="padding-left: 13px">
									@if ( count(Session::get('konsentrasi')) > 0 )
										<span class="tooltip-area">
											<a href="{{ route('konsentrasi_filter') }}?remove=1" class="btn btn-xs btn-warning" title="Reset Filter"><i class="fa fa-filter"></i></a>
										</span>
									@endif
								</td>
								<td width="250px">
									<form action="{{ route('konsentrasi_cari') }}" method="post" id="form-cari">
										<div class="input-group pull-right">
											{{ csrf_field() }}
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('konsentrasi.cari') }}">
											<div class="input-group-btn">
												<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								@if ( Sia::role('admin|akademik|cs') )
									<td width="110px">
										<a href="{{ route('konsentrasi_add') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
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
										<th>NIM</th>
										<th>Kelas</th>
										<th>Konsentrasi</th>
										<th>Semester</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($mhs as $r)
										<tr>
											<td>{{ $loop->iteration - 1 + $mhs->firstItem() }}</td>
											<td align="left">{{ $r->nm_mhs }}</td>
											<td align="left">{{ $r->nim }}</td>
											<td>{{ $r->kelas }}</td>
											<td align="left">{{ $r->nm_konsentrasi }}</td>
											<td>{{ $r->id_smt }}</td>
											<td>
												<span class="tooltip-area">
												@if ( Sia::role('admin|akademik|cs') )
													<a href="{{ route('konsentrasi_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
													<a href="{{ route('konsentrasi_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
												@endif
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							@if ( $mhs->total() == 0 )
								&nbsp; Tidak ada data
							@endif

							@if ( $mhs->total() > 0 )
								<div class="pull-left">
									Jumlah data : {{ $mhs->total() }}
								</div>
							@endif

							<div class="pull-right"> 
								{{ $mhs->render() }}
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
			<a href="{{ route('konsentrasi_excel') }}" class="btn btn-sm btn-primary"><i class="fa fa-file-text"></i> EXCEL</a>&nbsp; 
			<a href="{{ route('konsentrasi_print') }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> CETAK</a>
		</center>
	</div>
	<!-- //modal-body-->
</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
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
        window.location.href = '{{ route('konsentrasi_filter') }}?modul='+modul+'&val='+value;
    }
</script>
@endsection