<?php
	
	$files = DB::table('seminar_file')
					->where('id_seminar', $seminar->id)
					->get();

	$seminar_file = [];

	foreach( $files as $val ) {
		$seminar_file[] = [
			'id' => $val->id,
			'jenis' => $val->jenis_file,
			'file' => $val->file,
			'ket' => $val->ket
		];
	}

?>

<div class="col-md-12" style="margin-bottom: 20px">
	
	<p><b>Upload Dokumen Pendukung</b></p>

	<div class="table-responsive">
		<table class="table table-bordered" style="min-width: 400px">
			<thead class="custom">
				<tr>
					<th width="200">Jenis Dokumen</th>
					<th width="250">File</th>
					<th width="160">Aksi</th>
				</tr>
			</thead>

			<tbody>

				@if ( !empty($seminar_file) )
					<tr>
						<td><b>PEMBAYARAN</b></td>
						<td align="center">
							@foreach( $seminar_file as $sf )
								
								@if ( $sf['jenis'] == 'pembayaran' )
									@if ( !empty($sf['file']) )
									<a href="{{ config('app.url-file-seminar') }}/{{ Sia::sessionMhs('nim') }}/{{ $sf['file'] }}" title="Lihat" target="_blank">
										<i class="fa fa-picture-o fa-2x"></i>
									</a>
									@else
										<label class="bg-danger">Belum ada file</label>
									@endif
								@endif

							@endforeach
						</td>
						<td align="center">
							<button class="btn btn-theme btn-xs" onclick="jenisFile('pembayaran')">
								<i class="fa fa-plus"></i> Ganti File
							</button>
						</td>
					</tr>
				@else
					<tr>
						<td colspan="3" align="center">
							<button class="btn btn-theme" onclick="jenisFile('pembayaran')">
								<i class="fa fa-plus"></i> Upload Bukti Pembayaran
							</button>
						</td>
					</tr>
				@endif

				@if ( $jenis == 'H' )
					<tr>
						<td colspan="4">
							<br>
							
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<p><b>File Olah data atau Validasi untuk NDC</b></p>
							<table class="table table-bordered table-hover">
								<tr>
									<th width="40">No</th>
									<th width="150">File</th>
									<th>Keterangan</th>
									<th width="200">Aksi</th>
								</tr>
								
								<?php $no = 1 ?>
								<?php $dt = 0 ?>

								@foreach( $seminar_file as $sf )

									@if ( $sf['jenis'] == 'olah-data' )

										<tr>
											<td align="center">{{ $no++ }}</td>
											<td align="center">
												@if ( !empty($sf['file']) )
													<a href="{{ config('app.url-file-seminar') }}/{{ Sia::sessionMhs('nim') }}/{{ $sf['file'] }}" title="Lihat" target="_blank">
														<i class="fa fa-picture-o fa-2x"></i>
													</a>
												@else
													-
												@endif
											</td>
											<td>{{ $sf['ket'] }}</td>
											<td align="center">
												<button class="btn btn-danger btn-xs" onclick="tombolHapus('{{ $sf['id'] }}', 'Anda ingin menghapus data ini?')">
													<i class="fa fa-times"></i> Hapus
												</button>
												<form id="delete-form-{{ $sf['id'] }}" action="{{ route('mhs_seminar_delete_file') }}" method="POST" style="display: none;">
													<input type="hidden" name="id" value="{{ $sf['id'] }}">
								                    {{ csrf_field() }}
								                </form>
											</td>
										</tr>

									@endif

								@endforeach

								@if ( $no == 1 )
									<tr>
										<td colspan="4">Belum ada data</td>
									</tr>
								@endif
								
							</table>
							<br>
							<button class="btn btn-theme" onclick="jenisFile('olah-data')">
								<i class="fa fa-plus"></i> Upload File Olah Data/Validasi
							</button>
						</td>
					</tr>
					<tr>
						<td align="center" colspan="4">
							
						</td>
					</tr>
				@endif
			</tbody>
		</table>
	</div>
</div>

<div id="modal-upload" class="modal fade" tabindex="-1" style="top:30%">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
		<h4 class="modal-title">Upload file <span id="jns-file"></span></h4>
	</div>
	<!-- //modal-header-->
	<form action="{{ route('mhs_seminar_store') }}" method="post" enctype="multipart/form-data" id="form-upload-file">
		<div class="modal-body">
			{{ csrf_field() }}
			<input type="hidden" name="id" value="{{ $seminar->id }}">
			<input type="hidden" name="jenis_file" id="jenis-file">
			<input type="file" id="field-upload-file" name="file">


			<div class="form-group" id="keterangan">
				<hr>
				<label>Keterangan (Optional)</label>
				<input type="text" name="ket" class="form-control">
			</div>
		</div>

		<div class="modal-footer">
			<button class="btn btn-theme" id="btn-submit-upload-file">
				<i class="fa fa-save"></i> Simpan
			</button>
		</div>
	</form>
	<!-- //modal-body-->
</div>