@extends('informasi.layout.main')

@section('title', 'Foto Galeri')

@section('contents')

	<div class="content">
		<div class="row title-page">
			<div class="col-md-12">
				<h1>Galeri &nbsp;<small>List Gambar</small>
					<a href="{{ route('album') }}" class="btn btn-primary btn-xs pull-right">Kembali</a>
				</h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-8">
				{{ Rmt::AlertSuccess() }}
				{{ Rmt::AlertError() }}
				{{ Rmt::AlertErrors($errors) }}
				<div class="widget animated fadeInLeft delay-1">
					<br>
					@if ( count($galeri) > 0 )
						<div class="widget-body">
							<div class="row tab-pane active" role="tabpanel">
								@foreach( $galeri as $r )
									<div class="col-md-3" style="padding: 0;margin-bottom: 2px">
										<div class="gambar" style="background: url('{{ url('storage') }}/galeri/{{ Request::get('id') }}/small-{{ $r->gambar }}') center;background-size: cover;width: 166px;height: 166px;">
											<button onclick="Delete('{{ route('galeri_delete', ['id' => $r->id])}}')" class="btn btn-danger btn-xs pull-right"><i class="fa fa-trash-o"></i></button>
										</div>
									</div>
								@endforeach
							</div>
						</div>
						<div class="clearfix"></div>
					@else
						<div class="widget-body">
							<div class="row tab-pane active" role="tabpanel">
								<div class="text-center alert alert-info">
									<p>Belum ada data</p>
								</div>
							</div>
						</div>
					@endif

				</div>
			</div>
			<div class="col-md-4">
				<div class="widget animated fadeInLeft delay-2">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Gambar Baru</h2>
					</div>

	                <div class="widget-body">
						
						<div id="message-galeri" class="messages error"></div>

						<form method="post" action="{{ route('galeri_store') }}" id="form-galeri" enctype="multipart/form-data" class="form-custom tab-content">
							{{ csrf_field() }}
					      	<input type="hidden" name="id_album" value="{{ Request::get('id') }}">
					      	<div class="row tab-pane active" role="tabpanel">
						      	<div class="form-group col-md-12 col-sm-12">
									<label for="">Gambar</label>
						      		<input type="file" name="files[]" required class="form-control" multiple>
								</div>

								<div class="col-md-12">
									<button class="btn btn-info" id="btn-submit">Upload</button>
									<div class="progress" style="display: none">
										<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width:100%">
									    	0%
										</div>
									</div>
								</div>
							</div>
				        </form>

			        </div>
			    </div>
	        </div>
		</div>
	</div>

@endsection

@section('modal')	

	<div class="modal fade modal-custom modal-small" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<form action="" class="form-custom">
				<div class="modal-content">
					<div class="modal-header modal-header-danger">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel"><i class="fa fa-exclamation-circle"></i>&nbsp; Hapus</h4>
					</div>
					<div class="modal-body">
						<div class="content">
							Apakah Anda yakin ingin menghapus data ini?
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-cancel" data-dismiss="modal">Batal</button>
						<a href="#" id="confirm-hapus" class="btn btn-danger"><i class="fa fa-trash-o"></i>&nbsp; Hapus</a>
					</div>
				</div>
			</form>
		</div>
	</div>

@endsection


@section('registerscript')
	<script src="{{ url('resources') }}/assets/informasi/js/jquery.form.js"></script>

	<script>
		$('.menu-galeri').addClass('actived');
		
		function Delete(url)
		{
			$('#modal-delete').modal('show');

			$('#confirm-hapus').attr('href',url);
			$('#confirm-hapus').click(function(){
				$(this).attr('disabled','');
				$(this).html('<i style="width:14.5px" class="fa fa-spinner fa-spin"></i> Hapus');
			});
		}

		SubmitGambar();

		function SubmitGambar()
		{
			var bar = $(".progress-bar"),
				barc = $('.progress');

			var options = { 
			    beforeSend: function() 
			    {
					$("#btn-submit").hide();
			        bar.attr('style','width:0%');
			        bar.html('0%');
			        barc.show();
			    },
			    uploadProgress: function(event, position, total, percentComplete) {
			        bar.attr('style','width:'+percentComplete+'%');
			        bar.html(percentComplete+'%');
			    },
				success:function(data, status, message) {
					bar.attr('style','width:100%');
			        bar.html('100%');
					window.location.reload();
				},
				error: function(data, status, message)
				{
					barc.hide();
					$("#btn-submit").show();
					var respon = parseObj(data.responseJSON);
					var pesan = '';
					for ( i = 0; i < respon.length; i++ ){
						pesan += "- "+respon[i]+"<br>";
					}

					if ( pesan == '' ) {
						pesan = message;
					}
					$('#message-galeri').hide();
					$('#message-galeri').html(pesan);
					$('#message-galeri').fadeIn(500);

					$('#btn-submit').show();
				}
			}; 

		    $('#form-galeri').ajaxForm(options);
		}

		function parseObj(data){
			arr = []
			for(var event in data){
			    var dataCopy = data[event]
			    for(key in dataCopy){
			        if(key == "start" || key == "end"){
			            dataCopy[key] = new Date(dataCopy[key])
			        }
			    }
			    arr.push(dataCopy)
			}
			return arr;
		}
	</script>

@endsection