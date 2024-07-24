@extends('informasi.layout.main')

@section('title', 'Pengaturan')

@section('contents')

	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Pengaturan</h2>
					</div>
					<div class="widget-body">

						{{ Rmt::AlertSuccess() }}

						@if ( $pengaturan->count() > 0 )

							<?php $peng = $pengaturan->get() ?>

							<form action="{{ route('pengaturan_update') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}
								<div class="row tab-pane active" role="tabpanel">
									
									@foreach ( $peng as $pe )
										<div class="form-group col-md-12">
											<label for="">{{ $pe->option }}</label>
											<input type="hidden" name="option[]" value="{{ $pe->option }}">
											<input type="text" value="{{ $pe->value }}" name="value[]" required class="form-control">
										</div>
									@endforeach

									<div class="col-md-12 text-right">
										<br>
										<button class="btn btn-info">Simpan</button>
									</div>
								</div>
							</form>

						@else
							Belum ada data
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>


@endsection

@section('registerscript')
	
	<script>

		$('.menu-pengaturan').addClass('actived');
		
	</script>

@endsection
