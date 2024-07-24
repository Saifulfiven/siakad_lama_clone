@extends('layouts.app')

@section('title','Pembayaran Semester Pendek')

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
						Pembayaran Semester Pendek
						<select onchange="filter('smt', this.value)" id="filter-smt">
							@foreach( $semester as $smt )
								@if ( $smt->smt == 1 )
									<?php continue ?>
								@endif
								<option value="{{ $smt->id_smt }}" {{ Session::get('sp_smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
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
									<td width="165">
										<select class="form-custom mw-2" name="prodi" onchange="filter('prodi', this.value)" id="list-prodi">
											<option value="all">Semua Prodi</option>
											@foreach( Sia::listProdi() as $pr )
						                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('sp_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
						                    @endforeach
										</select>
									</td>

									<td width="150">
										<select class="form-custom mw-2" onchange="filter('angkatan', this.value)" id="list-prodi">
											<option value="all">All angkatan</option>
											@foreach( Sia::listAngkatan() as $a )
						                    	<option value="{{ $a }}" {{ Session::get('sp_angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
						                    @endforeach
										</select>
									</td>

									<td width="">
										<select class="form-custom mw-2" onchange="filter('bayar', this.value)">
											<option value="ALL" {{ Session::get('sp_bayar') == 'all' ? 'selected':'' }}>ALL STATUS BAYAR</option>
											<option value="BB" {{ Session::get('sp_bayar') == 'BB' ? 'selected':'' }}>BELUM BAYAR</option>
											<option value="SB" {{ Session::get('sp_bayar') == 'SB' ? 'selected':'' }}>SUDAH BAYAR</option>
										</select>
									</td>

									<td width="300px">
										<form action="" id="form-cari">
											<div class="input-group pull-right">
												<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
												<div class="input-group-btn">
													<a href="{{ route('keu_sp') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
													<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>

								</tr>
							</table>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" data-provide="data-table">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>NIM</th>
											<th>Nama</th>
											<th>Prodi</th>
											<th>Tahun Akademik</th>
											<th>jml SKS</th>
											<th>Tgl Bayar</th>
											<th>Jumlah Dibayar</th>
											<th>Status bayar</th>
											<th width="80">Aksi</th>
										</tr>
									</thead>
									<tbody align="center">
										<?php $no = 1 ?>
										@foreach($mahasiswa as $r)
											<!-- tampilkan yg belum bayar -->
											@if ( Session::get('sp_bayar') == 'BB' )
												@if ( !empty($r->jml_bayar) )
													<?php continue ?>
												@endif
											@endif
											
											<!-- tampilkan yg sudah bayar -->
											@if ( Session::get('sp_bayar') == 'SB' )
												@if ( empty($r->jml_bayar) )
													<?php continue ?>
												@endif
											@endif
											<tr>
												<td>{{ $no++ }}</td>
												<td width="100">{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td>{{ $r->jenjang .' '. $r->nm_prodi }}</td>
												<td class="ta"></td>
												<td>{{ $r->jml_sks }}</td>
												<td>{{ !empty($r->tgl_bayar) ? Carbon::parse($r->tgl_bayar)->format('d/m/Y') : '-' }}</td>
												<td>{{ empty($r->jml_bayar) ? '-' : 'Rp '.Rmt::rupiah($r->jml_bayar) }}</td>
												<td>
													<select onchange="updateStatusBayar('{{ $r->id_mhs_reg }}',this.value)" class="form-control">
														<option value="A" {{ $r->sudah_bayar > 0 ? 'selected': '' }}>SUDAH bayar</option>
														<option value="N" {{ $r->sudah_bayar == 0 ? 'selected': '' }}>BELUM Bayar</option>
													</select>
												</td>
												<td>
													<span class="tooltip-area">
														@if ( Sia::keuangan() )
															<a href="javascript:;" onclick="bayar('{{ $r->id_mhs_reg }}','{{ $r->nim .' - '.$r->nm_mhs }}')" class="btn btn-theme btn-xs" title="Bayar"><i class="fa fa-dollar"></i></a> &nbsp;
															<a href="{{ route('keu_detail_sp', ['id' => $r->id_mhs_reg]) }}?smt={{ Session::get('sp_smt') }}&bayarsp=99" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i></a> &nbsp;
														@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

							</div>
						</div>
					</div>
				</section>
			</div>
			
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

	<div id="modal-bayar" class="modal fade" data-width="800" tabindex="-1">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Input Pembayaran</h4>
	    </div>
	    <div class="modal-body">

		    <div class="col-md-6" id="data-pembayaran">
		    	<center>
			    	<br>
			    	<br>
			    	<br>
			    	<i class="fa fa-spinner fa-spin"></i>
			    	<br>
			    	<br>
			    	<br>
			    </center>
		    </div>

	    	<div class="col-md-6">
		        <form action="{{ route('keu_sp_store') }}" id="form-bayar" method="post">
		            {{ csrf_field() }}
		            <input type="hidden" name="id_mhs_reg" id="id-mhs-reg">

		            <table class="table" width="100%" border="0">
		            	<tr>
		            		<td style="padding: 10px 0">Mahasiswa</td>
		            		<td id="mahasiswa"></td>
		            	</tr>
		            	<tr>
		            		<td style="padding: 10px 0">Semester</td>
		            		<td class="ta"></td>
		            	</tr>
		            	<tr>
		            		<td style="padding: 10px 0">Jenis Pembayaran</td>
		            		<td>Semester Pendek</td>
		            	</tr>
		            	<tr>
		            		<td>Jumlah Bayar</td>
		            		<td>
		            			<input type="text" name="jml_bayar" class="form-control">
		            		</td>
		            	</tr>
		            	<tr>
		            		<td>Tanggal Bayar</td>
		            		<td><input type="date" name="tgl_bayar" class="form-control mw-2"></td>
		            	</tr>
		            	<tr>
		            		<td>Tempat Bayar</td>
		            		<td>
		            			<select name="jenis_bayar" class="form-control" id="jenis-bayar">
		            				<option value="LAINNYA">LAINNYA</option>
		            				<option value="BANK">BANK</option>
		            			</select>
		            		</td>
		            	</tr>
		            	<tr>
		            		<td>Nama Bank</td>
		            		<td>
		            			<select name="bank" disabled="" class="form-control" id="bank">
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
	</div>

	<div id="modal-edit" class="modal fade" data-width="600" tabindex="-1" style="top: 30% !important">
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
<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>

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
	    	var nm_smt = $('#filter-smt option:selected').text();

        	window.open('{{ route('keu_cetak_langsung_sp') }}?bayar_spp=99&ang='+ang+'&prodi='+prodi+'&nmsmt='+nm_smt,'_blank');
        });

        $('table[data-provide="data-table"]').dataTable({
        	"bFilter": false,
        	"bLengthChange" : false,
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

    function bayar(id_mhs_reg, mhs)
    {
    	$('#id-mhs-reg').val(id_mhs_reg);
    	$('#mahasiswa').html(mhs);
    	$('#modal-bayar').modal('show');

    	getPembayaran(id_mhs_reg);
    }

    function getPembayaran(id_mhs_reg)
    {
    	$.ajax({
    		url: '{{ route('keu_data_pembayaran_sp') }}',
    		data: { id_mhs_reg: id_mhs_reg},
    		success: function(result){
    			$('#data-pembayaran').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
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

    function updateStatusBayar(id_mhs_reg, ket)
    {
    	window.location.href='?update_status_bayar='+ket+'&id_mhs_reg='+id_mhs_reg;
    }

    function hapus(id,id_mhs_reg)
    {
    	var conf = confirm('Anda yakin ingin menghapus data ini?');
    	var smt_mulai = $('#smt_mulai').val();

    	if ( conf == true ) {

	    	$('#data-pembayaran').html('<br><br><br><center><i class="fa fa-spinner fa-spin"></i></center>')

	    	$.ajax({
	    		url: '{{ route('keu_delete') }}/'+id,
	    		success: function(result){
	    			getPembayaran(id_mhs_reg);
	    			$.notific8('Berhasil menghapus data',{ life:5000,horizontalEdge:"bottom", theme:"primary" ,heading:" Pesan "});
	    		},
	    		error: function(data,status,msg){
	    			alert(msg);
	    		}
	    	});
    	}
    }

    function laporan(tipe)
    {
    	var tgl_1 = $('#tgl-1').val();
    	var tgl_2 = $('#tgl-2').val();
    	var nm_smt = $('#filter-smt option:selected').text();

    	if ( tipe == 'cetak' ) {
    		window.open('{{ route('keu_cetak_sp') }}?tgl1='+tgl_1+'&tgl2='+tgl_2+'&bayar_sp=99&nmsmt='+nm_smt+'&smt={{ Session::get('sp_smt') }}','_blank');
    	} else {
    		window.open('{{ route('keu_ekspor_sp') }}?tgl1='+tgl_1+'&tgl2='+tgl_2+'&bayar_sp=99&nmsmt='+nm_smt+'&smt={{ Session::get('sp_smt') }}','_blank');
    	}
    }
</script>
@endsection