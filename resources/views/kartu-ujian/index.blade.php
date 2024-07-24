@extends('layouts.app')

@section('title','Kartu Ujian');

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Kartu Ujian
					</header>

					<div class="panel-body">

						<div class="col-md-2" style="padding-left: 0">

						</div>

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="120"><b>Tahun Akademik :</b> </td>
									<td width="180">
										<select class="form-custom" onchange="filter('smt', this.value)" id='filter-smt'>
											@foreach( $semester as $smt )
												<option value="{{ $smt->id_smt }}" {{ Session::get('ku_smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
											@endforeach
										</select>
									</td>
									<td width="150">
										<select class="form-custom mw-2" name="prodi" onchange="filter('prodi', this.value)" id="list-prodi">
											<option value="all">Semua Prodi</option>
											@foreach( Sia::listProdi() as $pr )
						                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('ku_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
						                    @endforeach
										</select>
									</td>

									<td width="85">
										<select class="form-custom mw-2" onchange="filter('jenis', this.value)">
											<option value="UTS" {{ Session::get('ku_jenis') == 'UTS' ? 'selected':'' }}>UTS</option>
											<option value="UAS" {{ Session::get('ku_jenis') == 'UAS' ? 'selected':'' }}>UAS</option>
										</select>
									</td>

									<td width="">
										<select class="form-custom mw-2" onchange="filter('status', this.value)">
											<option value="AKTIF" {{ Session::get('ku_status') == 'AKTIF' ? 'selected':'' }}>Aktif</option>
											<option value="NON-AKTIF" {{ Session::get('ku_status') == 'NON-AKTIF' ? 'selected':'' }}>Non Aktif</option>
										</select>
									</td>

								</tr>
							</table>
							
							<hr>

							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" data-provide="data-table">
									<thead class="custom">
										<tr>
											<th>No.</th>
											<th width="120">NIM</th>
											<th>Nama</th>
											<th>Prodi</th>
											<th>Jenis Ujian</th>
											<th>Status</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody align="center">
										<?php $no = 1 ?>
										@foreach($mahasiswa as $r)
											<tr>
												<td>{{ $no++ }}</td>
												<td width="100">{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td>{{ $r->jenjang .' '. $r->nm_prodi }}</td>
												<td>{{ Session::get('ku_jenis') }}</td>
												<td><i <?= Session::get('ku_status') == 'AKTIF' ? 'class="fa fa-check" style="color: green"':'class="fa fa-ban" style="color: red"' ?></i></td>
												<td><span id="{{ $r->id_mhs_reg }}"></span>
													<select class="form-custom" onchange="update(this.value)">
														<option value="{{ $r->id_mhs_reg }}|N" {{ Session::get('ku_status') == 'NON-AKTIF' ? 'selected':'' }}>NON-AKTIF</option>
														<option value="{{ $r->id_mhs_reg }}|A" {{ Session::get('ku_status') == 'AKTIF' ? 'selected':'' }}>AKTIF</option>
													</select>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

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
<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>

<script>

    $(document).ready(function(){

        $('table[data-provide="data-table"]').dataTable({
        	// "bFilter": false,
        	// "bLengthChange" : false,
        });
    });

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

    function update(value)
    {
    	var data = value.split('|');

    	$('#'+data[0]).html('<i class="fa fa-spinner fa-spin"></i>');

    	$.ajax({
    		url: '{{ route('ku_update') }}', 
    		data: { status: data[1], id_mhs_reg: data[0]}, 
    		success: function(result){
    			$('#'+data[0]).html('');
    		},
    		error: function(result,status,xhr){
    			$('#'+data[0]).html('');
    			alert('Gagal mengubah data. Cek koneksi anda.');
    			window.location.reload();
    		}
    	});
    }
</script>
@endsection