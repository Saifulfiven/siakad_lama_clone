@extends('layouts.app')

@section('title','Validasi Pengambilan Ijazah')

@section('content')

<?php $level = Auth::user()->level ?>

<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Validasi Pengambilan Ijazah - 
					<strong>
						@if ( $level == 'jurusan' )
							Validasi Skripsi/Tesis
						@elseif ( $level == 'pustakawan' )
							Validasi Bebas Pustaka
						@elseif ( $level == 'keuangan' )
							Validasi Bebas Pembayaran
						@endif
					</strong>
				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<div class="table-responsive">
							<table border="0" width="100%" style="min-width: 550px;margin-bottom: 10px">
								<tr>
									<td width="200">
										<select class="form-control input-sm" onchange="filter('prodi', this.value)">
				                            <option value="all">Semua Prodi</option>
				                            @foreach( Sia::listProdi() as $pr )
				                                <option value="{{ $pr->id_prodi }}" {{ Session::get('ijazah.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
				                            @endforeach
				                        </select>
				                    </td>

									<td width="150">
										<select class="form-control input-sm" onchange="filter('status', this.value)">
				                            <option value="all" {{ Session::get('ijazah.status') == 'all' ? 'selected':'' }}>Semua Status</option>
				                            <option value="0" {{ Session::get('ijazah.status') == '0' ? 'selected':'' }}>Belum disetujui</option>
				                            <option value="1" {{ Session::get('ijazah.status') == '1' ? 'selected':'' }}>Disetujui</option>
				                        </select>
									</td>
									<td></td>
									<td width="250px">
										<form action="" method="get" id="form-cari">
				                            <div class="input-group pull-right">
				                                <input type="hidden" name="pencarian" value="1">
				                                <input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('ijazah.cari') }}">
				                                <div class="input-group-btn">
				                                    @if ( Session::has('ijazah.cari') )
				                                        <button class="btn btn-danger btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
				                                    @endif
				                                    <button class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
				                                </div>
				                            </div>
				                        </form>
									</td>
								</tr>
							</table>

							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Mahasiswa</th>
										<th>Prodi</th>
										@if ( $level == 'jurusan' )
											<th>File Skripsi</th>
											<th>File Turnitin</th>
										@endif

										<th>Status</th>
										<th width="100">Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach( $mahasiswa as $mhs )
										<tr>
											<td>{{ $loop->iteration }}</td>
											<td align="left">{{ $mhs->nim }} - {{ $mhs->nm_mhs }}</td>
											<td>{{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>

											@if ( $level == 'jurusan' )

												<?php $status = $mhs->bebas_skripsi; ?>

												<td>
													@if ( !empty($mhs->skripsi) )
														<a href="{{ route('val_ijazah_download', ['mhs' => $mhs->id_mhs_reg, 'file' => $mhs->skripsi]) }}" title="Download File" class="btn btn-primary btn-xs">
			                                                <i class="fa fa-download"></i> File Skripsi
			                                            </a>
		                                            @else
		                                            	Belum diupload
		                                            @endif
												</td>
												<td>
													@if ( !empty($mhs->turnitin) )
														<a href="{{ route('val_ijazah_download', ['mhs' => $mhs->id_mhs_reg, 'file' => $mhs->turnitin]) }}" title="Download File" class="btn btn-primary btn-xs">
			                                                <i class="fa fa-download"></i> File Turnitin
			                                            </a>
		                                            @else
		                                            	Belum diupload
		                                            @endif
												</td>

											@elseif ( $level == 'pustakawan' )

												<?php $status = $mhs->bebas_pustaka; ?>

											@elseif ( $level == 'keuangan' )

												<?php $status = $mhs->bebas_pembayaran; ?>

											@endif

											<td>{{ Rmt::status3($status) }}</td>

											<td>
												@if ( $status == 0 )
													<a href="{{ route('val_ijazah_update', ['mhs' => $mhs->id_mhs_reg, 'val' => 1]) }}"
														class="btn btn-success btn-xs"
														onclick="return confirm('Anda ingin menyetujui mahasiswa ini?')">Setujui</a>&nbsp; 

												@else

													<a href="{{ route('val_ijazah_update', ['mhs' => $mhs->id_mhs_reg, 'val' => 0]) }}"
													class="btn btn-danger btn-xs"
													onclick="return confirm('Anda ingin menolak ajuan mahasiswa ini?')">Tolak</a>&nbsp; 

												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>

							@if ( $mahasiswa->total() == 0 )
								&nbsp; Tidak ada data
							@endif

							@if ( $mahasiswa->total() > 0 )
								<div class="pull-left">
									Jumlah data : {{ $mahasiswa->total() }}
								</div>
							@endif

							<div class="pull-right"> 
								{{ $mahasiswa->render() }}
							</div>

						</div>
						
					</div>
				</div>
			</section>
		</div>
		
	</div>
	<!-- //content > row-->
		
</div>
<!-- //content-->

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
        window.location.href = '{{ route('session_filter') }}?key=ijazah&modul='+modul+'&val='+value;
    }

    function submit(modul)
    {
    	var btn = '<?= !empty($data_bim) ? 'UPLOAD PERUBAHAN':'UPLOAD FILE' ?>';

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
                showMessage2('pelatihan', pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }

    submit('upload-file');
</script>
@endsection