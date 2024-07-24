@extends('informasi.layout.main')

@section('title', 'Literatur')

@section('contents')

	<div class="content">
		<div class="row title-page">
			<div class="col-md-12">
				<h1>Literatur &nbsp;<small>Daftar Literatur</small></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				{{ Rmt::AlertSuccess() }}
				{{ Rmt::AlertError() }}
				{{ Rmt::AlertErrors($errors) }}
				<div class="widget animated fadeInLeft delay-1">

					<div class="widget-option" style="margin-top: 5px;margin-right: 5px"><a href="{{ route('literatur_create') }}" id="add-account">+ Literatur Baru</a></div>
					
					<div class="widget-body">
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="tab-1">

								@if ( $literatur->total() > 0 )

									<table class="table lists absen">
										<tr class="first">
											<td class="checkbox-custom" width="40px">
												<input type="checkbox" id="page-all" data-check="page" onclick="checkAll(this)">
												<label for="page-all"><i class="fa fa-check"></i></label>
											</td>
											<td colspan="2">Tandai Semua &nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="delete" onclick="DeleteMassal('check', '{{ route('literatur_delete') }}')"><i class="fa fa-trash-o"></i>&nbsp; Hapus</a></td>
										</tr>

										@foreach( $literatur as $r )
											<tr>
												<td class="checkbox-custom">
													<input type="checkbox" name="" id="page-{{ $r->id }}" value="{{ $r->id }}" data-check="page" class="check">
													<label for="page-{{ $r->id }}"><i class="fa fa-check"></i></label>
												</td>
												<td class="absen-title">
													{{ $r->judul }}
												</td>
												<td class="absen-title">
													{{ $r->dosen }}
												</td>
												<td class="text-right action">
													<div class="btn-group" role="group">
														<a href="{{ $r->url }}" target="blank" class="btn btn-default"><i class="fa fa-search-plus"></i></a>
														<a href="{{ route('literatur_edit',['id' => $r->id]) }}" class="btn btn-default"><i class="fa fa-pencil"></i></a>
														<a href="javascript:;" class="btn btn-default" onclick="Delete('{{ route('literatur_delete', ['id' => $r->id])}}')"><i class="fa fa-trash-o"></i></a>
													</div>
												</td>
											</tr>
										@endforeach

									</table>

									<div class="text-center">
										{{ $literatur->links() }}
									</div>

								@else
									<br>
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
	
	<script>
		$('.menu-literatur').addClass('actived');

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