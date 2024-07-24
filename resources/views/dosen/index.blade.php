@extends('layouts.app')

@section('title','Dosen')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Dosen
					<div class="pull-right">
						<button class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-ekspor"><i class="fa fa-print"></i> EKSPOR</button>
					</div>

				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="125">
									<select class="form-control input-sm" onchange="filter('prodi', this.value)">
										<option value="all">All Prodi</option>
										@foreach( Sia::listProdi() as $pr )
											<option value="{{ $pr->id_prodi }}" {{ Session::get('dosen.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
										@endforeach
									</select>
								</td>
								<td width="150" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('jenis', this.value)">
										<option value="all">All jenis dosen</option>
										@foreach( Sia::jenisDosen() as $jd )
											<option value="{{ $jd }}" {{ Session::get('dosen.jenis') == $jd ? 'selected':'' }}>{{ $jd }}</option>
										@endforeach
										<option value="DTY,DPK" {{ Session::get('dosen.jenis2') == 'DTY,DPK' ? 'selected':'' }}>DTY & DPK</option>
									</select>
								</td>
								<td width="130" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('jabatan', this.value)">
										<option value="all">All Jabatan</option>
										@foreach( Sia::jabatanFungsional() as $key => $val )
											<option value="{{ $key }}" {{ Session::get('dosen.jabatan') == $key ? 'selected':'' }}>{{ $val }}</option>
										@endforeach
									</select>
								</td>
								<td width="100px" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('pendidikan', this.value)">
										<option value="all">All PDK</option>
										<option value="S2" {{ Session::get('dosen.pendidikan') == 'S2' ? 'selected':'' }}>S2</option>
										<option value="S3" {{ Session::get('dosen.pendidikan') == 'S3' ? 'selected':'' }}>S3</option>
									</select>
								</td>
								<td width="160" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('aktivitas', this.value)">
										<option value="all">All Aktivitas</option>
										@foreach( Sia::aktivitasDosen() as $key => $val )
											<option value="{{ $key }}" {{ Session::get('dosen.aktivitas') == $key ? 'selected':'' }}>{{ $val }}</option>
										@endforeach
									</select>
								</td>
								
								<!-- <td width="100px" style="padding-left: 3px">
									<select class="form-control input-sm" onchange="filter('status', this.value)">
										<option value="all">All Status</option>
										<option value="1" {{ Session::get('dosen.status') == '1' ? 'selected':'' }}>Aktif</option>
										<option value="0" {{ Session::get('dosen.status') == '0' ? 'selected':'' }}>Non aktif</option>
									</select>
								</td> -->
								<td>
									@if ( Sia::admin() )
										&nbsp; &nbsp; <button class="btn btn-theme btn-sm"  data-toggle="modal" data-target="#modal-impor" data-backdrop="static" data-keyboard="false"">+ IMPOR</button>
									@endif
								</td>
								<td style="padding-left: 13px">
									@if ( count(Session::get('dosen')) > 0 )
										<span class="tooltip-area">
											<a href="{{ route('dosen_filter') }}?remove=1" class="btn btn-xs btn-warning" title="Reset Filter"><i class="fa fa-filter"></i></a>
										</span>
									@endif
								</td>
								<td width="250px">
									<form action="{{ route('dosen_cari') }}" method="post" id="form-cari">
										<div class="input-group pull-right">
											{{ csrf_field() }}
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('dosen.cari') }}">
											<div class="input-group-btn">
												<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								@if ( Sia::role('admin|akademik|cs|personalia') )
									<td width="110px">
										<a href="{{ route('dosen_add') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
									</td>
								@endif

							</tr>
						</table>
						
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Nama</th>
										<th>Uername</th>
										<th>NIDN</th>
										<th>Jabatan</th>
										<th>Gol.</th>
										<th>Aktivitas</th>
										<th>Jenis</th>
										<th width="50px">Status</th>
										<th>HP</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($dosen as $r)
										<tr>
											<td>{{ $loop->iteration - 1 + $dosen->firstItem() }}</td>
											<td align="left">{{ Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang) }}</td>
											<td align="left">{{ $r->username }}</td>
											<td>{{ $r->nidn }}</td>
											<td>{{ Sia::jabatanFungsional($r->jabatan_fungsional) }}</td>
											<td>{{ $r->golongan }}</td>
											<td>{{ Sia::aktivitasDosen($r->aktivitas) }}</td>
											<td>{{ $r->jenis_dosen }}</td>
											<td><?= Sia::statusDosen($r->aktif) ?></td>
											<td>{{ $r->hp }}</td>
											<td>
												<span class="tooltip-area">
												@if ( Sia::role('admin') )
													<a href="{{ route('dosen_login', ['id_user' => $r->id_user])}}" class="btn btn-primary btn-xs" title="Masuk ke akun dosen"><i class="fa fa-sign-in"></i></a> &nbsp; &nbsp;
												@endif
												@if ( Sia::role('admin|akademik|cs|personalia') )
													<a href="{{ route('dosen_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
													<a href="{{ route('dosen_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
												@endif
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							@if ( $dosen->total() == 0 )
								&nbsp; Tidak ada data
							@endif

							@if ( $dosen->total() > 0 )
								<div class="pull-left">
									Jumlah data : {{ $dosen->total() }}
								</div>
							@endif

							<div class="pull-right"> 
								{{ $dosen->render() }}
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


<div id="modal-ekspor" class="modal fade" style="top:30%" tabindex="-1" data-width="300">
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Ekspor</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<center>
				<a href="{{ route('dosen_excel') }}" class="btn btn-sm btn-primary"><i class="fa fa-file-text"></i> EXCEL</a>&nbsp; 
				<a href="{{ route('dosen_print') }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> CETAK</a>
			</center>
		</div>
		<!-- //modal-body-->
</div>

<div id="modal-impor" class="modal fade" tabindex="-1" style="top:30%">
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Impor Dosen</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<form id="form-dosen" action="{{ route('dosen_impor') }}" enctype="multipart/form-data" method="post">
				{{ csrf_field() }}
				<div class="form-group">
						<label for="fileExcel">Upload File</label>
						<input type="file" id="fileExcel" name="file">
						<p class="help-block">Unggah file excel <b>.xlsx</b></p>
				</div>
				
				<button type="submit" id="btn-submit" class="btn btn-primary btn-sm">IMPOR</button>&nbsp; &nbsp; &nbsp;
				<button type="button" data-dismiss="modal" class="btn btn-sm btn-default pull-right">BATAL</button>

			</form>
		</div>
		<!-- //modal-body-->
</div>

<div id="modal-error" class="modal fade" tabindex="-1" style="top: 30%">
		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Terjadi kesalahan</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<div class="ajax-message"></div>
			<center>
				<button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
			</center>
		</div>
		<!-- //modal-body-->

</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    $(document).ready(function(){
    	$('#nav-mini').trigger('click');

        $('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });
    });

    function filter(modul, value)
    {
        window.location.href = '{{ route('dosen_filter') }}?modul='+modul+'&val='+value;
    }

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> IMPOR');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $("#btn-submit").attr('disabled','');
                $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengimpor...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(data.msg);
                } else {
                		alert('Impor berhasil.. :)')
                    window.location.href='{{ route('dosen') }}';
                }
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
                showMessage(pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('dosen');
</script>
@endsection