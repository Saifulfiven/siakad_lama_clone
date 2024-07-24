@extends('informasi.layout.main')

@section('title', 'Edit RPS')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-7">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Edit RPS</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('gbpp_update') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}
								<input type="hidden" name="id" value="{{ $gbpp->id }}">
								<div class="row tab-pane active" role="tabpanel">
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Nama Matakuliah</label>
										<input type="text" required name="judul" value="{{ $gbpp->judul }}" class="form-control">
									</div>
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Prodi</label>
										<select name="prodi" class="form-control" required="">
											<option value="">Pilih Prodi</option>
											<option value="61201" {{ $gbpp->prodi == '61201' ? 'selected':'' }}>Manajemen</option>
											<option value="62201" {{ $gbpp->prodi == '62201' ? 'selected':'' }}>Akuntansi</option>
											<option value="61101" {{ $gbpp->prodi == '61101' ? 'selected':'' }}>Pascasarjana</option>
										</select>
									</div>
									<div class="form-group col-md-12 col-sm-12">
										<label for="">Link</label>
										<input type="text" name="link" value="{{ $gbpp->link }}" class="form-control">
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
		$('.menu-gbpp').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});

	</script>

@endsection
