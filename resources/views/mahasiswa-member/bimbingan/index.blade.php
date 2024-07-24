@extends('layouts.app')

@section('title','Bimbingan Skripsi/Tesis')

@section('heading')
	
	<style>
		.content-bimbingan ol {
			list-style-type: decimal;
			margin-left: 10px;
		}
		.content-bimbingan ul {
			list-style: inside !important;
		}
	</style>

@endsection

@section('content')
<div id="overlay"></div>

<div id="content" class="content-bimbingan">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Bimbingan Skripsi/Tesis
				</header>

				<div class="panel-body">

					<?php
						$jabatan = '';

						if ( !empty(@$bimbingan[0]) ) {

							$status_bimbingan = $data_bim->pembimbing_1 + $data_bim->pembimbing_2;

						}
					?>

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<div class="row">
							<div class="col-md-6" style="padding: 0">
								<div class="table-responsive">
									<table border="0" class="table">
										<tr>
											<td width="150"><b>Pilih tahun akademik</b></td>
											<td>
												<select class="form-control input-sm mw-2" onchange="filter('smt', this.value)">
													@foreach( Sia::listSemester() as $sm )
														<option value="{{ $sm->id_smt }}" {{ Session::get('bim.smt') == $sm->id_smt ? 'selected':'' }}>{{ $sm->nm_smt }}</option>
													@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td width="120"><b>Pilih jenis seminar</b></td>
											<td>
												<select class="form-control input-sm mw-2" onchange="filter('jenis', this.value)">
													<option value="P" {{ Session::get('bim.jenis') == 'P' ? 'selected':'' }}>Seminar Proposal</option>
													<option value="H" {{ Session::get('bim.jenis') == 'H' ? 'selected':'' }}>Seminar Hasil</option>
													<option value="S" {{ Session::get('bim.jenis') == 'S' ? 'selected':'' }}>Skripsi/Tesis</option>
												</select>
											</td>
										</tr>
										@if ( !empty(@$bimbingan[0]) )
											<tr>
												<td width="120">Pembimbing I</td>
												<td>: {{ Sia::namaDosen(@$bimbingan[0]->gelar_depan, @$bimbingan[0]->nm_dosen, @$bimbingan[0]->gelar_belakang) }}</td>
											</tr>

											<tr>
												<td>Pembimbing II</td>
												<td>: {{ Sia::namaDosen(@$bimbingan[1]->gelar_depan, @$bimbingan[1]->nm_dosen, @$bimbingan[1]->gelar_belakang) }}</td>
											</tr>
											<tr>
												<td colspan="2">
												<b>Judul</b><br>
												{{ @$bimbingan[0]->judul_tmp }}
												</td>
											</tr>
										@endif
									</table>
								</div>
							</div>

							@if ( !empty(@$bimbingan[0]) )
								<div class="col-md-6" style="margin-bottom: 20px">
									<div class="well {{ empty($data_bim) ? '':'bg-theme-inverse' }}" style="padding: 5px 10px 5px 10px">
										<h3><strong>File</strong> Skripsi/Tesis </h3>

										@if ( empty($data_bim) )
											<p>Belum ada file diupload atau link yang dimasukkan</p>
										@else
											@if ( empty($data_bim->file) && empty($data_bim->link) )
												<p>Belum ada file diupload atau link yang dimasukkan</p>
											@elseif ( empty($data_bim->file) && !empty($data_bim->link) )

												<p>
													<a href="{{ $data_bim->link }}" class="btn btn-primary" target="_blank">
														<i class="fa fa-external-link"></i> Buka Link File
													</a>
												</p>

											@else
												<p>
													<div class="icon-resources">
			                                            <?php $icon = Rmt::icon($data_bim->file); ?>
			                                            <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
			                                        </div>

			                                        <?php $param = ['id' => $data_bim->id,'jenis' => $data_bim->jenis] ?>

													<a href="{{ route('mhs_bim_download', $param) }}" title="Download File" style="color: #fff">
														{{ $data_bim->file }}
													</a>
													<!-- <p><i class="fa fa-clock-o"></i><i> {{ Rmt::Waktulalu($data_bim->updated_at) }}</i></p> -->
												</p>
											@endif
											
										@endif

										@if ( $status_bimbingan != 2 )
											<div class="flip">
												<form action="{{ route('mhs_bim_upload') }}" method="post" enctype="multipart/form-data" id="form-upload-file">
													{{ csrf_field() }}
													<input type="hidden" name="id" value="{{ !empty($data_bim) ? $data_bim->id : '' }}">

													@if ( !empty($data_bim) )
														<hr>
													@endif
													<label for="upload-file" class="btn {{ empty($data_bim) ? 'btn-theme-inverse':'btn-theme' }}" id="btn-upload-file">
														<i class="fa fa-plus"></i> 

														@if ( !empty($data_bim) )
															@if ( !empty($data_bim->file) )
																UPLOAD PERUBAHAN
															@else
																UPLOAD FILE
															@endif
														@else
															UPLOAD FILE
														@endif
													</label>
													 &nbsp; atau &nbsp;
													<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-add-link">
														<i class="fa fa-external-link"></i> 
														@if ( !empty($data_bim) )
															@if ( !empty($data_bim->link) )
																Ganti Link
															@else
																Masukkan Link
															@endif
														@endif
													</button>

													<input type="file" id="upload-file" name="file" style="display: none">
													<div id="nama-file"></div>
												</form>

											</div>
										@endif
									</div>

									<div class="well {{ $status_bimbingan == 2 ? 'bg-success' : 'bg-info' }}" style="padding: 5px 10px 5px 10px">
										<h3>
											<strong>Status</strong> Bimbingan - 
											@if ( $status_bimbingan == 2 )
												<small style="color: #fff"><i class="fa fa-check-square"></i> Selesai</small>
											@else
												<small style="color: #fff"><i class="fa fa-refresh"></i> Dalam Proses</small>
											@endif
										</h3>
										<p>
											Status Pembimbing I&nbsp; : 
											<?= $data_bim->pembimbing_1 == '1' ? '<b><i class="fa fa-check"></i> Selesai</b>' : '<b>Belum selesai</b>' ?><br>
											Status Pembimbing II : 
											<?= $data_bim->pembimbing_2 == '1' ? '<b><i class="fa fa-check"></i> Selesai</b>' : '<b>Belum selesai</b>' ?>
										</p>

									</div>
								</div>
							@endif

						</div>

						@if ( !empty(@$bimbingan[0]) )

							<p><b>RIWAYAT BIMBINGAN "{{ Rmt::jnsSeminar(Session::get('bim.jenis')) }}"</b></p>
							
							@if ( !empty($data_bim) )

								<?php 
									
									$riwayat_1 = App\Bimbingandetail::where('id_bimbingan_mhs', $data_bim->id)
												->where('jabatan_pembimbing', @$bimbingan[0]->jabatan)
												->get(); 

									$riwayat_2 = App\Bimbingandetail::where('id_bimbingan_mhs', $data_bim->id)
												->where('jabatan_pembimbing', @$bimbingan[1]->jabatan)
												->get(); 

								?>
							
								<div class="tabbable">
				                    <ul class="nav nav-tabs" data-provide="tabdrop">
			                        	<li class="active">
			                        		<a href="#pbb1" data-toggle="tab">Pembimbing I</a>
			                        	</li>
			                        	<li>
			                        		<a href="#pbb2" data-toggle="tab">Pembimbing II</a>
				                    	</li>
				                    </ul>
				                    <div class="tab-content">
				                    
				                        <div class="tab-pane fade in active" id="pbb1">
				                        	@if ( count($riwayat_1) == 0 )
												<div class="alert alert-warning">
													Belum ada data, tunggu hingga dosen pembimbing mengomentarinya.
												</div>
											@else
												
												<a href="{{ route('mhs_bim_cetak', ['id' => $data_bim->id]) }}?jb=KETUA&dsn={{ $bimbingan[0]->id_dosen }}&jdl={{ @$bimbingan[0]->judul_tmp }}" target="_blank" class="btn btn-primary pull-right"><i class="fa fa-print"></i> Cetak Pembimbing I</a>
					                        	
					                        	<div class="table-responsive">
													<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
														<thead class="custom">
															<tr>
																<th width="20px">No.</th>
																<th>Tanggal</th>
																<th>Sub pokok bahasan
																<th>Komentar/Saran</th>
																<th width="150">Lampiran</th>
															</tr>
														</thead>
														<tbody align="center">
															@foreach( $riwayat_1 as $r1 )
																<tr>
																	<td>{{ $loop->iteration }}</td>
																	<td>{{ Carbon::parse($r1->tgl_bimbingan)->format('d/m/Y') }}</td>
																	<td align="left">{{ $r1->sub_bahasan }}</td>
																	<td align="left">{!! $r1->komentar !!}</td>
																	<td>
																		@if ( !empty($r1->file) )
																			<?php $param = ['id' => $r1->id, 'id_bim' => $r1->id_bimbingan_mhs, 'jenis' => @$bimbingan[0]->jenis]; ?>
																			<a href="{{ route('mhs_bim_lampiran', $param) }}">Buka lampiran</a>
																		@else
																			-
																		@endif
																	</td>
																</tr>
															@endforeach
														</tbody>
													</table>
												</div>
											@endif

				                        </div>

				                        <div class="tab-pane fade" id="pbb2">
				                        	@if ( count($riwayat_2) == 0 )
												<div class="alert alert-warning">
													Belum ada data, tunggu hingga dosen pembimbing mengomentarinya.
												</div>
											@else

												<a href="{{ route('mhs_bim_cetak', ['id' => $data_bim->id]) }}?jb=SEKRETARIS&dsn={{ $bimbingan[1]->id_dosen }}&jdl={{ @$bimbingan[1]->judul_tmp }}" target="_blank" class="btn btn-primary pull-right"><i class="fa fa-print"></i> Cetak Pembimbing II</a>

					                        	<div class="table-responsive">
													<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
														<thead class="custom">
															<tr>
																<th width="20px">No.</th>
																<th>Tanggal</th>
																<th>Sub pokok bahasan
																<th>Komentar/Saran</th>
																<th width="150">Lampiran</th>
															</tr>
														</thead>
														<tbody align="center">
															@foreach( $riwayat_2 as $r2 )
																<tr>
																	<td>{{ $loop->iteration }}</td>
																	<td>{{ Carbon::parse($r2->tgl_bimbingan)->format('d/m/Y') }}</td>
																	<td align="left">{{ $r2->sub_bahasan }}</td>
																	<td align="left">{!! $r2->komentar !!}</td>
																	<td>
																		@if ( !empty($r2->file) )
																			<?php $param = ['id' => $r2->id, 'id_bim' => $r2->id_bimbingan_mhs, 'jenis' => @$bimbingan[0]->jenis]; ?>
																			<a href="{{ route('mhs_bim_lampiran', $param) }}">Buka lampiran</a>
																		@else
																			-
																		@endif
																	</td>
																</tr>
															@endforeach
														</tbody>
													</table>

												</div>
											@endif

				                        </div>

				                    </div>
				                </div>

			                @else

			                	<p>Belum ada riwayat bimbingan, silahkan upload file skripsi/tesis anda.</p>

							@endif

						@else

							<hr>
							<p>Belum ada data, jika anda memprogram skripsi pada semester ini, tunggu hingga bagian akademik menentukan pembimbing anda.</p>

						@endif
						
					</div>
				</div>
			</section>
		</div>
		
	</div>
	<!-- //content > row-->
		
