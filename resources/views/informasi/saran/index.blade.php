@extends('informasi.layout.main')

@section('title', 'Saran')

@section('contents')

	<div class="content">
		<div class="row title-page">
			<div class="col-md-12">
				<h1>Saran &nbsp;<small>List data saran/kritik</small></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				{{ Rmt::AlertSuccess() }}
				{{ Rmt::AlertError() }}
				{{ Rmt::AlertErrors($errors) }}
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
		
					</div>
					<div class="widget-body">
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="tab-1">

								@if ( $saran->total() > 0 )

									<table class="table lists absen">
										<tr class="first">
											<th>Dari</th>
											<th>Subjek</th>
											<th>Saran</th>
											<th></th>
										</tr>

										@foreach( $saran as $pd )
											<tr>
												<td class="absen-title">
													<?php  $dari = App\Mahasiswa::where('id', $pd->from)->first(); ?>
													{{ !empty($dari) ? $dari->nama : '' }}
												</td>
												<td class="absen-content">
													{{ $pd->subjek }}
												</td>
												<td class="absen-content">
													{{ str_limit(strip_tags($pd->saran),60) }}
												</td>
												<td width="150px" class="text-right action">
													<div class="btn-group" role="group">
														<button type="button" onclick="detailSaran('{{ !empty($dari) ? $dari->nama :'' }}','{{ $pd->subjek }}','{{ $pd->saran }}')" class="btn btn-warning" data-toggle="modal" data-target="#modal-detail-saran"><i class="fa fa-search-plus"></i></button>
														<button type="button" onclick="Delete('{{ route('saran_delete', ['id' => $pd->id]) }}')" class="btn btn-default"><i class="fa fa-trash-o"></i></button>
													</div>
												</td>
											</tr>
										@endforeach

									</table>

									<div class="text-center">
										{{ 	$saran->appends(array_except(Request::query(), 'page'))->links() }}
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

<div id="modal-detail-saran" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title subjek"></h4>
      </div>
      <div class="modal-body pesan">
       
      </div>
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
		$('.menu-saran').addClass('actived');

		function Delete(url)
		{
			$('#modal-delete').modal('show');

			$('#confirm-hapus').attr('href',url);
			$('#confirm-hapus').click(function(){
				$(this).attr('disabled','');
				$(this).html('<i style="width:14.5px" class="fa fa-spinner fa-spin"></i> Hapus');
			});
		}

		function detailSaran(dari, subjek, saran){
			$('.subjek').html(subjek+' ('+dari+')');
			$('.pesan').html(saran);
		}


	</script>

@endsection