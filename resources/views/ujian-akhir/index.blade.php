@extends('layouts.app')

@section('title','Ujian Akhir')

@section('topMenu')
	@include('ujian-akhir.top-menu')
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						{{ Session::get('ua_nm_jenis') }} <span class="header-prodi"></span>

						<div class="pull-right">
							@if ( Session::get('ua_prodi') == '61101' )
								<button class="btn btn-primary btn-xs hidden-xs" data-toggle="modal" data-target="#modal-jadwal">
									<i class="fa fa-print"></i> Cetak Jadwal Ujian
								</button>

							@endif
							
							<a href="{{ route('ua_ekspor_telah_ujian') }}" target="_blank" class="btn btn-primary btn-xs hidden-xs">
								<i class="fa fa-download"></i> Telah Ujian
							</a>

							<a 
								href="javascript:void(0)" 
								class="btn btn-primary btn-xs"
								onclick="cetakRekapNilai()"
								data-toggle="modal"
								data-target="#modal-cetak-nilai"
								data-backdrop="static">
								<i class="fa fa-print"></i> REKAP NILAI
							</a>
						</div>
					</header>

					<div class="panel-body">

						<div class="tabbable">
							<?php 
								$get_jenis = Request::get('jenis');
								$tab_aktif = Request::get('tab');
							?>
                            <ul class="nav nav-tabs" data-provide="tabdrop">
                                <li <?= $tab_aktif == 'all' || empty($tab_aktif) ? 'class="active"':'' ?>><a href="{{ route('ua') }}?jenis={{ $get_jenis }}&tab=all">Semua Data</a></li>
                                @if ( $get_jenis == 'P' )
                                	<li <?= $tab_aktif == 'b' ? 'class="active"':'' ?>><a href="{{ route('ua') }}?jenis={{ $get_jenis }}&tab=b">Butuh Penguji</a>
                                	
                                	</li>
                                @endif
                                <li <?= $tab_aktif == 'c' ? 'class="active"':'' ?>><a href="{{ route('ua') }}?jenis={{ $get_jenis }}&tab=c">Menunggu Persetujuan</a></li>
                                <li <?= $tab_aktif == 'd' ? 'class="active"':'' ?>><a href="{{ route('ua') }}?jenis={{ $get_jenis }}&tab=d">Siap Seminar/Ujian</a></li>
                            </ul>
                            <div class="tab-content">
                            
                                @if ( $tab_aktif == 'all' || empty($tab_aktif) )

                                	@include('ujian-akhir.semua')

                                @elseif ( $tab_aktif == 'b' )

                                	@include('ujian-akhir.butuh-penguji')

                                @elseif ( $tab_aktif == 'c' )

                                	@include('ujian-akhir.menunggu-persetujuan')

                                @else

                                	@include('ujian-akhir.siap-seminar')

                                @endif

							</div>

						</div>

					</div>
				</section>
			</div>
			 
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

	<div id="modal-penguji" class="modal fade" data-width="600" tabindex="-1" style="top: 35% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Penguji</h4>
	    </div>
	    <div class="modal-body">
	        <form action="{{ route('ua_penguji_store') }}" id="form-penguji" method="post">
	            {{ csrf_field() }}
	            <div id="data-penguji"></div>
	            <hr>
	        	<button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> BATAL</button>
	            <button type="submit" id="btn-submit-penguji" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
	        </form>
	    </div>
	</div>

	<div id="modal-jadwal" class="modal fade" style="top: 40% !important" tabindex="-1">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Cetak Jadwal Ujian/Seminar</h4>
	    </div>
	    <div class="modal-body">
			<div class="ajax-message"></div>
			<table width="100%">
				<tr>
					<td width="100"> Pilih Tanggal</td>
					<td width="100" style="padding: 5px">
						<?php
						    $id_mhs = Session::get('mhs_in_ujian_akhir');
					        $mhsArr = [];

					        foreach( $id_mhs as $val ) {
					            $mhsArr[] = $val->id_mhs_reg;
					        }

							$tanggal = DB::table('ujian_akhir')
									->whereIn('id_mhs_reg', $mhsArr)
									->where('tgl_ujian', '<>', '0000-00-00')
									->select('tgl_ujian')
									->groupBy('tgl_ujian')
									->orderBy('tgl_ujian')
									->get();

						?>

						<select name="tgl_ujian" class="form-custom" id="tgl">
							<option value="">Pilih tanggal</option>
							@foreach( $tanggal as $tgl )
								<option value="{{ $tgl->tgl_ujian }}">{{ Rmt::tgl_indo($tgl->tgl_ujian) }}</option>
							@endforeach
						</select>
					</td>
				</tr>
				<tr>
					<td>Pilih ruangan</td>
					<td width="100" style="padding: 5px">
						<?php
							$ruangan = DB::table('ujian_akhir')
									->whereIn('id_mhs_reg', $mhsArr)
									->where('ruangan','<>','')
									->whereNotNull('ruangan')
									->select('ruangan')
									->groupBy('ruangan')
									->orderBy('ruangan')
									->get();
						?>
						<select name="ruang" class="form-custom" id="ruang">
							<option value="">Pilih ruangan</option>
							@foreach( $ruangan as $ruang )
								<option value="{{ $ruang->ruangan }}">{{ $ruang->ruangan }}</option>
							@endforeach
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2"><br>
						<a href="javascript:;" onclick="doCetakJadwal()" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Cetak</a>
					</td>
				</tr>
			</table>

	    </div>

	</div>

	<div id="modal-nilai" class="modal fade" data-width="600" tabindex="-1" style="top: 35% !important">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4>Nilai</h4>
	    </div>
	    <div class="modal-body">
	        <form action="{{ route('ua_nilai_store') }}" id="form-nilai" method="post">

	            {{ csrf_field() }}

	            <div id="data-nilai"></div>
	            <hr>
	        	<button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> BATAL</button>
	            <button type="submit" id="btn-submit-nilai" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
	        </form>
	    </div>
	</div>

	<div id="modal-cetak-nilai" data-width="600px" class="modal fade" tabindex="-1" style="top: 15%">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
	        <h4 class="modal-title">Rekapitulasi Nilai <strong>{{ Session::get('ua_nm_jenis') }} <span class="header-prodi"></span></strong></h4>
	    </div>
	    <!-- //modal-header-->
	    <div class="modal-body">
	    	<div id="konten-rekap-nilai">
	    		
	    	</div>
	    </div>
	    <!-- //modal-body-->
	    <div class="modal-footer">
	    	<center>
	            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
	        </center>
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

