@extends('informasi.layout.main')

@section('title', 'Profil')

@section('styles')
	<style>
		.alert { margin-bottom: 0 !important }
	</style>
@endsection

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active"><a href="{{ route('profil') }}">Profil</a></li>
					<li><a href="{{ route('visi') }}">Visi Misi</a></li>
					<li><a href="{{ route('keunggulan') }}">Keunggulan</a></li>
					<li><a href="{{ route('prodi') }}">Prodi</a></li>
					<li><a href="{{ route('fasilitas') }}">Fasilitas</a></li>
					<li><a href="{{ route('peta') }}">Peta</a></li>
				</ul>

				<div class="widget animated fadeInLeft delay-1">

					<div class="widget-body" style="padding: 0 0 10px 0">

						@if ( $profil->count() > 0 )
							<?php $r = $profil->first() ?>
							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('profil_update') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}
								<div class="row tab-pane active" role="tabpanel">

									<div class="form-group col-md-12">
										<textarea name="konten" id="editor" rows="10" cols="80">
							                {{ $r->value }}
							            </textarea>
									</div>

									<div class="col-md-12 text-right" style="right: 10px">
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
	<script src="{{ url('resources') }}/assets/informasi/ckeditor/ckeditor.js"></script>
	<script>
		$('.menu-about').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});
		CKEDITOR.replace( 'editor' );

	</script>

@endsection
