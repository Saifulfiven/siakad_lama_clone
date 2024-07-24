@extends('layouts.app')

@section('title','Mahasiswa Semester Pendek')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Mahasiswa Semester Pendek
				</header>

				<div class="panel-body">

					<div class="col-md-12">

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="125">
									<select class="form-control input-sm" onchange="filter('prodi', this.value)">
										<option value="all">All Prodi</option>
										@foreach( Sia::listProdi() as $pr )
											<option value="{{ $pr->id_prodi }}" {{ Session::get('daftar_sp.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
										@endforeach
									</select>
								</td>
								
								<td width="200" style="padding-left: 13px">
									<select onchange="filter('smt', this.value)" class='form-control input-sm' onchange="filter('smt', this.value)">
										@foreach( Sia::listSemester() as $smt )
											<option value="{{ $smt->id_smt }}" {{ Session::get('daftar_sp.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
										@endforeach
									</select>
								</td>
								<td style="padding-left: 13px">
									@if ( count(Session::get('daftar_sp')) > 0 )
										<span class="tooltip-area">
											<a href="{{ route('daftar_sp_filter') }}?remove=1" class="btn btn-xs btn-warning" title="Reset Filter"><i class="fa fa-filter"></i></a>
										</span>
									@endif
								</td>
								<td></td>
								<td width="300px" style="padding-right: 13px">
									<form action="{{ route('daftar_sp_cari') }}" method="post" id="form-cari">
										<div class="input-group pull-right">
											{{ csrf_field() }}
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('daftar_sp.cari') }}">
											<div class="input-group-btn">
												<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								@if ( Sia::role('admin|akademik|cs') )
									<td width="85px">
										<button class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#modal-tambah" data-backdrop="static" data-keyboard="false""><i class="fa fa-plus"></i> Tambah</button>
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
										<th>Semester</th>
										<th>SKS diprogram</th>
										<th>Status Pembayaran</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($mahasiswa as $r)
										<tr>
											<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
											<td>{{ $r->nim }}</td>
											<td align="left">{{ $r->nm_mhs }}</td>
											<td align="left">{{ $r->nm_prodi }} - {{ $r->jenjang }}</td>
											<td>{{ $r->nm_smt }}</td>
											<td>{{ $r->jml_sks }}</td>
											<td>
												<span class="tooltip-area"> 
													@if ( $r->sudah_bayar == 1 )
														<label class="label label-success"><i class="fa fa-check"></i></label>
													@else
														<label class="label label-danger"><i class="fa fa-ban"></i></label>
													@endif
												</span>
											</td>
											<td>
												<span class="tooltip-area">
												@if ( Sia::canAction($r->id_smt) && Sia::role('admin|akademik') )
													<a href="javascript:;" id="ubah"
														data-id="{{ $r->id }}"
														data-mahasiswa="{{ $r->nim }} - {{ $r->nm_mhs }}"
														data-semester="{{ $r->nm_smt }}"
														data-jml_sks="{{ $r->jml_sks }}"
														class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
													<a href="{{ route('daftar_sp_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
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


<div id="modal-tambah" class="modal fade" tabindex="-1" style="top:30%">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
		<h4 class="modal-title">Tambah Mahasiswa</h4>
	</div>
	<!-- //modal-header-->
	<div class="modal-body">
		<form id="form-tambah" action="{{ route('daftar_sp_store') }}" method="post">
			{{ csrf_field() }}
			<div class="form-group">
                <label class="control-label">Semester</label>
                <div>
                    {{ Sia::sessionPeriode('nama') }}
                </div>
            </div>
			<div class="form-group">
				 <label class="control-label">Mahasiswa <span>*</span></label>
				<div style="position: relative;">
                    <div class="input-icon right"> 
                        <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                        <input type="text" style="font-size:13px" class="form-control" name="mahasiswa_value" required="" id="autocomplete-ajax" placeholder="Mahasiswa">
                        <input type="hidden" id="id-mhs-reg" name="mahasiswa">
                        <input type="hidden" name="id_smt" value="{{ Sia::sessionPeriode() }}">
                    </div>
                </div>
			</div>
			<div class="form-group jdk-normal">
                <label class="control-label">Jumlah SKS Diprogram <span>*</span></label>
                <div>
                    <input type="number" required="" class="form-control mw-1" value="" name="jml_sks" maxlength="1" size="1">
                </div>
            </div>
			<hr>			
			<button type="submit" id="btn-submit-tambah" class="btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i>  SIMPAN</button>&nbsp; &nbsp; &nbsp;
			<button type="button" data-dismiss="modal" class="btn btn-sm btn-default pull-right">BATAL</button>

		</form>
	</div>
	<!-- //modal-body-->
</div>

<div id="modal-ubah" class="modal fade" tabindex="-1" style="top:30%">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
		<h4 class="modal-title">Ubah</h4>
	</div>
	<!-- //modal-header-->
	<div class="modal-body">
		<form id="form-ubah" action="{{ route('daftar_sp_update') }}" method="post">
			{{ csrf_field() }}
			<input type="hidden" name="id" id="id">
			<div class="form-group">
                <label class="control-label">Semester</label>
                <div id="semester">
                </div>
            </div>
			<div class="form-group">
				 <label class="control-label">Mahasiswa</label>
				<div id="mahasiswa">
                </div>
			</div>
			<div class="form-group jdk-normal">
                <label class="control-label">Jumlah SKS Diprogram <span>*</span></label>
                <div>
                    <input type="number" required="" id="jml_sks" class="form-control mw-1" value="" name="jml_sks" maxlength="1" size="1">
                </div>
            </div>
			<hr>			
			<button type="submit" id="btn-submit-ubah" class="btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i>  SIMPAN</button>&nbsp; &nbsp; &nbsp;
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

        $('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

        @if ( Session::has('success') )
        	$.notific8('Berhasil menyimpan data',{ life:5000,horizontalEdge:"bottom", theme:"success" ,heading:" Pesan "});
        @endif

        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('daftar_sp_mhs') }}',
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
                $('#id-mhs-reg').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $('#ubah').click(function(){
        	var div = $(this);
        	$('#id').val(div.data('id'));
        	$('#mahasiswa').html(div.data('mahasiswa'));
        	$('#semester').html(div.data('semester'));
        	$('#jml_sks').val(div.data('jml_sks'));
        	$('#modal-ubah').modal('show');
        });
    });


    function filter(modul, value)
    {
        window.location.href = '{{ route('daftar_sp_filter') }}?modul='+modul+'&val='+value;
    }

    function showMessage(pesan, modul)
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
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengimpor...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(data.msg, modul);
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
                showMessage(pesan, modul);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('tambah');
    submit('ubah');
</script>
@endsection