<script>

    $(document).ready(function(){

    	$('<input>').attr({
		    type: 'hidden',
		    name: 'jenis',
		    value: '{{ Request::get('jenis') }}'
		}).appendTo('#form-cari');

    	$('<input>').attr({
		    type: 'hidden',
		    name: 'tab',
		    value: '{{ Request::get('tab') }}'
		}).appendTo('#form-cari');

        $('.header-prodi').html($('#list-prodi option:selected').text());

        $('#reset-cari').click(function(){
        	window.location.href="{{ route('ua') }}?jenis={{ Request::get('jenis') }}&tab={{ Request::get('tab') }}";
        });

        $(document).on('click', '.btn-penguji', function(){
            var el = $(this);
            $('#modal-penguji').modal({backdrop: 'static', keyboard: false});
            $('#data-penguji').html('<br><br><br><br><br><br><center><i class="fa fa-spinner fa-spin"></i><br><br><br><br><br><br><br><br>');

            var id_mhs_reg = el.data('id');
			var nim = el.data('nim');
            var nama = el.data('nama');

	        $.ajax({
	            url: '{{ route('ua_penguji') }}',
	            data: { id_mhs_reg: id_mhs_reg, nim : nim, nama: nama, tab: '{{ $tab_aktif }}' },
	            success: function(data){
	                $('#data-penguji').html(data);
	            },
	            error: function(err,data,msg)
	            {
	                alert(msg)
	            }
	        });
            
        });

        $(document).on('click', '.btn-nilai', function(){
            var el = $(this);
            $('#modal-nilai').modal({backdrop: 'static', keyboard: false});
            $('#data-nilai').html('<br><br><br><br><br><br><center><i class="fa fa-spinner fa-spin"></i><br><br><br><br><br><br><br><br>');

            var id_mhs_reg = el.data('id');
			var nim = el.data('nim');
            var nama = el.data('nama');
            var jdk = el.data('jdk');

	        $.ajax({
	            url: '{{ route('ua_nilai') }}',
	            data: { id_mhs_reg: id_mhs_reg, nim : nim, nama: nama,id_jdk:jdk },
	            success: function(data){
	                $('#data-nilai').html(data);
	            },
	            error: function(err,data,msg)
	            {
	                alert(msg)
	            }
	        });
            
        });

    });

    function cetakRekapNilai()
    {
    	$('#konten-rekap-nilai').html('<i class="fa fa-spinner fa-spin"></i> SEDANG MEMUAT...')

        $.ajax({
            url: '{{ route('ua_rekap_nilai') }}',
            success: function(data){
                $('#konten-rekap-nilai').html(data);
            },
            error: function(err,data,msg)
            {
                alert(msg)
            }
        });
    }

    function goCetak(tgl)
    {
        window.open('{{ route('ua_cetak_rekap_nilai') }}?tgl='+tgl);
    }

    function showMessage(modul,pesan)
    {
    	$('body').modalmanager('loading');
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
                $('body').modalmanager('loading');
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(modul, data.msg);
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
                showMessage(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('penguji');
    submit('nilai');

    function filter(modul, value)
    {
    	window.location.href='?jenis={{ $get_jenis }}&tab={{ $tab_aktif }}&'+modul+'='+value;
    }

    function doCetakJadwal()
    {
    	var tgl = $('#tgl').val();
    	var ruang = $('#ruang').val();

    	if ( tgl === '' || ruang === '' ) {
    		alert('Tanggal dan ruangan harus dipilih');
    		return;
    	}
    	window.open('{{ route('ua_cetak_jadwal_seminar') }}?tgl_ujian='+tgl+'&ruang='+ruang);
    }

</script>
@endsection