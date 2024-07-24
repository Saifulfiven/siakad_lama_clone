@extends('informasi.layout.main')

@section('title', 'Tambah Foto')

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-8">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Foto Baru</h2>
					</div>
					<div class="widget-body">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							{{ Rmt::AlertErrors($errors) }}

							
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

		Dropzone.options.galeriDropzone = {
		  paramName: "files", // The name that will be used to transfer the file
		  maxFilesize: 2, // MB
		  accept: function(file, done) {
		    if (file.name == "justinbieber.jpg") {
		      done("Naha, you don't.");
		    }
		    else { done(); }
		  },
		  uploadMultiple: true
		};
		// $("#galeri-dropzone").dropzone({ url: "{{ route('galeri_store') }}" });

	</script>

@endsection
