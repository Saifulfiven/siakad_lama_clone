@extends('informasi.layout.main')

@section('title', 'Slide')

@section('styles')
	<link href="{{ url('resources') }}/assets/informasi/css/jquery-ui.css" rel="stylesheet">
@endsection

@section('contents')

	<div class="content">
		<div class="row title-page">
			<div class="col-md-12">
				<h1>Slide &nbsp;<small>Daftar Slide</small></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				{{ Rmt::AlertSuccess() }}
				{{ Rmt::AlertError() }}
				{{ Rmt::AlertErrors($errors) }}
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">

					<div class="widget-option" style="margin-top: -15px;margin-right: 5px">
						<a href="{{ route('slide_create') }}" id="add-account">+ Slide Baru</a>
					</div>
					
					@php($order = 1)
					@if ( count($slide) > 0 )
						<br>
						<div class="panel panel-default">

	                        <div class="panel-body">
	                          
	                          <div id="list">

	                              <div id="primary-list">

	                               	@foreach( $slide as $r )
	                                  
	                                    <div class="panel-heading" id="arrayOrder-{{ $r->id }}" 
	                                      style="border:none;background:#eee;margin-bottom:5px">

	                                      <h4 class="panel-title">
	                                          <span class='judul'><small>{{ $r->ket }}</small></span>
	                                          <label class="pull-right">
	                                          		<a href="{{ route('slide_edit',['id' => $r->id]) }}" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>
													<a href="javascript:;" class="btn btn-default btn-xs" onclick="Delete('{{ route('slide_delete', ['id' => $r->id])}}')"><i class="fa fa-trash-o"></i></a>
	                                          </label>
	                                      </h4>

	                                    </div>
	                                    @php($order = $r->order + 1)

	                               @endforeach

	                               </div>

	                          </div>

	                        </div>

	                    </div>

					@else
						<br>
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
		$('.menu-pedoman').addClass('actived');

		$('#add-account').attr('href','{{ route('slide_create') }}?order={{ $order }}');

        $("#list #primary-list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
      
            	var order = $(this).sortable("serialize");
            	$.get('{{ route('slide_order') }}',{order:order})
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

		function DeleteMassal(classs,url,btn_hapus = 'confirm-hapus')
		{

			if($('.'+classs+':checked').length) {

				$('#'+btn_hapus).click(function(){
					$(this).attr('disabled','');
					$(this).html('<i style="width:14.5px" class="fa fa-spinner fa-spin"></i> Hapus');
				});

				$('#modal-delete').modal('show');
				
				var id = "";
				$('.'+classs+':checked').each(function() {
					id += $(this).val() + ",";
				});

				id =  id.slice(0,-1);
			}
			else {
				return false;
			}
			$('#'+btn_hapus).attr('href',url+"/"+id);
		}
	</script>

@endsection