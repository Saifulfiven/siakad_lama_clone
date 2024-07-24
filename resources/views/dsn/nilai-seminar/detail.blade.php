@extends('layouts.app')

@section('title','Penilaian Ujian Seminar')

@section('heading')
	
	<style>
		.content-bimbingan ol {
			list-style-type: decimal;
			margin-left: 10px;
		}
		.content-bimbingan ul {
			list-style: inside !important;
		}
	</style>

@endsection

@section('content')

<?php $jenis = Session::get('seminar.jenis'); ?>

<div id="overlay"></div>

<div id="content" class="content-bimbingan">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Penilaian Ujian Seminar
					@if ( !empty($menguji) )
						<div class="pull-right" style="margin-top: -5px">
							<a href="{{ route('dsn_seminar_cetak', ['id_mhs_reg' => $mhs->id, 'id_smt' => $id_smt]) }}?jenis={{ $jenis }}&prodi={{ $mhs->id_prodi }}" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i> Cetak Nilai</a>
						</div>
					@endif
				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<div class="row">
							<div class="col-md-6" style="padding-left: 0">
								<div class="table-responsive">
									<table border="0" class="table">
										
										@if ( !empty($menguji) )
											<tr>
												<td width="120"><b>Pilih jenis seminar</b></td>
												<td>
													<select class="form-control mw-2" onchange="filter(this.value)">
														@foreach( $menguji as $key => $val )
															<option value="{{ $key }}" {{ Session::get('seminar.jenis') == $key ? 'selected':'' }}>{{ Rmt::jnsSeminar($key) }}</option>
														@endforeach
													</select>
												</td>
											</tr>
										@endif

										<tr>
											<td width="150"><b>Tahun akademik</b></td>
											<td>
												: {{ Rmt::namaTa($id_smt) }}
											</td>
										</tr>
										<tr>
											<td><b>Mahasiswa</b></td>
											<td>: {{ $mhs->mhs->nm_mhs }} - {{ $mhs->nim }}</td>
										</tr>
										@if ( !empty(@$bimbingan[0]) )
											<tr>
												<td width="120">Pembimbing I</td>
												<td>: {{ Sia::namaDosen(@$bimbingan[0]->gelar_depan, @$bimbingan[0]->nm_dosen, @$bimbingan[0]->gelar_belakang) }}</td>
											</tr>

											<tr>
												<td>Pembimbing II</td>
												<td>: {{ Sia::namaDosen(@$bimbingan[1]->gelar_depan, @$bimbingan[1]->nm_dosen, @$bimbingan[1]->gelar_belakang) }}</td>
											</tr>
											<tr>
												<td colspan="2">
												<b>Judul</b><br>
												{{ @$bimbingan[0]->judul_tmp }}
												</td>
											</tr>
										@endif
									</table>
								</div>

								<div class="hidden-md hidden-lg"><hr></div>
							</div>

							<?php


							$penguji = DB::table('penguji as p')
							            ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
							            ->select('d.id','p.jabatan', DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as nm_dosen'),'p.nilai')
							            ->where('p.id_mhs_reg', $mhs->id)
							            ->whereNotNull('d.id')
							            ->where('p.jenis', $jenis)
							            ->where('p.id_smt', $id_smt)
							            ->get();

							?>

						</div>

						<div class="row">
							<div class="col-md-9" style="padding-left: 0;margin-top: 20px">

								<div class="hidden-md hidden-lg" style="margin-top: 10px">&nbsp;</div>

								@if ( empty($menguji) )
									<p>Jadwal seminar/ujian belum diinput</p>
								@else
									<div class="table-responsive">
							            <p class="pull-left">Data Dosen Penguji <b>"{{ Rmt::jnsSeminar(Session::get('seminar.jenis')) }}"</b></p>

							            <button class="btn btn-primary btn-sm pull-right" onclick="nilai()">
							            	<i class="fa fa-plus"></i>
							            	 Masukkan Nilai / Ubah Nilai
							           	</button>

							            @include('dsn.nilai-seminar.data-penguji')

							        </div>
							    @endif
       						</div>
						</div>

					</div>
				</div>

			</section>
		</div>
		
	</div>
	<!-- //content > row-->

    <div id="modal-nilai" class="modal fade" data-width="600" tabindex="-1" >
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Masukkan/Ubah Nilai</h4>
        </div>
        <div class="modal-body">
            <div class="col-md-12">
	            <form action="{{ route('dsn_seminar_store') }}" class="form-horizontal" id="form-nilai" method="post">
                
                </form>
            </div>
        </div>
    </div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

<script>

    $(document).ready(function(){

        $('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        });

        $('.number').live('keypress', function(event){
          if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();

          }
        });

        
    });

    function filter(value)
    {
        window.location.href = '?jenis='+value;
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
            	$('body').modalmanager('loading');
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                window.location.reload();
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
                showMessage2(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }

    submit('nilai');


    function nilai()
    {
    	var id_mhs_reg = '{{ $mhs->id }}';
    	var id_smt = '{{ Session::get('seminar.smt') }}';
    	var jenis = '{{ Session::get('seminar.jenis') }}';

        let $modal = $('#modal-nilai');

        $('body').modalmanager('loading');
          setTimeout(function(){
            $modal.find("#form-nilai").load('{{ route('dsn_seminar_nilai') }}?id_mhs_reg='+id_mhs_reg+'&id_smt='+id_smt+'&jenis='+jenis, '', function(){
              $modal.modal({backdrop: 'static', keyboard: false});
            });
        }, 500);
    }
</script>

@endsection