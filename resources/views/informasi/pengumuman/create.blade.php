@extends('informasi.layout.main')

@section('title', 'Tambah Pengumuman')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-12">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Pengumuman Baru</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('pengumuman_store') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}

								<div class="row tab-pane active" role="tabpanel">
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Judul</label>
										<input type="text" name="judul" value="{{ old('judul') }}" class="form-control">
									</div>

									<div class="form-group col-md-8 col-sm-12">
										<label for="">Pengumuman untuk</label>
										<select name="kategori" class="form-control" style="width: 150px">
											@foreach( Rmt::kategoriInformasi() as $k )
												<option value="{{ $k }}" {{ old('kategori') == $k ? 'selected':'' }}>{{ $k }}</option>
											@endforeach
										</select>
									</div>

									<div class="form-group col-md-12">
										<label for="">Konten</label>
										<textarea name="konten" id="editor" rows="10" cols="80">
							                {{ old('konten') }}
							            </textarea>
									</div>

									<div class="form-group col-md-8 col-sm-12">
										<label for="">File</label>
										<input type="file" name="file" value="{{ old('file') }}" class="form-control">
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
	<script src="{{ url('resources') }}/assets/informasi/ckeditor/ckeditor.js"></script>
	<script>
		$('.menu-pengumuman').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});
		CKEDITOR.replace( 'editor' );

	</script>

@endsection
