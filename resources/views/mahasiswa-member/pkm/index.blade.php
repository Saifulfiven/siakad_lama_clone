@extends('layouts.app')

@section('title','PKM')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Program Kreativitas Mahasiswa
				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td width="150">
									<select class="form-control input-sm" onchange="filter('smt', this.value)">
										<option value="all">All Semester</option>
										@foreach( Sia::listSemester() as $sm )
											<option value="{{ $sm->id_smt }}" {{ Session::get('pkm.smt') == $sm->id_smt ? 'selected':'' }}>{{ $sm->nm_smt }}</option>
										@endforeach
									</select>
								</td>
								<td></td>
								<td width="300px">
									<form action="{{ route('m_pkm_cari') }}" method="post" id="form-cari">
										<div class="input-group pull-right">
											{{ csrf_field() }}
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('pkm.cari') }}">
											<div class="input-group-btn">
												<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								<td width="110px">
									<a href="{{ route('m_pkm_daftar') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> DAFTAR</a>
								</td>

							</tr>
						</table>
						
						<div class="table-responsive">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Semester</th>
										<th>Judul</th>
										<th>Ketua</th>
										<th>Pembimbing</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach($pkm as $r)
										<tr>
											<td>{{ $loop->iteration - 1 + $pkm->firstItem() }}</td>
											<td>{{ $r->id_smt }}</td>
											<td align="left">{{ str_limit($r->judul, 30) }}</td>
											<td>{{ $r->ketua }}</td>
											<td>{{ $r->pembimbing }}</td>
											<td>
												<span class="tooltip-area">
													<a href="javascript::void(0);" class="btn btn-primary btn-xs" title="Lihat" onclick="detail('{{ $r->id }}')"><i class="fa fa-search-plus"></i></a> &nbsp; &nbsp; 
													@if ( $r->ketua )
														<a href="{{ route('m_pkm_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a>
													@endif
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>

							@if ( !empty($pkm) )

								@if ( $pkm->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $pkm->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $pkm->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $pkm->render() }}
								</div>

							@else
								Anda belum terdaftar dalam kelompok PKM<br>
								Jika anda adalah ketua PKM, silahkan daftarkan kelompok anda di sini.
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


<div id="modal-detail" class="modal fade container" data-width="800" style="top: 20% !important" tabindex="-1">
    <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 id="judul-pkm">Detail</h4>
    </div>
    <div class="modal-body">
		<div class="ajax-message"></div>

    	<div class="col-md-12" id="content-detail">

    	</div>


    	<div class="col-md-12">
            <hr>
            <button type="button" data-dismiss="modal" class="btn btn-danger pull-right">Tutup</button>
            <br>
            <br>
            <br>
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
    });


    function filter(modul, value)
    {
        window.location.href = '{{ route('m_pkm_filter') }}?modul='+modul+'&val='+value;
    }

    function detail(id)
    {
    	$('#modal-detail').modal('show');
    	$.ajax({
            url: '{{ route('m_pkm_detail') }}/'+id,
            data : { preventCache : new Date() },
            beforeSend: function( xhr ) {
                $('#content-detail').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            },
            success: function(data){
                $('#content-detail').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan muat ulang halaman');
            }
        });
    }
</script>
@endsection