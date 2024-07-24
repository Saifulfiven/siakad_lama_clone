@extends('layouts.app')

@section('title','Pembayaran Semester')

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
						Pembayaran Semester Tahun Akademik 
						<select onchange="filter('smt', this.value)" id='filter-smt'>
							@foreach( $semester as $smt )
								<option value="{{ $smt->id_smt }}" {{ Session::get('mhs_keu_smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
							@endforeach
						</select>

						<div class="hidden-xs pull-right">
							<a href="javascript:;" data-target="#modal-print" data-toggle="modal" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Cetak History</a>
							<a href="javascript:;" id="link-cetak-langsung" class="btn btn-theme-inverse btn-xs"><i class="fa fa-download"></i> Cetak berdasarkan filter</a>
							<a href="javascript:;" data-toggle="modal" data-target="#modal-impor" class="btn btn-theme btn-xs" data-backdrop="static"><i class="fa fa-upload"></i> Impor Data Pembayaran</a>
						</div>
					</header>

					<div class="panel-body">

						<div class="col-md-2" style="padding-left: 0">

						</div>

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							@if ( Session::has('errors_impor') )
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert">
										<span aria-hidden="true">&times;</span>
										<span class="sr-only">Close</span>
									</button>
									<b>DETAIL KESALAHAN..!</b><br>
									@foreach( Session::get('errors_impor') as $er )
										- {{ $er }}<br>
									@endforeach
								</div>
							@endif

							<div class="table-responsive">
								<table border="0" width="100%" style="min-width: 900px; margin-bottom: 10px">
									<tr>
										<td width="">
											<select class="form-custom mw-2" name="angkatan" onchange="filter('angkatan', this.value)">
												<option value="all">Angkatan</option>
												@foreach( Sia::listAngkatan() as $a )
							                    	<option value="{{ $a }}" {{ Session::get('mhs_keu_angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
							                    @endforeach
											</select>
										</td>
										<td width="">
											<select class="form-custom mw-2" name="smt" onchange="filter('smtin', this.value)" {{ Session::get('mhs_keu_angkatan') == 'all' ? 'disabled':'' }}>
												<option value="all">All smt masuk</option>
							                    <option value="1" {{ Session::get('mhs_keu_smtin') == 1 ? 'selected' : '' }}>GANJIL</option>
							                    <option value="2" {{ Session::get('mhs_keu_smtin') == 2 ? 'selected' : '' }}>GENAP</option>
											</select>
										</td>
										<td width="">
											<select class="form-custom mw-2" name="prodi" onchange="filter('prodi', this.value)" id="list-prodi">
												<option value="all">Semua Prodi</option>
												@foreach( Sia::listProdi() as $pr )
							                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('mhs_keu_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
							                    @endforeach
											</select>
										</td>
										<td width="">
											<select class="form-custom mw-2" onchange="filter('status', this.value)">
												@foreach( Sia::statusMhs() as $st )
													<option value="{{ $st->id_jns_keluar }}" {{ $st->id_jns_keluar == Session::get('mhs_keu_status') ? 'selected':'' }}>{{ $st->ket_keluar }}</option>
									            @endforeach
											</select>
										</td>

										<td width="">
											<select class="form-custom mw-2" onchange="filter('bayar', this.value)">
												<option value="ALL" {{ Session::get('mhs_keu_bayar') == 'ALL' ? 'selected':'' }}>ALL STATUS BAYAR</option>
												<option value="BB" {{ Session::get('mhs_keu_bayar') == 'BB' ? 'selected':'' }}>BELUM BAYAR</option>
												<option value="SB" {{ Session::get('mhs_keu_bayar') == 'SB' ? 'selected':'' }}>SUDAH BAYAR</option>
											</select>
										</td>

										<td width="300px">
											<form action="" id="form-cari">
												<div class="input-group pull-right">
													<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
													<div class="input-group-btn">
														<a href="{{ route('keu') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
														<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
													</div>
												</div>
											</form>
										</td>

									</tr>
								</table>
							</div>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>NIM</th>
											<th>Nama</th>
											<th>Prodi</th>
											<th>Status</th>
											<th>Biaya Kuliah</th>
											<th>Potongan</th>
											<th>Telah Dibayar</th>
											<th>Sisa Pembayaran</th>
											<th width="145">Status</th>
											<th width="80">Aksi</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach($mahasiswa as $r)

											<?php $biaya = Sia::biayaPerMhs($r->id_mhs_reg,$r->semester_mulai, $r->id_prodi) ?>
											<?php $potongan = Sia::totalPotonganPerMhs($r->id_mhs_reg,$r->semester_mulai, Session::get('mhs_keu_smt') ) ?>
											<?php $tunggakan = $biaya - $potongan - $r->jml_bayar ?>

											<tr>
												<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
												<td width="100">{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td>{{ $r->jenjang .' '. $r->nm_prodi }}</td>
												<td>{{ $r->ket_keluar }}</td>
												<td align="right">{{ Rmt::rupiah($biaya) }}</td>
												<td align="right">{{ Rmt::rupiah($potongan) }}</td>
												<td align="right">{{ empty($r->jml_bayar) ? '0' : Rmt::rupiah($r->jml_bayar) }}</td>
												<td align="right">{{ Rmt::rupiah($tunggakan) }}</td>
												<td>
													<select onchange="updateStatusBayar('{{ $r->id_mhs_reg }}',this.value)" class="form-control">
														<option value="A" {{ $r->sudah_bayar > 0 ? 'selected': '' }}>SUDAH bayar</option>
														<option value="N" {{ $r->sudah_bayar == 0 ? 'selected': '' }}>BELUM Bayar</option>
													</select>
												</td>
												<td>
													<span class="tooltip-area">
														@if ( $r->akm == 'C' || $r->akm == 'D' )
															<a class="btn btn-info btn-xs" title="SEDANG CUTI"><i class="fa fa-info"></i></a>
														@else
															<?php $nama = str_replace("'", '&apos;', $r->nm_mhs); ?>
															@if ( Sia::role('keuangan|admin') )
																@if ( empty($tunggakan) )
																	<a href="javascript:;" onclick="bayar('{{ $r->id_mhs_reg }}','{{ $r->nim .' - '.$nama }}','{{ $r->semester_mulai }}','lunas')" class="btn btn-success btn-xs" title="LUNAS"><i class="fa fa-check"></i></a>
																@else
																	<a href="javascript:;" onclick="bayar('{{ $r->id_mhs_reg }}','{{ $r->nim .' - '.$nama }}','{{ $r->semester_mulai }}')" class="btn btn-theme btn-xs" title="Bayar"><i class="fa fa-dollar"></i></a>
																@endif &nbsp;

																<a href="{{ route('keu_detail', ['id' => $r->id_mhs_reg]) }}?smt={{ Session::get('mhs_keu_smt') }}" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i></a>
															@endif
														@endif
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
										<br>
										<br>
										<button class="btn btn-primary btn-sm" onclick="showAllSb()">Set Semua Sudah Bayar</button>
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

	<div id="modal-bayar" class="modal fade" data-width="800" tabindex="-1" style="top: 40% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Tambah Pembayaran</h4>
	    </div>
	    <div class="modal-body">
		    <div class="col-md-6" id="data-pembayaran">

		    </div>

	    	<div class="col-md-6" id="insert-pembayaran">
		        <form action="{{ route('keu_store') }}" id="form-bayar" method="post">
		            {{ csrf_field() }}
		            <input type="hidden" name="id_mhs_reg" id="id-mhs-reg">

		            <table class="table" width="100%" border="0">
		            	<tr>
		            		<td style="padding: 10px 0">Mahasiswa</td>
		            		<td id="mahasiswa"></td>
		            	</tr>
		            	<tr>
		            		<td style="padding: 10px 0">Semester</td>
		            		<td id="nm-smt"></td>
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
		            		<td>Cara Bayar</td>
		            		<td>
		            			<select name="jenis_bayar" class="form-control" id="jenis-bayar">
		            				<option value="BANK">BANK</option>
		            				<option value="LAINNYA">LANGSUNG</option>
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
					<td width="100"><input type="date" id="tgl-1" class="form-control" value="{{ Carbon::now()->format('Y-m-d') }}"></td>
					<td align="center"> s/d </td>
					<td width="100"><input type="date" id="tgl-2" class="form-control" value="{{ Carbon::now()->format('Y-m-d') }}"></td>
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

	<div id="modal-impor" class="modal fade" tabindex="-1" style="top:30%">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title">Impor Data Pembayaran</h4>
		</div>
		<!-- //modal-header-->
		<div class="modal-body">
			<form id="form-impor" action="{{ route('keu_impor') }}" enctype="multipart/form-data" method="post">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="fileExcel">Upload File</label>
					<input type="file" class="form-control" id="fileExcel" name="file">
					<p class="help-block">Unggah file excel <b>.xlsx</b></p>
				</div>
				
				<button type="submit" id="btn-submit-impor" class="btn btn-theme btn-sm">IMPOR</button>&nbsp; &nbsp; &nbsp;
				<a href="{{ url('storage') }}/contoh-data/contoh format impor pembayaran.xlsx"  target="_blank" class="btn btn-sm btn-primary pull-right">LIHAT CONTOH DATA</a>

			</form>
		</div>
		<!-- //modal-body-->
	</div>

	<div id="modal-error" class="modal fade" tabindex="-1" style="top:30%">
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

	<div id="modal-set-status-bayar" class="modal fade" tabindex="-1" style="top:30%">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
	        <h4 class="modal-title">Set status bayar</h4>
	    </div>
	    <!-- //modal-header-->
	    <div class="modal-body">
	        <center>
	        	<div class="ajax-message-sb"></div>
	        	<div class="btn-sb">
		            <button type="button" data-dismiss="modal" class="btn btn-sm btn-default pull-left">Batal</button>
		            <button type="button" class="btn btn-sm btn-primary pull-right" onclick="setAllSudahBayar()">Set Semua Sudah Bayar</button>
	        	</div>
	        </center>
	        <div>&nbsp;</div>
	    </div>
	    <!-- //modal-body-->
	</div>

	<input type="hidden" id="smt_mulai">
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

<script>

    $(document).ready(function(){

    	$('#nav-mini').trigger('click');

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
        	window.open('{{ route('keu_cetak_langsung') }}?ang='+ang+'&prodi='+prodi,'_blank');
        });

        var nm_smt = $('#filter-smt option:selected').text();
        $('#nm-smt').html(nm_smt);
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
    submit('impor');

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

    function bayar(id_mhs_reg, mhs, smt_mulai, ket = '')
    {

    	if ( ket == 'lunas' ) {
    		$('#insert-pembayaran').hide();
    		$('#modal-bayar').attr('style', 'top: 20% !important')
    	} else {
    		$('#insert-pembayaran').show();
    		$('#modal-bayar').attr('style', 'top: 40% !important')
    	}

    	$('#data-pembayaran').html('<br><br><br><center><i class="fa fa-spinner fa-spin"></i></center>')
    	$('#id-mhs-reg').val(id_mhs_reg);
    	$('#mahasiswa').html(mhs);

    	$('#modal-bayar').modal('show');

    	$('#smt_mulai').val(smt_mulai);

    	getPembayaran(id_mhs_reg, smt_mulai);
    }

    function updateStatusBayar(id_mhs_reg, ket)
    {
    	window.location.href='?update_status_bayar='+ket+'&id_mhs_reg='+id_mhs_reg;
    }

    function getPembayaran(id_mhs_reg,smt_mulai)
    {
    	$.ajax({
    		url: '{{ route('keu_data_pembayaran') }}',
    		data: { id_mhs_reg: id_mhs_reg, semester_mulai: smt_mulai },
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

    function hapus(id,id_mhs_reg)
    {
    	var conf = confirm('Anda yakin ingin menghapus data ini?');
    	var smt_mulai = $('#smt_mulai').val();

    	if ( conf == true ) {

	    	$('#data-pembayaran').html('<br><br><br><center><i class="fa fa-spinner fa-spin"></i></center>')

	    	$.ajax({
	    		url: '{{ route('keu_delete') }}/'+id,
	    		success: function(result){
	    			getPembayaran(id_mhs_reg, smt_mulai);
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

    	if ( tipe == 'cetak' ) {
    		window.open('{{ route('keu_cetak') }}?tgl1='+tgl_1+'&tgl2='+tgl_2,'_blank');
    	} else {
    		window.open('{{ route('keu_ekspor') }}?tgl1='+tgl_1+'&tgl2='+tgl_2,'_blank');
    	}
    }

    function showAllSb()
    {
    	$('#modal-set-status-bayar').modal({backdrop: 'static', keyboard: false});
    }

    function setAllSudahBayar()
    {
    	$('.btn-sb').show();
    	
    	if ( confirm('Anda ingin update semua pembayaran mahasiswa menjadi SUDAH BAYAR') ) {
    		$('.ajax-message-sb').html('<h3><i class="fa fa-spin fa-spinner"></i> Memproses...</h3> Mungkin akan memerlukan beberapa menit.');
    		$('.btn-sb').hide();

    		$.ajax({
	    		url: '{{ route('keu_set_all_sb') }}',
	    		success: function(result){
	    			alert('PROSES SELESAI, Aktifkan filter status bayar untuk memastikan semua telah berhasil.');
	    			window.location.reload();
	    		},
	    		error: function(data,status,msg){
	    			var respon = parseObj(data.responseJSON);
	                var pesan = '';
	                for ( i = 0; i < respon.length; i++ ){
	                    pesan += "- "+respon[i]+"\n";
	                }
	                if ( pesan == '' ) {
	                    pesan = message;
	                }

	                alert(pesan);

	    			$('.ajax-message-sb').html('');
    				$('.btn-sb').show();
	    		}
	    	});
    	}
    }
</script>
@endsection