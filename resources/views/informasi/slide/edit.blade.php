@extends('informasi.layout.main')

@section('title', 'Edit Pedoman Akademik')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-8">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Ubah Slide</h2>
					</div>
					<div class="widget-body">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertErrors($errors) }}
						<form action="{{ route('slide_update') }}" method="post" enctype="multipart/form-data" class="form-custom tab-content">
							{{ csrf_field() }}
							<input type="hidden" name="id" value="{{ $r->id }}">
							<input type="hidden" name="gambar_lama" value="{{ $r->gambar }}">
							<div class="row tab-pane active" role="tabpanel">
								<div class="form-group col-md-8 col-sm-12">
									<label for="">Keterangan</label>
									<input type="text" name="keterangan" value="{{ $r->ket }}" class="form-control">
								</div>
								<div class="form-group col-md-6">
									<label for="">Gambar</label>
									<input type="file" name="gambar" class="form-control">
									<br>
									<img src="{{ url('storage') }}/slide/{{ $r->gambar }}" width="100">
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
		$('.menu-pedoman').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});

	</script>

@endsection