</div>
<!-- //content-->

<div id="modal-add-link" class="modal fade" tabindex="-1" style="top:30%">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
		<h4 class="modal-title">Masukkan Link Skripsi/Tesis</h4>
	</div>
	<!-- //modal-header-->
	<div class="modal-body">
		<form id="form-add-link" action="{{ route('mhs_bim_store_link') }}" method="post">
			{{ csrf_field() }}

			<input type="hidden" name="id" value="{{ !empty($data_bim) ? $data_bim->id : '' }}">

			<div class="form-group">
				<label for="fileExcel">Masukkan Link</label>
				<input type="text" name="link" class="form-control" value="{{ !empty($data_bim) ? $data_bim->link : '' }}">
				<p class="help-block">Link Google Drive, Dropbox, dll</p>
			</div>
			
			<button type="submit" id="btn-submit-add-link" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Simpan</button>&nbsp; &nbsp; &nbsp;
			<button type="button" data-dismiss="modal" class="btn btn-sm btn-default pull-right">BATAL</button>

		</form>
	</div>
	<!-- //modal-body-->
</div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    $(document).ready(function(){

        $('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        });

        $('#upload-file').change(function(){
        	$('#form-upload-file').submit();
        });
    });

    function filter(modul, value)
    {
        window.location.href = '{{ route('mhs_bim_filter') }}?modul='+modul+'&val='+value;
    }

    function submit(modul)
    {

    	@if ( !empty($data_bim) )
			@if ( !empty($data_bim->file) )
				var btn = 'UPLOAD PERUBAHAN';
			@else
				var btn = 'UPLOAD FILE';
			@endif
		@else
			var btn = 'UPLOAD FILE';
		@endif

        var options = {
            beforeSend: function() 
            {
            	let filename = $('input[type=file]').val().split('\\').pop();
            	$('#nama-file').html(filename);
            	$('body').modalmanager('loading');
                $("#btn-upload-file").attr('disabled','');
                $("#btn-upload-file").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengupload...");
            },
            success:function(data, status, message) {
                window.location.reload();
            },
            error: function(data, status, message)
            {
            	$('#nama-file').html('');
            	$('#btn-upload-file').html('<i class="fa fa-plus"></i> '+btn);
            	$('#btn-upload-file').removeAttr('disabled');

                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }

    function submitLink(modul)
    {

        var options = {
            beforeSend: function() 
            {
            	$('body').modalmanager('loading');
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                window.location.reload();
            },
            error: function(data, status, message)
            {
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }

    submit('upload-file');
    submitLink('add-link');
</script>
@endsection