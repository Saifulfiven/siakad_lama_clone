@extends('informasi.layout.main')

@section('title', 'Fasilitas')

@section('styles')
	<style>
		.alert { margin-bottom: 0 !important }
	</style>
	<link href="{{ url('resources') }}/assets/informasi/css/jquery-ui.css" rel="stylesheet">
@endsection

@section('contents')

	<div class="content">

		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li><a href="{{ route('profil') }}">Profil</a></li>
					<li><a href="{{ route('visi') }}">Visi Misi</a></li>
					<li><a href="{{ route('keunggulan') }}">Keunggulan</a></li>
					<li><a href="{{ route('prodi') }}">Prodi</a></li>
					<li class="active"><a href="{{ route('fasilitas') }}">Fasilitas</a></li>
					<li><a href="{{ route('peta') }}">Peta</a></li>
				</ul>

				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">

					<div class="widget-option" style="margin-top: -15px;margin-right: 5px">
						<a href="{{ route('fasilitas_create') }}" id="add-account">+ Fasilitas Baru</a>
					</div>
					
					@php($urutan = 1)
					@if ( count($fasilitas) > 0 )
						<br>
						<div class="panel panel-default">

	                        <div class="panel-body">
	                          
	                          <div id="list">

	                              <div id="primary-list">

	                               	@foreach( $fasilitas as $r )
	                                  
	                                    <div class="panel-heading" id="arrayurutan-{{ $r->id }}" 
	                                      style="border:none;background:#eee;margin-bottom:5px">

	                                      <h4 class="panel-title">
	                                          <span class='judul'><small>{{ $r->judul }}</small></span>
	                                          <label class="pull-right">
	                                          		<a href="{{ route('fasilitas_edit', ['id' => $r->id]) }}" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>
													<a href="javascript:;" class="btn btn-default btn-xs" onclick="Delete('{{ route('fasilitas_delete', ['id' => $r->id])}}')"><i class="fa fa-trash-o"></i></a>
	                                          </label>
	                                      </h4>

	                                    </div>
	                                    @php($urutan = $r->urutan + 1)

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
		$('.menu-about').addClass('actived');
		$('.form-custom').on('submit', function(){
			$(this).find('button').attr('disabled','');
		});

		$('#add-account').attr('href','{{ route('fasilitas_create') }}?urutan={{ $urutan }}');

        $("#list #primary-list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
      
            	var urutan = $(this).sortable("serialize");
            	$.get('{{ route('fasilitas_urutan') }}',{urutan:urutan})
            		.error(function(data,status,msg){
            			alert('Gagal mengubah urutan : '+msg);
            		});
           }
        });

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
