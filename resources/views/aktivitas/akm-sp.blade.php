@extends('layouts.app')

@section('title','AKM SP')

@section('content')
	<div id="overlay"></div>

	<div id="content">

		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Hitung Aktivitas Kuliah Mahasiswa TA. {{ Sia::sessionPeriode('nama') }} Semester Pendek
						<a href="{{ route('akm') }}" class="btn btn-success btn-xs pull-right">Kembali</a>
					</header>

					<div class="panel-body">
				        <div class="row">
				            <div class="col-md-12">

				            	{{ Rmt::alertError() }}
				            	{{ Rmt::alertSuccess() }}

				            	<table border="0">
									<tr>
										<td width="100">Program Studi</td>
										<td>
											<select class="form-custom mw-2" id="filter-prodi">
												@foreach( Sia::listProdi() as $pr )
							                    	<option value="{{ $pr->id_prodi }}" {{ Request::get('prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
							                    @endforeach
											</select>
										</td>
									</tr>

									
								</table>

								<br>

								<div class="table-responsive">


	                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-hover">
	                                    <thead class="custom">
	                                        <tr>
	                                            <th width=30px">No.</th>
	                                            <th>NIM</th>
	                                            <th>Nama Mahasiswa</th>
	                                            <th>Semester</th>
	                                            <th>SKS semester</th>
	                                            <th>IPS</th>
	                                            <th>Total sks</th>
	                                            <th>IPK</th>
	                                            <th width="20px">status</th>
	                                        </tr>
	                                    </thead>
	                                    <tbody align="center">
	                                    	<?php $no = 1 ?>
											@foreach( $mahasiswa as $m )
												<!-- Jika lulus di periode ini dan sks smt = 0 maka lewati 
													Artinya aktivitas terakhirnya telah dilapor di periode sebelumnya -->
												<?php if ( empty($m->sks_smt) && $m->semester_keluar == Sia::sessionPeriode() ) continue ?>

												<?php $ipk = Sia::ipkAktivitas2($m->id) ?>

												<tr<?= empty($m->sks_smt) ? ' class=empty-sks':'' ?>>
													<td>{{ $no++ }}</td>
													<td align="left">{{ $m->nim }}</td>
													<td align="left">{{ $m->nm_mhs }}</td>
													<td align="left">{{ Sia::sessionPeriode() + 1 }}</td>
													<td>{{ $m->sks_smt }}</td>
													<td>{{ number_format($m->ips,2) }}</td>
													<td>{{ $m->sks_total >= 145 ? '145' : $m->sks_total  }}</td>
													<td>{{ $ipk }}</td>
													<td>A</td>
												</tr>
											@endforeach
	                                    </tbody>
	                                </table>
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
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
            
<script>

$(function(){
	$('#filter-prodi').change(function(){
		var prodi = $(this).val();
		var url = '?prodi='+prodi+'&angkatan={{ Request::get('angkatan') }}';
		window.location.href=url;
	});

	$('#form-status').on('submit', function(){
		var btn = $('#submit');
		btn.attr('disabled','');
		btn.html('<i class="fa fa-spinner fa-spin"></i> MENYIMPAN');
	});

	$('.empty-sks').css('background-color', 'rgb(245, 245, 158)');
})


</script>
@endsection