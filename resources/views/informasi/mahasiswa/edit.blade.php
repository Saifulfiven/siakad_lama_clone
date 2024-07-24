@extends('informasi.layout.main')

@section('title', 'Edit Mahasiswa')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-6">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Ubah Mahasiswa</h2>
					</div>
					<div class="widget-body">
						
						{{ Rmt::AlertSuccess() }}
						{{ Rmt::AlertError() }}
						{{ Rmt::AlertErrors($errors) }}

						<form action="{{ route('mahasiswa_update') }}" method="post" class="form-custom tab-content">
							{{ csrf_field() }}
							<input type="hidden" name="id" value="{{ $r->id }}">
							<div class="row tab-pane active" role="tabpanel">
								<div class="form-group col-md-12">
									<label for="">NIM</label>
									<input type="text" name="nim" value="{{ $r->nim }}" class="form-control">
								</div>
								<div class="form-group col-md-12">
									<label for="">Nama</label>
									<input type="text" name="nama" value="{{ $r->nama }}" class="form-control">
								</div>
								<div class="form-group col-md-12">
									<label for="">Username</label>
									<input type="text" name="username" value="{{ $r->username }}" class="form-control">
								</div>
								<div class="form-group col-md-12">
									<label for="">Password Baru</label>
									<input type="text" name="password" class="form-control">
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
		$('.menu-mahasiswa').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});
	</script>

@endsection
