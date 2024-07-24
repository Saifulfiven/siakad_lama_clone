@extends('informasi.layout.main')

@section('title', 'Tambah RPS')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-7">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">RPS Baru</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}
							<form action="{{ route('gbpp_store') }}" method="post" class="form-custom tab-content">
								{{ csrf_field() }}
								<div class="row tab-pane active" role="tabpanel">
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Nama Matakuliah</label>
										<input type="text" required name="judul" value="{{ old('judul') }}" class="form-control">
									</div>
									<div class="form-group col-md-8 col-sm-12">
										<label for="">Prodi</label>
										<select name="prodi" class="form-control" required="">
											<option value="">Pilih Prodi</option>
											<option value="61201" {{ old('prodi') == '61201' ? 'selected':'' }}>S1 - Manajemen</option>
											<option value="62201" {{ old('prodi') == '62201' ? 'selected':'' }}>S1 - Akuntansi</option>
											<option value="59201" {{ old('prodi') == '59201' ? 'selected':'' }}>S1 - Sistem dan Teknologi Informasi</option>
											<option value="54244" {{ old('prodi') == '54244' ? 'selected':'' }}>S1 - Teknologi Hasil Perikanan</option>
											<option value="31201" {{ old('prodi') == '31201' ? 'selected':'' }}>S1 - Teknik Pertambangan</option>
											<option value="26201" {{ old('prodi') == '26201' ? 'selected':'' }}>S1 - Teknik Industri</option>
											<option value="83207" {{ old('prodi') == '83207' ? 'selected':'' }}>S1 - Pendidikan Teknologi Informasi</option>

											<option value="61101" {{ old('prodi') == '61101' ? 'selected':'' }}>S2 - Program Magister Manajemen</option>
											<option value="61112" {{ old('prodi') == '61112' ? 'selected':'' }}>S2 - Program Magister Keuangan Publik</option>
											<option value="61113" {{ old('prodi') == '61113' ? 'selected':'' }}>S2 - Program Magister Manajemen dan Kewirausahaan</option>

										</select>
									</div>
									<div class="form-group col-md-12 col-sm-12">
										<label for="">Link</label>
										<input type="text" name="link" value="{{ old('link') }}" class="form-control">
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
