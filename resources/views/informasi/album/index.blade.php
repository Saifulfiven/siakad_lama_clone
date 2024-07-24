@extends('informasi.layout.main')

@section('title', 'Album')

@section('styles')
	<link href="{{ url('resources') }}/assets/informasi/css/jquery-ui.css" rel="stylesheet">
@endsection

@section('contents')

	<div class="content">
		<div class="row title-page">
			<div class="col-md-12">
				<h1>Album &nbsp;<small>Daftar Album</small></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-7">
				{{ Rmt::AlertSuccess() }}
				{{ Rmt::AlertError() }}
				{{ Rmt::AlertErrors($errors) }}
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">

						@php($urutan = 1)
						@if ( count($album) > 0 )
							<br>
							<div class="panel panel-default">

		                        <div class="panel-body">
		                          
		                          <div id="list">

		                              <div id="primary-list">

		                               	@foreach( $album as $r )
		                                  
		                                    <div class="panel-heading" id="arrayOrder-{{ $r->id }}" 
		                                      style="border:none;background:#eee;margin-bottom:5px">

		                                      <h4 class="panel-title">
		                                          <span class='judul'><small><a href="{{ route('galeri', ['id' => $r->id]) }}">{{ $r->judul }}</a></small></span>
		                                          <label class="pull-right">
														<button type="button" class="btn btn-default btn-xs" onclick="edit('{{ $r->id }}','{{ $r->judul }}')"><i class="fa fa-pencil"></i></button>
														<button type="button" class="btn btn-default btn-xs" onclick="Delete('{{ route('album_delete', ['id' => $r->id])}}')"><i class="fa fa-trash-o"></i></button>
		                                          </label>
		                                      </h4>

		                                    </div>
		                                    @php($urutan = $r->order + 1)

		                               @endforeach

		                               </div>

		                          </div>

		                        </div>

		                    </div>

						@else
							<br>
							<div class="text-center alert alert-info">
								<p>Belum ada data</p>
							</div>
						@endif

					</div>

				</div>
			</div>
			
			<div class="col-md-5">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title text-uppercase">Album Baru</h2>
					</div>
					<div class="widget-body">
						<form action="{{ route('album_store') }}" method="post" class="form-custom tab-content">
							{{ csrf_field() }}
							<input type="hidden" name="id" id="id" value="">
							<input type="hidden" name="urutan" value="{{ $urutan }}">
							<div class="row tab-pane active" role="tabpanel">
								<div class="form-group col-md-12 col-sm-12">
									<label for="">Judul</label>
									<input type="text" name="judul" id="judul" value="{{ old('judul') }}" class="form-control">
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
	<script src="{{ url('resources') }}/assets/informasi/js/jquery-ui.js"></script>
	
	<script>
		$('.menu-galeri').addClass('actived');
		
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});

        $("#list #primary-list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
      
            	var urutan = $(this).sortable("serialize");
            	$.get('{{ route('album_urutan') }}',{urutan:urutan})
            		.error(function(data,status,msg){
            			alert('Gagal mengubah urutan : '+msg);
            		});
           }
        });

        function edit(id,judul){
        	$('#judul').val(judul);
        	$('#judul').focus();
        	$('#id').val(id);
        }

		function Delete(url)
		{
			$('#modal-delete').modal('show');

			$('#confirm-hapus').attr('href',url);
			$('#confirm-hapus').click(function(){
				$(this).attr('disabled','');
				$(this).html('<i style="width:14.5px" class="fa fa-spinner fa-spin"></i> Hapus');
			});
		}

	</script>

@endsection