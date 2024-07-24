@extends('informasi.layout.main')

@section('title', 'Tambah Kalender Akademik')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-8">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Kalender Akademik Baru</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('kalender_store') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}
								<input type="hidden" name="order" value="{{ Request::get('order') }}">
								<div class="row tab-pane active" role="tabpanel">
									<div class="form-group col-md-6 col-sm-12">
										<label for="">Kategori</label>
										<select name="kategori" class="form-control">
											<?php $kategori = Rmt::kategoriKalender() ?>
											@foreach( $kategori as $key => $val )
												<option value="{{ $key }}" {{ old('kategori') == $key ? 'selected':'' }}>{{ $val }}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Tanggal</label>
										<input type="text" name="tanggal" value="{{ old('tanggal') }}" class="form-control">
									</div>
									<div class="form-group col-md-12 col-sm-12">
										<label for="">Deskripsi</label>
										<input type="text" name="deskripsi" value="{{ old('deskripsi') }}" class="form-control">
									</div>

									<div class="col-md-12 text-right">
										<br>
										<button class="btn btn-info">Simpan</button>
									</div>
								</div>
							</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('registerscript')
	<script>
		$('.menu-kalender').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});

	</script>

@endsection
