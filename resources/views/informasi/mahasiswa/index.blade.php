@extends('informasi.layout.main')

@section('title', 'Mahasiswa')

@section('contents')

	<div class="content">
		<div class="row title-page">
			<div class="col-md-12">
				<h1>Data Mahasiswa &nbsp;<small>Laporan data mahasiswa</small></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				{{ Rmt::AlertSuccess() }}
				{{ Rmt::AlertError() }}
				{{ Rmt::AlertErrors($errors) }}
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title">
							<div class="widget-option" style="margin-top: -10px">
								<a href="#" id="add-account" class="btn btn-info btn-xs"><i class="fa fa-upload"></i> Import Mahasiswa</a></div>
						</h2>
					</div>
					<div class="widget-body">
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="tab-1">

								@if ( $mahasiswa->total() > 0 )

									<table class="table table-hover">
										<tr class="first">
											<th>Nomor</th>
											<th>Username</th>
											<th>NIM</th>
											<th>Nama</th>
											<th></th>
										</tr>

										@foreach( $mahasiswa as $pd )
											<tr>
												<td class="absen-title">
													{{ $pd->nomor }}
												</td>
												<td class="absen-title">
													{{ $pd->username }}
												</td>
												<td class="absen-content">
													{{ $pd->nim }}
												</td>
												<td class="absen-content">
													{{ $pd->nama }}
												</td>
												<td width="150px" class="text-right action">
													<div class="btn-group" role="group">
														<a href="{{ route('mahasiswa_edit',['id' => $pd->id]) }}" class="btn btn-warning"><i class="fa fa-pencil"></i></a>
														<button type="button" onclick="Delete('{{ route('mahasiswa_delete', ['id' => $pd->id]) }}')" class="btn btn-default"><i class="fa fa-trash-o"></i></button>
													</div>
												</td>
											</tr>
										@endforeach

									</table>

									<div class="text-center">
										{{ 	$mahasiswa->appends(array_except(Request::query(), 'page'))->links() }}
									</div>

								@else
									<div class="text-center">
										Belum ada data
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

	<div class="modal fade modal-custom modal-small" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="{{ route('mahasiswa_store') }}" enctype="multipart/form-data" method="post" id="form-import" class="form-custom tab-content">
					<div class="modal-header modal-header-danger">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel"><i class="fa fa-tag"></i>&nbsp; Import Data Mahasiswa</h4>
					</div>
					<div class="modal-body">

						{{ csrf_field() }}
						<div class="row tab-pane active" role="tabpanel" id="new-account">
							<input type="file" required name="file" class="form-control">
						</div>
						
					</div>

					<div class="modal-footer">
						<a href="{{ url('storage') }}/data/Format data mahasiswa.xlsx" download class="btn btn-info pull-left"><i class="fa fa-download"></i> Download Format</a>
						<button type="button" class="btn btn-cancel" data-dismiss="modal">Batal</button>
						<button class="btn btn-info import"><i class="fa fa-save"></i>&nbsp; Import</a>
					</div>
				</form>
			</div>
		</div>
	</div>

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
		$('.menu-mahasiswa').addClass('actived');

		$('#add-account').click(function(){
			$('#modal-add').modal('show');
		});

		$('#form-import').on('submit',function(){
			$(this).find('.import').attr('disabled','');
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