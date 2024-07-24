@extends('informasi.layout.main')

@section('title', 'Tambah Literatur')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-7">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Literatur Baru</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('literatur_store') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}
								<input type="hidden" name="order" value="{{ Request::get('order') }}">
								<div class="row tab-pane active" role="tabpanel">
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Nama Matakuliah</label>
										<input type="text" required name="judul" value="{{ old('judul') }}" class="form-control">
									</div>
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Nama Dosen</label>
										<input type="text" name="dosen" value="{{ old('dosen') }}" class="form-control">
									</div>
									<div class="form-group col-md-12 col-sm-12">
										<label for="">Url</label>
										<input type="text" name="url" value="{{ old('url') }}" class="form-control">
									</div>

									<div class="col-md-12">
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
		$('.menu-literatur').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});

	</script>

@endsection
