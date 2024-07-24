@extends('layouts.app')

@section('title','Kehadiran Mahasiswa')

@section('heading')
<style type="text/css">
	thead input {
        width: 100%;
    }
</style>

@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Kehadiran Mahasiswa 
						<select class="form-custom mw-2" onchange="filter('smt', this.value)" id='filter-smt'>
							@foreach( $semester as $smt )
								<option value="{{ $smt->id_smt }}" {{ Session::get('abs_smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
							@endforeach
						</select>

						<select class="form-custom mw-2" onchange="filter('angkatan', this.value)" id="list-prodi">
							<option value="all">All angkatan</option>
							@foreach( Sia::listAngkatan() as $a )
		                    	<option value="{{ $a }}" {{ Session::get('abs_angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
		                    @endforeach
						</select>

						<select class="form-custom mw-2" name="prodi" onchange="filter('prodi', this.value)" id="list-prodi">
							@foreach( Sia::listProdi() as $pr )
		                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('abs_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
		                    @endforeach
						</select>
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="150">
										
									</td>
									<td width="165">
										
									</td>
									<td></td>

									<!-- <td width="300px">
										<form action="" id="form-cari">
											<div class="input-group pull-right">
												<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
												<div class="input-group-btn">
													<a href="{{ route('keu_praktek') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
													<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td> -->

								</tr>
							</table>
							
							<div class="table-responsive">
								<table class="table display table-bordered table-striped table-hover" id="data-table">
									<thead class="custom">
										<tr>
											<th>No.</th>
											<th>NIM</th>
											<th>Nama</th>
											<th>Prodi</th>
											<th>Total Hadir</th>
											<th>Jml Mk</th>
											<th>Rata2 Kehadiran</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach($mahasiswa as $r)
											<?php
												$absen = !empty($r->absen) || !empty($r->mk) ? $r->absen / $r->mk : 0; ?>
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td>{{ $r->jenjang .' '. $r->nm_prodi }}</td>
												<td>{{ $r->absen }}</td>
												<td>{{ $r->mk }}</td>
												<td>{{ number_format($absen, 2) }}</td>
												<td>
													<a href="{{ route('absen_mhs', ['id_mhs_reg' => $r->id_mhs_reg]) }}" class="btn btn-primary btn-sm"><i class="fa fa-search-plus"></i> Buka</a>
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
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>
<script>

    $(document).ready(function(){
    	// Setup - add a text input to each footer cell
	    $('#data-table thead tr').clone(true).appendTo( '#data-table thead' );
	    $('#data-table thead tr:eq(1) th').each( function (i) {
	    	

	        var title = $(this).text();
	        if ( i === 7 || i === 0 || i === 3 ) {
	    		$(this).html('');
	    	} else {
	        	$(this).html( '<input type="text" class="form-custom" placeholder="Cari '+title+'" />' );
	 		}

	        $( 'input', this ).on( 'keyup change', function () {
	            if ( table.column(i).search() !== this.value ) {
	                table
	                    .column(i)
	                    .search( this.value )
	                    .draw();
	            }
	        } );
	    } );

	    var table = $('#data-table').DataTable( {
	        orderCellsTop: true
	    } );
	 
	    

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