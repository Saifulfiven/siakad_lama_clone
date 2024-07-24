@extends('layouts.app')

@section('title','Jadwal Ujian')

@section('topMenu')
    @include('jadwal-ujian.top-menu')
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">

		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						<table class="table" border="0">
							<tr>
								<td width="130">Tahun Akademik</td>
								<td width="200">
									<select class="form-custom mw-2" onchange="filter('smt', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('jdu_semester') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
					                    @endforeach
									</select>
								</td>
								<td width="10">Prodi</td>
								<td width="180">
									<select class="form-custom mw-2" onchange="filter('prodi', this.value)">
										@foreach( Sia::listProdi() as $pr )
					                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('jdu_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
					                    @endforeach
									</select>
								</td>
								<td width="100">Jenis Ujian</td>
								<td>
									<select class="form-custom mw-2" onchange="filter('jns', this.value)">
					                    <option value="UTS" {{ Session::get('jdu_jenis_ujian') == 'UTS' ? 'selected' : '' }}>UTS</option>
					                    <option value="UAS" {{ Session::get('jdu_jenis_ujian') == 'UAS' ? 'selected' : '' }}>UAS</option>
									</select>
								</td>
								<td><a href="{{ route('pengawas') }}" class="btn btn-theme btn-sm pull-right">PENGAWAS</a></td>
							</tr>
						</table>
					</header>

					<div class="panel-body">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td>
									<div class="btn-group" data-btn-group="monochromatic" data-btn-color="#41CAC0">
										<a href="{{ route('jdu_print') }}" target="_blank" class="btn"><i class="fa fa-print"></i> Jadwal Ujian</a>
										<a href="javascript:;" class="btn" data-toggle="modal" data-target="#modal-kartu-ujian"><i class="fa fa-print"></i> Kartu Ujian</a>
										<a href="{{ route('jdu_print_label_ujian') }}" target="_blank" class="btn"><i class="fa fa-print"></i> Label Ujian</a>
									</div>
								</td>

								<td width="300px">
									<form action="{{ route('jdu') }}" method="get" id="form-cari">
										<div class="input-group pull-right">
											<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
											<div class="input-group-btn">
												<a href="{{ route('jdu') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								@if ( Sia::akademik() )
									<td width="110px">
										<a href="{{ route('jdu_add') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
									</td>
								@endif

							</tr>
						</table>
						
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped" id="table-data">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Waktu</th>
										<th>Matakuliah</th>
										<th>Smstr</th>
										<th>Kelas /<br>Ruang</th>
										<th>Program Studi</th>
										<th>Pengawas</th>
										<th>Peserta</th>
										<th colspan="2">Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($jadwal as $r)

										<tr>
											<td>{{ $loop->iteration - 1 + $jadwal->firstItem() }}</td>
											<td>
												{{ empty($r->hari) ? '-': Rmt::hari($r->hari) }}
												({{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_selesai,0,5) }})
											</td>
											<td align="left">
												<a href="javascript:;" onclick="detail('{{ $r->id }}','{{ $r->kode_mk }} - {{ $r->nm_mk }}')" title="Detail">
													{{ $r->kode_mk }} -
													{{ $r->nm_mk }} ({{ $r->sks_mk }} sks)
												</a>
											</td>
											<td>{{ $r->smt }}</td>
											<td>{{ $r->kode_kls }} / {{ $r->nm_ruangan }}</td>
											<td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
											<td align="left"><?= $r->pengawas ?></td>
											<td>{{ empty($r->jml_peserta) ? '':$r->jml_peserta }}</td>
											<td>
												<span class="tooltip-area">
													<a href="{{ route('jdu_print_absensi_ujian', ['id' => $r->id]) }}" target="blank" class="btn btn-primary btn-transparent btn-xs" title="Cetak Absensi"><i class="fa fa-print"></i></a> &nbsp;
													<a href="javascript:;" onclick="detail('{{ $r->id }}', '{{ $r->kode_mk }} - {{ $r->nm_mk }}')" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i></a> &nbsp;
													@if ( Sia::adminOrAkademik() )
														@if ( Sia::canAction($r->id_smt) )
															<a href="javascript:;" onclick="edit('{{ $r->id }}', '{{ $r->kode_mk }} - {{ $r->nm_mk }}')" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a>
														@endif
													@endif
												</span>
											</td>

											@if ( $r->id_jdk != @$id_jdk )
												<td id="{{ $r->id_jdk }}">
													<span class="tooltip-area">
													@if ( Sia::adminOrAkademik() )
														@if ( Sia::canAction($r->id_smt) )
															<a href="{{ route('jdu_delete', ['id_jdk' => $r->id_jdk])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@endif
													@endif
													</span>
												</td>
											@else
												<script>
													document.getElementById("{{ @$id_jdk }}").setAttribute("rowspan", "2");
												</script>
											@endif
										</tr>
										<?php $id_jdk = $r->id_jdk ?>
									@endforeach
								</tbody>
							</table>
							@if ( $jadwal->total() == 0 )
								&nbsp; Tidak ada data
							@endif

							@if ( $jadwal->total() > 0 )
								<div class="pull-left">
									Jumlah data : {{ $jadwal->total() }}
								</div>
							@endif

							<div class="pull-right"> 
								{{ $jadwal->render() }}
							</div>

						</div>

					</div>
				</section>
			</div>
			
		</div>
		<!-- //content > row-->
		
	</div>
	<!-- //content-->

