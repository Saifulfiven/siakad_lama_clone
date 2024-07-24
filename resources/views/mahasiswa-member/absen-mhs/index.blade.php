@extends('layouts.app')

@section('title','Absensi Mahasiswa')

@section('content')

<div id="content" class="content-bimbingan">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Absensi Mahasiswa
				</header>

				<div class="panel-body">

						<div class="alert bg-theme-inverse" style="padding: 15px !important">
							Berikut ini matakuliah yang anda program semester ini, <b>klik</b> untuk <b>mengabsen</b> atau melihat riwayat absensi anda.<br>
							<b>NB:</b> Warna latar akan berwarna merah apabila absen terbuka
						</div>

						@foreach($jadwal as $r)

							<?php

								$absen = App\AbsenMhs::where('id_jdk', $r->id)
															->where('updated_at', '>', Carbon::now())
															->count();

							?>

							<div class="col-md-6" style="padding: 5px;">

								<a href="{{ route('mhs_absensi_detail', ['id_jdk' => $r->id]) }}">
									<div class="well {{ $absen > 0 ? 'bg-theme' : 'bg-info' }}" style="padding-bottom: 0">
										<div class="widget-tile">
											<section>
												<h5>{{ $r->kode_mk }} <br>
													<strong>{{ $r->nm_mk }}</strong>
												</h5>
												<!-- <h2><i class="fa fa-spinner fa-spin"></i></h2> -->
												<div class="progress progress-xs progress-white progress-over-tile">
														<div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="1000" aria-valuemax="1000"></div>
												</div>
												<label class="progress-label label-white" style="font-size: 12px">
													<?= $r->dosen ?>
												</label>

											</section>

											@if ( $absen > 0 )
												<div class="pull-right"><small>Absen terbuka</small></div>
											@endif

											<div class="hold-icon"><i class="fa fa-check-square-o"></i></div>
										</div>
									</div>
								</a>
							
							</div>

						@endforeach

				</div>

			</section>

		</div>

	</div>

</div>

@endsection

@section('registerscript')
<script>

</script>
@endsection