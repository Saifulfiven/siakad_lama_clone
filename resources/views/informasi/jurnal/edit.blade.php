@extends('informasi.layout.main')

@section('title', 'Edit Dokumen Jurnal')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-7">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Dokumen Jurnal Baru</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('jurnal_update') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}
								<input type="hidden" name="id" value="{{ $jurnal->id }}">
								<div class="row tab-pane active" role="tabpanel">
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Judul</label>
										<input type="text" required name="judul" value="{{ $jurnal->judul }}" class="form-control">
									</div>
									<div class="form-group col-md-12 col-sm-12">
										<label for="">Url</label>
										<input type="text" name="url" value="{{ $jurnal->url }}" class="form-control">
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
		$('.menu-jurnal').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});

	</script>

@endsection
