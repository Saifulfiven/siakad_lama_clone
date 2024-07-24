@extends('layouts.app')

@section('title','Daftar Mahasiswa Menunggak')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Daftar Mahasiswa Yang Menunggak
						<div class="pull-right">
							<a href="javascript:;" id="link-cetak-langsung" class="btn btn-theme-inverse btn-xs"><i class="fa fa-download"></i> Cetak</a>
						</div>
					</header>

					<div class="panel-body">

						<div class="col-md-2" style="padding-left: 0">

						</div>

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							@if ( Session::has('errors_impor') )
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert">
										<span aria-hidden="true">&times;</span>
										<span class="sr-only">Close</span>
									</button>
									<b>DETAIL KESALAHAN..!</b><br>
									@foreach( Session::get('errors_impor') as $er )
										- {{ $er }}<br>
									@endforeach
								</div>
							@endif

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="120">
										<select class="form-custom mw-2" name="angkatan" onchange="filter('angkatan', this.value)">
											<option value="all">Angkatan</option>
											@foreach( Sia::listAngkatan() as $a )
						                    	<option value="{{ $a }}" {{ Session::get('tu_angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
						                    @endforeach
										</select>
									</td>
									<td width="150">
										<select class="form-custom mw-2" name="smt" onchange="filter('smtin', this.value)" {{ Session::get('tu_angkatan') == 'all' ? 'disabled':'' }}>
											<option value="all">All smt masuk</option>
						                    <option value="1" {{ Session::get('tu_smtin') == 1 ? 'selected' : '' }}>GANJIL</option>
						                    <option value="2" {{ Session::get('tu_smtin') == 2 ? 'selected' : '' }}>GENAP</option>
										</select>
									</td>
									<td width="150">
										<select class="form-custom mw-2" name="prodi" onchange="filter('prodi', this.value)" id="list-prodi">
											<option value="all">Semua Prodi</option>
											@foreach( Sia::listProdi() as $pr )
						                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('tu_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
						                    @endforeach
										</select>
									</td>
									<td width="">
										<select class="form-custom mw-2" onchange="filter('status', this.value)">
											@foreach( Sia::statusMhs() as $st )
												<option value="{{ $st->id_jns_keluar }}" {{ $st->id_jns_keluar == Session::get('tu_status') ? 'selected':'' }}>{{ $st->ket_keluar }}</option>
								            @endforeach
										</select>
									</td>

								</tr>
							</table>
							
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" data-provide="data-table">
								<thead class="custom">
									<tr>
										<th>No.</th>
										<th width="120">NIM</th>
										<th>Nama</th>
										<th>Prodi</th>
										<th>Status</th>
										<th>Tunggakan</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									<?php $no = 1 ?>
									<?php $smt = Sia::sessionPeriode('berjalan'); ?>
									@foreach($mahasiswa as $r)
										<?php
		                                $smt_mulai = $r->semester_mulai > Rmt::smtMulaiOnTunggakan() ? $r->semester_mulai : Rmt::smtMulaiOnTunggakan();
		                                $tunggakan = Sia::tunggakan($r->id_mhs_reg, $smt_mulai, Sia::sessionPeriode('berjalan')); 

		                                if ( $tunggakan <= 0 ) continue; ?>

										<tr>
											<td>{{ $no++ }}</td>
											<td width="100">{{ $r->nim }}</td>
											<td align="left">{{ $r->nm_mhs }}</td>
											<td>{{ $r->jenjang .' '. $r->nm_prodi }}</td>
											<td>{{ $r->ket_keluar }}</td>
											<td>Rp {{ Rmt::rupiah($tunggakan) }}</td>
											<td>
												<a href="{{ route('keu_detail', ['id' => $r->id_mhs_reg, 'smt' => $smt]) }}" class="btn btn-xs btn-primary">
													<i class="fa fa-search-plus">
												</i>
											</a>
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
@endsection

@section('registerscript')
<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>

<script>

    $(document).ready(function(){

    	$('#nav-mini').trigger('click');

        $('table[data-provide="data-table"]').dataTable({
        	// "bFilter": false,
        	// "bLengthChange" : false,
        });

    });

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

</script>
@endsection