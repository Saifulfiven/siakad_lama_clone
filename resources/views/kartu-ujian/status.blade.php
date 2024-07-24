@extends('layouts.app')

@section('title','Cek Kartu Ujian')

@section('content')
	<div id="overlay"></div>

	<div id="content">

		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Cek Kartu Ujian
					</header>

					<div class="panel-body">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<div class="table-responsive">
							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="130">Tahun Akademik</td>
									<td width="200">
										<select class="form-custom mw-2" onchange="filter('smt', this.value)">
											@foreach( Sia::listSemester() as $smt )
												<option value="{{ $smt->id_smt }}" {{ Session::get('jdu_semester') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
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

									<td width="300px">
										<form action="{{ route('ku_status') }}" method="get" id="form-cari">
											<div class="input-group pull-right">
												<input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
												<div class="input-group-btn">
													<a href="{{ route('ku_status') }}" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
													<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>

								</tr>
							</table>
						
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
													<a href="javascript:;" onclick="detail('{{ $r->id }}', '{{ $r->kode_mk }} - {{ $r->nm_mk }}')" class="btn btn-primary btn-xs" title="Detail"><i class="fa fa-search-plus"></i></a> &nbsp;
												</span>
											</td>
										</tr>
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


<div id="modal-detail" class="modal fade container" data-width="800" style="top: 20% !important" tabindex="-1">
    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 id="matakuliah-detail">Detail Ujian</h4>
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
@endsection

@section('registerscript')
<script>

    $(document).ready(function(){

        $('#reset-cari').click(function(){
        	var q = $('input[name="q"]').val();
        	$('input[name="q"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });
    });

    function filter(modul, value)
    {
    	window.location.href='?'+modul+'='+value;
    }

    function detail(id, matakuliah)
    {
    	$('#matakuliah-detail').html(matakuliah);
    	$('#modal-detail').modal('show');

	    $.ajax({
            url: '{{ route('ku_status_detail') }}/'+id,
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
</script>
@endsection