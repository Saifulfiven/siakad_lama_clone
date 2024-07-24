@extends('layouts.app')

@section('title','Kelas Mahasiswa')

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Kelas Mahasiswa
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							<?php
								$fil_prodi = Session::has('kls.prodi') ? '<p>Prodi: '.implode(',', Session::get('kls.prodi')).'</p>':'';
								$fil_kelas = Session::has('kls.kode') ? '<p>Kelas: '.implode(',', Session::get('kls.kode')).'</p>':'';
								$filtered = $fil_prodi.$fil_kelas;
							?>

							<div class="table-responsive">
								<table border="0" width="100%" style="margin-bottom: 10px; min-width: 500px">
									<tr>
										<td width="90">
											<button class="btn btn-sm btn-filter <?= Session::has('kls') ? 'btn-danger':'btn-default' ?>" title="<?= $filtered ? $filtered : 'None' ?>" data-toggle="modal" data-target="#modal-filter"><i class="fa fa-filter"></i> Filter</button>
										</td>
										<td width="90">
											<a href="{{ route('kelas_ekspor') }}" target="_blank" class="btn btn-sm btn-default" title="Excel"><i class="fa fa-download"></i> Ekspor</a>
										</td>
										<td></td>
										<td width="300px">
											<form action="{{ route('kelas_cari') }}" method="post" id="form-cari">
												<div class="input-group pull-right">
													{{ csrf_field() }}
													<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('kls.cari') }}">
													<div class="input-group-btn">
														<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
														<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
													</div>
												</div>
											</form>
										</td>

									</tr>
								</table>
							</div>
							
							
							<div class="table-responsive">
								<span id="loading-nonkelas" style="display: none"><i class="fa fa-spinner fa-spin"></i></span>
								<span id="check-nonkelas">
									<input type="checkbox" value="1" id="non-kelas" onclick="aksi(this)" <?= Session::get('non_kelas') == 1 ? 'checked':'' ?>>
								</span>
								<label for="non-kelas">Hanya tampilkan yang tidak punya kelas</label>
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="table-data">
									<?php $fakultas = Sia::getFakultasUser() ?>
									<thead class="custom">
										<tr>
											<th width="20px">No.</th>
											<th>Nim</th>
											<th>Nama</th>
											<th>Kelas</th>
											<th>Program Studi</th>
										</tr>
									</thead>
									<tbody align="center">
										@foreach($mahasiswa as $r)
											<tr>
												<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
												<td>{{ $r->nim }}</td>
												<td align="left">{{ $r->nm_mhs }}</td>
												<td>
													<span class="tooltip-area"> 
														<a href="#" class="kelas <?= !empty($r->kode_kelas) ? 'btn btn-primary btn-xs': '' ?>" <?= !empty($r->kode_kelas) ? "title='Ubah kelas'": '' ?> data-type="text" data-pk="{{ $r->id_mhs_reg }}" data-title="Masukkan kelas (Max 5 char)">{{ $r->kode_kelas }}</a>
													</span>
												</td>
												<td>{{ $r->jenjang }} - {{ $r->nm_prodi }}</td>
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

<div id="modal-filter" class="modal fade" data-width="600" style="top: 25%" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Filter</h4>
    </div>
    <div class="modal-body">
        <form action="{{ route('kelas_set_filter') }}" id="form-filter">
        	<div class="form-group">
        		<label class="control-label">Program Studi</label>
        		<div>
        			<select name="prodi[]" class="selectpicker form-control mw-3" multiple>
	        			@foreach( Sia::listProdi() as $r )
	                    	<option value="{{ $r->id_prodi }}" {{ Session::has('kls.prodi') && in_array($r->id_prodi,Session::get('kls.prodi')) ? 'selected':'' }}>{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
	                    @endforeach
	                </select>
	            </div>
        	</div>
        	<div class="form-group">
		        <label class="control-label">Kode Kelas</label>
		        <div>
			        <select name="kelas[]" class="selectpicker form-control mw-2" multiple>

			            @foreach( Sia::kelasMhs() as $kls )
			                <option value="{{ $kls->kode_kelas }}" {{ Session::has('kls.kode') && in_array($kls->kode_kelas, Session::get('kls.kode')) ? 'selected':'' }}>{{ $kls->kode_kelas }}</option>
			            @endforeach
			        </select>
			    </div>
		    </div>
		    <div class="form-group">
		    	<button class="btn btn-primary btn-block btn-set-filter">Terapkan</button>
		    </div>
        </form>
    </div>
</div>

@endsection


@section('registerscript')

<link href="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.css" rel="stylesheet"/>
<script src="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.min.js"></script>
<script>

    $(document).ready(function(){

        $('.kelas').editable({
            url: '{{ route('kelas_update') }}',
            name: 'nilai',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                return params;
            },
            success: function(response, newValue) {
                showSuccess('Berhasil menyimpan data');
            },
            error: function(response,value)
            {
                console.log(JSON.stringify(response));
                var respon = parseObj(response.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = response.statusText;
                }
                showMessage2('', pesan);
            }
        });

        $('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

        $('.btn-filter').tooltipster({
		    animation: 'fade',
		    delay: 200,
		    theme: 'tooltipster-punk',
		    contentAsHTML: true
		});

		$('#form-filter').on('submit', function(){
			$('.btn-set-filter').html('<i class="fa fa-spinner fa-spin"></i> Memproses..');
			$('.btn-set-filter').attr('disabled','');
		});
    });

    function aksi(value)
    {
    	// console.log("Clicked, new value = " + value.checked);
    	$('#loading-nonkelas').show();
    	$('#check-nonkelas').hide();
    	$.get("{{ route('kelas_non_kelas') }}", { value: value.checked }, function(data){

    		$('#loading-nonkelas').hide();
    		$('#check-nonkelas').show();
    		window.location.reload();

    	}).fail(function(){
    		alert('Gagal filter, silahkan muat ulang halaman dan ulangi lagi');
    	});
    }

</script>
@endsection