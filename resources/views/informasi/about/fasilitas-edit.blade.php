@extends('informasi.layout.main')

@section('title', 'Edit Fasilitas')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-12">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">
							Edit Fasilitas
							<div class="pull-right">
								<a href="{{ route('fasilitas') }}" class="btn btn-primary btn-sm">Kembali</a>
							</div>
						</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('fasilitas_update') }}" method="post" enctype="multipart/form-data" class="form-custom tab-content">
								{{ csrf_field() }}
								<input type="hidden" name="id" value="{{ $r->id }}">
								<input type="hidden" name="gambar_lama" value="{{ $r->gambar }}">
								<div class="row tab-pane active" role="tabpanel">
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Nama Fasilitas</label>
										<input type="text" name="nama_fasilitas" value="{{ $r->judul }}" class="form-control">
									</div>
									<div class="form-group col-md-12">
										<label for="">Keterangan</label>
										<textarea name="keterangan" id="editor" rows="10" cols="80">
							                {{ $r->deskripsi }}
							            </textarea>
									</div>

									<div class="form-group col-md-4 col-sm-12">
										<label for="">Ganti gambar</label>
										<input type="file" name="gambar" accept="image/*" class="form-control"><br>
										<img src="{{ url('storage') }}/fasilitas/{{ $r->gambar }}" width="100">
									</div>

									<div class="col-md-12">
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
	<script src="{{ url('resources') }}/assets/informasi/ckeditor/ckeditor.js"></script>
	<script>
		$('.menu-about').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});
		CKEDITOR.replace( 'editor' );

	</script>

@endsection