<div id="modal-jdu" class="modal fade" style="top: 20% !important" tabindex="-1">
    <div class="modal-header">
        <a href="{{ route('pengawas') }}" target="_blank" class="btn btn-xs btn-warning pull-right"><i class="fa fa-plus"></i> Tambah Pengawas</a>
        <h4>Ubah Jadwal Ujian</h4>
    </div>
    <div class="modal-body">
		<div class="ajax-message"></div>

		<div id="close-error" style="display: none">
	        <button class="btn btn-warning btn-xs">OK, Saya mengerti</button>
	        <br>
	        <br>
	    </div>

        <p><b>Matakuliah : </b><span id="matakuliah">Matakuliah</span></p>
        <hr>

        <form action="{{ route('jdu_update') }}" method="post" id="form-edit-jdu">

        	<div class="col-md-12" id="content-edit">

        	</div>

        	<div class="col-md-12">
                <hr>
                <button class="btn btn-primary pull-right" id="btn-submit">Simpan</button>
                <button type="button" data-dismiss="modal" class="btn btn-submit pull-left">Keluar</button>
                <br>
                <br>
                <br>
            </div>
        </form>
    </div>
</div>

<div id="modal-detail" class="modal fade container" data-width="800" style="top: 20% !important" tabindex="-1">
    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 id="matakuliah-detail">Detail Jadwal Ujian</h4>
    </div>
    <div class="modal-body">
		<div class="ajax-message"></div>

    	<div class="col-md-12" id="content-detail">

    	</div>


    	<div class="col-md-12">
            <hr>
            <button type="button" data-dismiss="modal" class="btn btn-submit pull-right">Keluar</button>
            <br>
            <br>
            <br>
        </div>
    </div>
</div>

<div id="modal-kartu-ujian" class="modal fade" style="top: 40% !important" tabindex="-1">
    <div class="modal-header">
        <a href="{{ route('jdu_print_ku') }}" target="blank" class="btn btn-theme btn-xs pull-right">CETAK MASSAL</a>
        <h4>Cetak Kartu Ujian</h4>
    </div>
    <div class="modal-body">
		<div class="ajax-message"></div>

		<p><b>Cetak Per Mahasiswa</b></p>
		<table width="100%">
			<tr>
				<td width="100">Masukkan NIM</td>
				<td>
					<div style="position: relative;">
                        <div class="input-icon right"> 
                            <span id="spinner-autocomplete-2" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                            <input type="text" class="form-control" id="autocomplete">
                        </div>
                    </div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><br><a href="" target="blank" id="btn-cetak-nim" class="btn btn-theme-inverse btn-sm">Cetak</a></td>
			</tr>
		</table>

    </div>

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-submit btn-sm pull-right">Keluar</button>
    </div>

</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.mockjax.js"></script>
            
<script>

    $(document).ready(function(){

        $(".collapse.in").each(function(){
        	$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
        });
        
        $(".collapse").on('show.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hide.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });

        $('#nav-mini').trigger('click');

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

            var options = {
                beforeSend: function() 
                {
                    $('#overlay').show();
                    $("#btn-submit").attr('disabled','');
                    $("#btn-keluar").attr('disabled','');
                    $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                },
                success:function(data, status, message) {
                    if ( data.error == 1 ) {
                        $("#close-error").show();
                        showMessage(data.msg);
                    } else {
                        window.location.reload();
                    }
                },
                error: function(data, status, message)
                {
                    var respon = parseObj(data.responseJSON);
                    var pesan = '';
                    for ( var i = 0; i < respon.length; i++ ){
                        pesan += "- "+respon[i]+"<br>";
                    }
                    if ( pesan == '' ) {
                        pesan = message;
                    }
                    $("#close-error").show();
                    showMessage(pesan);
                }
            }; 

            $('#form-edit-jdu').ajaxForm(options);

        $("#close-error").click(function(){
            $('.ajax-message').hide();
            $(this).hide();
        });

        $('#autocomplete').autocomplete({
            serviceUrl: '<?= route('jdu_get_mhs') ?>',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-2').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-2').hide();
            },
            onSelect: function(suggestion) {
                $('#btn-cetak-nim').attr('href', '{{ route('jdu_print_ku') }}?mhs='+suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });
    });

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

    function edit(id, matakuliah)
    {
    	$('#matakuliah').html(matakuliah);
    	$('#modal-jdu').modal({ backdrop: 'static' }, 'show');

	    $.ajax({
            url: '{{ route('jdu_edit') }}/'+id,
            data : { preventCache : new Date() },
            beforeSend: function( xhr ) {
                $('#content-edit').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            },
            success: function(data){
                $('#content-edit').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
            }
        });
    }

    function detail(id, matakuliah)
    {
    	$('#matakuliah-detail').html(matakuliah);
    	$('#modal-detail').modal('show');

	    $.ajax({
            url: '{{ route('jdu_detail') }}/'+id,
            data : { preventCache : new Date() },
            beforeSend: function( xhr ) {
                $('#content-detail').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            },
            success: function(data){
                $('#content-detail').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
            }
        });
    }

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-keluar').removeAttr('disabled');
        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }
</script>
@endsection