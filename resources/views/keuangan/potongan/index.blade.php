@extends('layouts.app')

@section('title','Potongan Biaya Kuliah')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Potongan Biaya Kuliah
						<a href="javascript:;" data-toggle="modal" data-target="#modal-impor" class="btn btn-theme btn-xs pull-right" data-backdrop="static"><i class="fa fa-upload"></i> Impor Potongan Pembayaran</a>
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}

							@if ( Session::has('errors_impor') )
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert">
										<span aria-hidden="true">&times;</span>
										<span class="sr-only">Close</span>
									</button>
									<b>DETAIL KESALAHAN..!</b><br>
									<?php $no = 1 ?>
									@foreach( Session::get('errors_impor') as $er )
										{{ $no++ }}. {{ $er }}<br>
									@endforeach
								</div>
							@endif

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="180">
										<select class="form-custom mw-2" name="smt" onchange="filter('smt', this.value)">
												<option value="all">All smt masuk</option>
											@foreach( $semester as $sm )
						                    	<option value="{{ $sm->id_smt }}" {{ Session::get('pot_smt_masuk') == $sm->id_smt ? 'selected' : '' }}>{{ $sm->nm_smt }}</option>
						                    @endforeach
										</select>
									</td>
									<td width="160">
										<select class="form-custom mw-2" name="prodi" onchange="filter('prodi', this.value)" id="list-prodi">
											<option value="all">Semua Prodi</option>
											@foreach( Sia::listProdi() as $pr )
						                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('pot_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
						                    @endforeach
										</select>
									</td>

				                	<td>
				                		<select name="jenis_potongan" onchange="filter('jenis', this.value)" class="form-custom">
				                			<option value="all">Semua Jenis</option>
				                			@foreach( sia::jenisPotongan() as $jp )
				                				<option value="{{ $jp }}" {{ Session::get('pot_jenis') == $jp ? 'selected':'' }}>{{ $jp }}</option>
				                			@endforeach
				                		</select>
				                	</td>

									<td>
										<a href="{{ route('pot_cetak') }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-print"></i> CETAK</a>
									</td>

									<td width="300px">
										<form action="" id="form-cari">
											<div class="input-group pull-right">
												<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
												<div class="input-group-btn">
													<a href="{{ route('pot') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
													<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>

									@if ( Sia::keuangan() )
										<td width="110px">
											<a href="javascript:;" data-toggle="modal" data-target="#modal-tambah" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
										</td>
									@endif

								</tr>
							</table>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>NIM</th>
											<th>Nama</th>
											<th>Prodi</th>
											<th>Jenis Potongan</th>
											<th>Jumlah Potongan</th>
											<th>Ket</th>
											<th width="80px">Tools</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach( $mahasiswa as $r )
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td align="left">{{ $r->jenjang.' '.$r->nm_prodi }}</td>
												<td>{{ $r->jenis_potongan }}</td>
												<td align="left">{{ 'Rp '. Rmt::rupiah($r->potongan) }}</td>
												<td align="left">{{ $r->ket }}</td>
												<td>
													<span class="tooltip-area">
														<a href="javascript::void()" onclick="ubah('{{ $r->id_mhs_reg }}')" class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a>

														<a href="{{ route('pot_delete', ['id' => $r->id_mhs_reg]) }}" onclick="return confirm('Anda ingin menghapus data ini ?')" class="btn btn-danger btn-xs ubah" title="Hapus"><i class="fa fa-trash-o"></i></a>
													</span>
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

	<div id="modal-tambah" class="modal fade" data-width="550" tabindex="-1" style="top: 40% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Tambah Potongan Biaya Kuliah</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="col-md-12">
		        <form action="{{ route('pot_store') }}" id="form-biaya" method="post">
		        	{{ csrf_field() }}
	                <table class="table" width="100%" border="0">
		                <tr>
		                    <td style="padding: 10px 0">Mahasiswa</td>
		                    <td><div style="position: relative;">
			                        <div class="input-icon right"> 
			                            <span id="spinner-autocomplete-mhs" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
			                            <input type="text" class="form-control" required="" id="autocomplete-mhs">
			                            <input type="hidden" id="id-mhs" name="mahasiswa">
			                        </div>
			                    </div>
	                		</td>
		                </tr>
		                <tr>
		                	<td>Jenis Potongan</td>
		                	<td>
		                		<select name="jenis_potongan" class="form-control">
		                			@foreach( sia::jenisPotongan() as $jp )
		                				<option value="{{ $jp }}">{{ $jp }}</option>
		                			@endforeach
		                		</select>
		                	</td>
		                </tr>
		                <tr>
		                    <td>Jumlah Potongan</td>
		                    <td><input type="text" name="potongan" class="form-control"></td>
		                </tr>
		                <tr>
		            		<td>Keterangan</td>
		            		<td><textarea name="ket" class="form-control"></textarea></td>
		            	</tr>
		            </table>

		            <hr>
		            <button type="submit" id="btn-submit-biaya" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
		        	<br>
		        	<br>
		        </form>
		    </div>
	    </div>
	</div>

	<div id="modal-edit" class="modal fade" data-width="500" tabindex="-1" style="top: 40% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Ubah Potongan Biaya Kuliah</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="col-md-12">
		        <form action="{{ route('pot_update') }}" id="form-edit" method="post">

		        	<div id="data-edit">
		        		<center><br><br><br><i class="fa fa-spinner fa-spin"></i><br><br><br></center>
		        	</div>

		            <hr>
		            <button type="submit" id="btn-submit-edit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
		        	<br>
		        	<br>
		        </form>
		    </div>
	    </div>
	</div>

	<div id="modal-impor" class="modal fade" tabindex="-1" style="top:30%">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title">Impor Potongan Pembayaran</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<form id="form-impor" action="{{ route('pot_impor') }}" enctype="multipart/form-data" method="post">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="fileExcel">Upload File</label>
					<input type="file" class="form-control" id="fileExcel" name="file">
					<p class="help-block">Unggah file excel <b>.xlsx</b></p>
				</div>
				
				<button type="submit" id="btn-submit-impor" class="btn btn-theme btn-sm">IMPOR</button>&nbsp; &nbsp; &nbsp;
				<a href="{{ url('storage') }}/contoh-data/contoh format impor potongan bayar.xlsx"  target="_blank" class="btn btn-sm btn-primary pull-right">LIHAT CONTOH DATA</a>

			</form>
		</div>
		<!-- //modal-body-->
	</div>

	<div id="modal-error" class="modal fade" tabindex="-1" style="top: 40% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
	        <h4 class="modal-title">Terjadi kesalahan</h4>
	    </div>
	    <!-- //modal-header-->
	    <div class="modal-body">
	        <div class="ajax-message"></div>
	        <hr>
	        <center>
	            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
	        </center>
	    </div>
	    <!-- //modal-body-->
	</div>

@endsection

@section('registerscript')

<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>

<script>
    $(function(){

        $('#autocomplete-mhs').autocomplete({
            serviceUrl: '{{ route('pot_mhs') }}?smtmasuk={{ Session::get('pot_smt_masuk') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-mhs').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-mhs').hide();
            },
            onSelect: function(suggestion) {
                $('#id-mhs').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $(document).on( "keyup", 'input[name="potongan"]', function( event ) {

            var selection = window.getSelection().toString();
            if ( selection !== '' ) {
                return;
            }
            
            if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
                return;
            }
            
            
            var $this = $( this );
            
            var input = $this.val();

            var input = input.replace(/[\D\s\._\-]+/g, "");
            input = input ? parseInt( input, 10 ) : 0;

            $this.val( function() {
                return ( input === 0 ) ? "" : input.toLocaleString();
            } );
        });

	 });
	            	
	
    function showMessage(modul,pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit-'+modul).removeAttr('disabled');
        $('#btn-submit-'+modul).html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
            	$('#overlay').hide();

                if ( data.error == 1 ) {
                    showMessage(modul, data.msg);
                } else {
                	if ( data.msg == '' ) {
                    	window.location.reload();
                    } else {
                    	getPembayaran(data.id_mhs_reg, data.smt_mulai);
                    	$('#btn-submit-'+modul).removeAttr('disabled');
        				$('#btn-submit-'+modul).html('<i class="fa fa-floppy-o"></i> SIMPAN');
        				$('#modal-edit').modal('hide');
        				$.notific8('Berhasil mengubah data',{ life:5000,horizontalEdge:"bottom", theme:"primary" ,heading:" Pesan "});
                    }
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
                showMessage(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('biaya');
    submit('edit');
    submit('impor');

	function ubah(id_mhs_reg)
	{
		$('#data-edit').html('<center><br><br><br><i class="fa fa-spinner fa-spin"></i><br><br><br></center>');
		$('#modal-edit').modal('show');
    	$.ajax({
    		url: '{{ route('pot_edit') }}',
    		data: { id_mhs_reg: id_mhs_reg },
    		success: function(result){
    			$('#data-edit').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
	}

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }
</script>
@endsection