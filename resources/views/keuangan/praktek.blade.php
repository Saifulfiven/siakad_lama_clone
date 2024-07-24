@extends('layouts.app')

@section('title','Pembayaran Kuliah Praktek')

@section('topMenu')
	@include('keuangan.top-menu')
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Pembayaran Kuliah Praktek & Wisuda Tahun Akademik 
						<select onchange="filter('smt', this.value)" id="filter-smt">
							@foreach( $semester as $smt )
								<option value="{{ $smt->id_smt }}" {{ Session::get('pr_smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
							@endforeach
						</select>
						<div class="pull-right">
							<a href="javascript:;" data-target="#modal-print" data-toggle="modal" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Cetak History</a>
							<a href="javascript:;" id="link-cetak-langsung" class="btn btn-theme-inverse btn-xs"><i class="fa fa-download"></i> Cetak berdasarkan filter</a>
						</div>
					</header>

					<div class="panel-body">

						<div class="col-md-2" style="padding-left: 0">

						</div>

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="180">
										<select class="form-custom mw-2" name="filter_jenis_bayar" onchange="filter('jnsbayar', this.value)">
											<option value="all">Semua Jenis</option>
											@foreach( Sia::listPembayaran() as $pb )
						                    	<option value="{{ $pb->id_jns_pembayaran }}" {{ Session::get('pr_jenis_bayar') == $pb->id_jns_pembayaran ? 'selected' : '' }}>{{ $pb->ket }}</option>
						                    @endforeach
										</select>
									</td>
									<td width="215">
										<select class="form-custom mw-2" onchange="filter('prodi', this.value)" id="list-prodi">
											<option value="all">Semua Prodi</option>
											@foreach( Sia::listProdi() as $pr )
						                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('pr_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
						                    @endforeach
										</select>
									</td>
									<td></td>
									<td width="300px">
										<form action="" id="form-cari">
											<div class="input-group pull-right">
												<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('pr_cari') }}">
												<div class="input-group-btn">
													<a href="{{ route('keu_praktek') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
													<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>
									<td width="85">
										<button class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#modal-bayar">Tambah</button>
									</td>

								</tr>
							</table>
							
							<!-- <div class="table-responsive"> -->
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" data-provide="data-table">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>NIM</th>
											<th>Nama</th>
											<th>Prodi</th>
											<th>Jenis</th>
											<th>Tgl Bayar</th>
											<th>Jumlah Dibayar</th>
											<th width="80">Aksi</th>
										</tr>
									</thead>
									<tbody align="center">
										<?php $no = 1 ?>
										@foreach($mahasiswa as $r)
											<tr>
												<td>{{ $no++ }}</td>
												<td align="left">{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td align="left">{{ $r->jenjang .' '. $r->nm_prodi }}</td>
												<td>{{ $r->ket }}</td>
												<td>{{ !empty($r->tgl_bayar) ? Carbon::parse($r->tgl_bayar)->format('d/m/Y') : '-' }}</td>
												<td align="right">{{ empty($r->jml_bayar) ? '-' : Rmt::rupiah($r->jml_bayar) }}</td>
												<td>
													<span class="tooltip-area">
														<a href="{{ route('keu_detail_praktek', ['id' => $r->id_mhs_reg]) }}?smt={{ Session::get('pr_smt') }}&jenisbayar={{ $r->id_jns_pembayaran }}" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i></a> &nbsp;

														<button class="btn btn-danger btn-xs" onclick="tombolHapus('{{ $r->id }}', 'Anda ingin menghapus pembayaran ini?')" title="Hapus">
															<i class="fa fa-times"></i>
														</button>
														<form id="delete-form-{{ $r->id }}" action="{{ route('keu_praktek_delete') }}" method="POST" style="display: none;">
															<input type="hidden" name="id" value="{{ $r->id }}">
										                    {{ csrf_field() }}
										                </form>

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
									{{ $mahasiswa->appends(Request::query())->links() }}
								</div>

							<!-- </div> -->
						</div>
					</div>
				</section>
			</div>
			
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

	<div id="modal-bayar" class="modal fade" tabindex="-1">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Input Pembayaran</h4>
	    </div>
	    <div class="modal-body">

	        <form action="{{ route('keu_praktek_store') }}" id="form-bayar" method="post">
	            {{ csrf_field() }}

	            <table class="table" width="100%" border="0">
	            	<tr>
	            		<td style="padding: 10px 0">Semester</td>
	            		<td class="ta"></td>
	            	</tr>
	            	<tr>
	            		<td style="padding: 10px 0">Mahasiswa <span>*</span></td>
	            		<td>
	            			<div style="position: relative">
                                <div class="input-icon right"> 
                                    <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                    <input type="text" id="autocomplete-ajax" class="form-control">
                                </div>
                                <input type="hidden" name="mahasiswa" id="id-mahasiswa">
                            </div>
	            		</td>
	            	</tr>
	            	<tr>
	            		<td style="padding: 10px 0">Jenis Pembayaran <span>*</span></td>
	            		<td>
	            			<select class="form-custom mw-2" name="jenis_bayar">
								@foreach( Sia::listPembayaran() as $pb )
			                    	<option value="{{ $pb->id_jns_pembayaran }}">{{ $pb->ket }}</option>
			                    @endforeach
							</select>
	            		</td>
	            	</tr>
	            	<tr>
	            		<td>Jumlah Bayar <span>*</span></td>
	            		<td>
	            			<input type="text" name="jml_bayar" class="form-control">
	            		</td>
	            	</tr>
	            	<tr>
	            		<td>Tanggal Bayar <span>*</span></td>
	            		<td><input type="date" name="tgl_bayar" class="form-control mw-2"></td>
	            	</tr>
	            	<tr>
	            		<td>Tempat Bayar</td>
	            		<td>
	            			<select name="tempat_bayar" class="form-control" id="jenis-bayar">
	            				<option value="BANK">BANK</option>
	            				<option value="LAINNYA">LAINNYA</option>
	            			</select>
	            		</td>
	            	</tr>
	            	<tr>
	            		<td>Nama Bank</td>
	            		<td>
	            			<select name="bank" class="form-control" id="bank">
	            				@foreach( Sia::bank() as $b )
	            					<option value="{{ $b->id }}">{{ $b->nm_bank }}</option>
	            				@endforeach
	            			</select>
	            		</td>
	            	</tr>
	            	<tr>
	            		<td>Keterangan</td>
	            		<td><textarea name="ket" class="form-control"></textarea></td>
	            	</tr>
	            </table>

	            <hr>
	        	<button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> KELUAR</button>
	            <button type="submit" id="btn-submit-bayar" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
	        	<br>
	        	<br>
	        </form>
	    </div>

	</div>

	<div id="modal-edit" class="modal fade" data-width="500" tabindex="-1" style="top: 30% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Ubah Pembayaran</h4>
	    </div>
	    <div class="modal-body">
	        <form action="{{ route('keu_update') }}" id="form-edit" method="post">
	            {{ csrf_field() }}
	            <div id="data-edit">

	            </div>

	            <hr>
	        	<button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> BATAL</button>
	            <button type="submit" id="btn-submit-edit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
	        	<br>
	        	<br>
	        </form>
	    </div>
	</div>

	<div id="modal-print" class="modal fade" style="top: 40% !important" tabindex="-1">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Cetak / Ekspor History Pembayaran</h4>
	    </div>
	    <div class="modal-body">
			<div class="ajax-message"></div>

			<table width="100%">
				<tr>
					<td>Tanggal</td>
					<td width="100"><input type="date" id="tgl-1" value="{{ Carbon::now()->format('Y-m-d') }}"></td>
					<td align="center"> s/d </td>
					<td width="100"><input type="date" id="tgl-2" value="{{ Carbon::now()->format('Y-m-d') }}"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="3"><br>
						<a href="javascript:;" onclick="laporan('cetak')" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Cetak</a>
						<a href="javascript:;" onclick="laporan('ekspor')" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Ekspor Excel</a>
					</td>
				</tr>
			</table>

	    </div>

	</div>

	<div id="modal-error" class="modal fade" tabindex="-1">
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

    $(document).ready(function(){

    	$('#nav-mini').trigger('click');

    	var ta = $('#filter-smt option:selected').text();
    	$('.ta').html(ta);

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

        $('#jenis-bayar').change(function(){
        	var val = $(this).val();
        	if ( val == 'BANK' ) {
        		$('#bank').removeAttr('disabled');
        	} else {
        		$('#bank').attr('disabled','');
        	}
        });

        $(document).on( "keyup", 'input[name="jml_bayar"]', function( event ) {

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


        $('#link-cetak-langsung').click(function(){
        	var ang = $('select[name="angkatan"] option:selected').text();
        	var prodi = $('select[name="prodi"] option:selected').text();
        	var jenis_bayar = $('select[name="filter_jenis_bayar"] option:selected').text();
	    	var nm_smt = $('#filter-smt option:selected').text();

        	window.open('{{ route('keu_cetak_langsung_praktek') }}?ang='+ang+'&prodi='+prodi+'&jnsbayar='+jenis_bayar+'&nmsmt='+nm_smt,'_blank');
        });

        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('keu_mhs') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete').hide();
            },
            onSelect: function(suggestion) {
                $('#id-mahasiswa').val(suggestion.data);
                getProdiAsal(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
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
    submit('bayar');
    submit('edit');

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

    function edit(id,id_mhs_reg)
    {
    	$('#modal-edit').modal('show');
    	$('#data-edit').html('<br><br><center><i class="fa fa-spinner fa-spin"></i></center>');
    	$.ajax({
    		url: '{{ route('keu_edit') }}',
    		data: { id_mhs_reg: id_mhs_reg, id: id },
    		success: function(result){
    			$('#data-edit').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
    }

    function jenisBayar(val)
    {
	    if ( val == 'BANK' ) {
			$(document).find('#bank-edit').removeAttr('disabled');
		} else {
			$(document).find('#bank-edit').attr('disabled','');
		}
    }

    function laporan(tipe)
    {
    	var tgl_1 = $('#tgl-1').val();
    	var tgl_2 = $('#tgl-2').val();
    	var jenis_bayar = $('select[name="filter_jenis_bayar"] option:selected').text();
    	var nm_smt = $('#filter-smt option:selected').text();

    	if ( tipe == 'cetak' ) {
    		window.open('{{ route('keu_cetak_praktek') }}?tgl1='+tgl_1+'&tgl2='+tgl_2+'&jnsbayar='+jenis_bayar+'&nmsmt='+nm_smt,'_blank');
    	} else {
    		window.open('{{ route('keu_ekspor_praktek') }}?tgl1='+tgl_1+'&tgl2='+tgl_2+'&jnsbayar='+jenis_bayar+'&nmsmt='+nm_smt,'_blank');
    	}
    }
</script>
@endsection