@extends('informasi.layout.main')

@section('title', 'Pengumuman')

@section('contents')

	<div class="content">
		<div class="row title-page">
			<div class="col-md-12">
				<h1>Pengumuman &nbsp;<small>Daftar Pengumuman</small></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				{{ Rmt::AlertSuccess() }}
				{{ Rmt::AlertError() }}
				{{ Rmt::AlertErrors($errors) }}
				<div class="widget animated fadeInLeft delay-1">

					<div class="widget-option" style="margin-top: 5px;margin-right: 5px"><a href="{{ route('pengumuman_create') }}" id="add-account">+ Pengumuman Baru</a></div>
					
					<div class="widget-body">
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="tab-1">
								<div style="padding: 10px 0 5px 0;border-bottom: 1px solid #eee">
									Kategori : 
									<select style="width: 150px;border: none" onchange="changeKategori(this.value)">
										<option value="">Semua</option>
										@foreach( Rmt::kategoriInformasi() as $val )
											<option value="{{ $val }}" {{ $val == Request::get('kat') ? 'selected':'' }}>{{ $val }}</option>
										@endforeach
									</select>
								</div>

								@if ( $pengumuman->total() > 0 )

									<table class="table lists absen">
										<tr class="first">
											<td class="checkbox-custom" width="40px">
												<input type="checkbox" id="page-all" data-check="page" onclick="checkAll(this)">
												<label for="page-all"><i class="fa fa-check"></i></label>
											</td>
											<td colspan="2">Tandai Semua &nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="delete" onclick="DeleteMassal('check', '{{ route('pengumuman_delete') }}')"><i class="fa fa-trash-o"></i>&nbsp; Hapus</a></td>
										</tr>

										@foreach( $pengumuman as $m )
											<tr>
												<td class="checkbox-custom">
													<input type="checkbox" name="" id="page-{{ $m->id }}" value="{{ $m->id }}" data-check="page" class="check">
													<label for="page-{{ $m->id }}"><i class="fa fa-check"></i></label>
												</td>
												<td class="absen-title">
													{{ $m->judul }}
												</td>
												<td class="absen-title">
													{{ Rmt::formatTgl($m->created_at) }}<br>
													{{ $m->kategori }}
												</td>
												<td class="absen-title">
													{{ str_limit(strip_tags($m->konten), 60) }}
												</td>
												<td class="text-right action">
													<div class="btn-group" role="group">
														<a href="{{ route('pengumuman_edit',['id' => $m->id]) }}" class="btn btn-default"><i class="fa fa-pencil"></i></a>
														<button type="button" class="btn btn-default" onclick="Delete('{{ route('pengumuman_delete', ['id' => $m->id])}}')"><i class="fa fa-trash-o"></i></button>
													</div>
												</td>
											</tr>
										@endforeach

									</table>

									<div class="text-center">
										{{ $pengumuman->appends(['kat' => Request::get('kat')])->links() }}
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
		$('.menu-pengumuman').addClass('actived');

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

		function changeKategori(value)
		{
			window.location.href="{{ route('pengumuman') }}?kat="+value;
		}
	</script>

@endsection