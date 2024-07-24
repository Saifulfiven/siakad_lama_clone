@extends('layouts.app')

@section('title','Penerimaan Mahasiswa Baru')

@section('content')
<div id="overlay">
</div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Penerimaan Mahasiswa Baru
					<!-- <div class="pull-right">
						<button class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal-ekspor"><i class="fa fa-print"></i> EKSPOR</button>
					</div> -->

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
											<option value="{{ $pr->id_prodi }}" {{ Session::get('maba.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
										@endforeach
									</select>
								</td>
								<td width="150" style="padding-left: 13px">
									<select class="form-control input-sm" onchange="filter('smt', this.value)">
										<option value="20201" {{ Session::get('maba.smt') == 20201 ? 'selected':'' }}>2020/2021 Ganjil</option>
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('maba.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
										@endforeach
									</select>
								</td>
								<td width="150" style="padding-left: 13px">
									<select class="form-control input-sm" onchange="filter('kelas', this.value)">
										<option value="all">Jenis Kelas: All</option>
										<option value="REGULER" {{ Session::get('maba.kelas') == 'REGULER' ? 'selected':'' }}>REGULER</option>
										<option value="NON-REGULER" {{ Session::get('maba.kelas') == 'NON-REGULER' ? 'selected':'' }}>NON-REGULER</option>
									</select>
								</td>
								<td width="160" style="padding-left: 13px">
									<select class="form-control input-sm" onchange="filter('status', this.value)">
										<option value="all">Status Impor: All</option>
										<option value="SELESAI" {{ Session::get('maba.status') == 'SELESAI' ? 'selected':'' }}>SELESAI</option>
										<option value="BELUM SELESAI" {{ Session::get('maba.status') == 'BELUM SELESAI' ? 'selected':'' }}>BELUM SELESAI</option>
									</select>
								</td>
								<td></td>
								<td width="250px" >
									<form action="{{ route('maba_cari') }}" method="post" id="form-cari">
										<div class="input-group pull-right">
											{{ csrf_field() }}
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('maba.cari') }}">
											<div class="input-group-btn">
												<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								@if ( Sia::role('admin|akademik|cs|personalia') )
								<!-- 	<td width="110px" style="padding-left: 13px">
										<a href="javascript:;" id="btn-submit" class="btn btn-sm btn-primary pull-right"><i class="fa fa-download"></i> IMPOR SEMUA</a>
									</td> -->
								@endif

							</tr>
						</table>
						
						<div class="table-responsive">
							<form action="{{ route('maba_impor_massal') }}" method="post" id="form-impor">
								{{ csrf_field() }}
								<input type="hidden" name="smt" value="{{ Session::get('maba.smt') }}">
								
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>Nama</th>
											<th>Tempat Lahir</th>
											<th>Tgl Lahir</th>
											<th>Kelurahan</th>
											<th>Alamat</th>
											<th>HP</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody>
										@foreach($maba as $r)
											<?php $mhs = DB::table('mahasiswa_reg')
												->where('id_maba', $r->id)->count(); ?>

											<input type="hidden" name="id[]" value="{{ $r->id }}">
											<input type="hidden" name="nama[]" value="{{ $r->nama }}">

											<tr id="row-{{ $r->id }}">
												<td align="center">{{ $loop->iteration - 1 + $maba->firstItem() }}</td>
												<td>{{ $r->nama }}</td>
												<td>{{ $r->tempat_lahir }}</td>
												<td>{{ Carbon::parse($r->tgl_lahir)->format('d/m/Y') }}</td>
												<td>{{ $r->kelurahan }}</td>
												<td>{{ $r->alamat }}</td>
												<td>{{ $r->hp }}</td>
												<td align="center" width="120" id="act-{{ $r->id }}">
													<span class="tooltip-area">
													@if ( Sia::role('admin|akademik|cs|personalia') )
														@if ( $mhs == 0 )
															<a href="javascript:;" onclick="impor('{{ $r->id }}', '{{ Session::get('maba.smt') }}')" id="btn-{{ $r->id }}" class="btn btn-primary btn-xs" title="Import"><i class="fa fa-download"></i></a> &nbsp; &nbsp; 
															<!-- <a href="{{ route('maba_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp;  -->
															<a href="{{ route('maba_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@else
															<a title="Selesai"><i style="color:green" class="fa fa-check"></i></a>
														@endif
													@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

								@if ( $maba->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $maba->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $maba->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $maba->render() }}
								</div>

							</form>

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
			<a href="" class="btn btn-sm btn-primary"><i class="fa fa-file-text"></i> EXCEL</a>&nbsp; 
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
		<form id="form-dosen" action="{{ route('maba_impor') }}" enctype="multipart/form-data" method="post">
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

@if ( Session::has('import') )
	<?php $msg = Session::get('import') ?>

	<div id="modal-pesan" class="modal fade" tabindex="-1" style="top: 30%">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title">Report</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<p><b>Jumlah sukses: </b> {{ count($msg['success']) }}</p>

			@if ( count($msg['errors']) > 0 )
				<p><b>Jumla error: {{ count($msg['errors']) }}</b></p>
				<p>Detail error:</p>
				<p>
					@foreach( $msg['errors'] as $val )
						- {{ $val['nama'] }}: {{ $val['ket'] }}<br>
					@endforeach
				</p>
			@endif

			<center>
				<button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
			</center>
		</div>
		<!-- //modal-body-->
	</div>
@endif

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

        $('#btn-submit').click(function(){
        	if ( !confirm('Anda ingin mengimpor semua calon MaBa pada halaman ini?') ){
        		return false;
        	} else {
        		$('#form-impor').submit();
        	}
        });

        @if ( Session::has('import') )
        	$('#modal-pesan').modal('show');
        	<?php Session::pull('import') ?>
        @endif
    });

    function impor(id, smt)
    {
    	if ( !confirm('Anda ingin memindahkan mahasiswa ini?') ) {
    		return;
    	}

    	var btn = $('#btn-'+id);
    	var row = $('#row-'+id);
    	var act = $('#act-'+id);

    	btn.html('<i class="fa fa-spinner fa-spin"></i>');
    	btn.attr('disabled','');
    	$('#overlay').show();

    	$.ajax({
    		url: '{{ route('maba_impor') }}',
    		data: { id: id, smt },
    		success: function(data){
    			if ( data.error == 1 ) {

    				showMessage(data.msg);
    			
    			} else {
    				act.html('<i style="color: green" class="fa fa-check"></i>');
		        	showSuccess('Berhasil memindahkan mahasiswa');
		        }

    			$('#overlay').hide();
    			btn.removeAttr('disabled');
		        btn.html('<i class="fa fa-download"></i>');
    		},
    		error: function(data,status,msg){
    			var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = msg;
                }
    			showMessage(pesan);
    			btn.removeAttr('disabled');
		        btn.html('<i class="fa fa-download"></i>');
    		}
    	});
    }

    function filter(modul, value)
    {
        window.location.href = '{{ route('maba_filter') }}?modul='+modul+'&val='+value;
    }

    function showMessage(pesan)
    {
    	$('#btn-submit').removeAttr('disabled');
		$('#btn-submit').html('<i class="fa fa-download"></i> IMPOR SEMUA');
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');
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
                    window.location.reload();
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
    submit('impor');
</script>
@endsection