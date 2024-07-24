@extends('layouts.app')

@section('title','Kegiatan Dosen')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<a href="{{ route('dosen_kegiatan') }}">kegiatan Dosen</a> / {{ Sia::namaDosen($dosen->gelar_depan, $dosen->nm_dosen, $dosen->gelar_belakang) }}
				</header>

				<div class="panel-body">


					{{ Rmt::AlertError() }}
					{{ Rmt::AlertSuccess() }}
					
					<div class="table-responsive">

						<table border="0" width="100%" style="margin-bottom: 10px">
							<tr>
								<td colspan="3"><p><b>Filter</b></p></td>
							</tr>
							<tr>
								<td width="200">
									<select class="form-control input-sm mw-2" onchange="filter('kategori', this.value)">
										<option value="all">Semua Kategori</option>
										@foreach( Rmt::katKegiatanDosen() as $key => $val )
											<option value="{{ $key }}" {{ Session::get('kegiatan.kategori') == $key ? 'selected':'' }}>{{ $val }}</option>
										@endforeach
									</select>
								</td>
								<td width="10"></td>
								<td width="100">
									<select class="form-control input-sm mw-2" onchange="filter('tahun', this.value)">
										<option value="all">Tahun</option>
										@foreach( $tahun as $thn )
											<option value="{{ $thn->tahun }}" {{ Session::get('kegiatan.tahun') == $thn->tahun ? 'selected':'' }}>{{ $thn->tahun }}</option>
										@endforeach
									</select>
								</td>
								<td></td>
							</tr>
						</table>

						<hr>

						<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="dataTable">
							<thead class="custom">
								<tr>
									<th width="20px">No.</th>
									<th>Nama Kegiatan</th>
									<th>Kategori</th>
									<th>File</th>
									<th>Tanggal</th>
									<th>Tahun</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach( $kegiatan as $kg )
									<tr>
										<td align="center">{{ $loop->iteration }}</td>
										<td>{{ $kg->nama_kegiatan }}</td>
										<td>{{ Rmt::katKegiatanDosen($kg->id_kategori) }}</td>
										<td>
											<div class="icon-resources">
                                                <?php $icon = Rmt::icon($kg->file); ?>
                                                <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                                {{ str_limit($kg->file, 20)}}
                                            </div>
										</td>
										<td align="center">{{ Carbon::parse($kg->tgl_kegiatan)->format('d/m/Y') }}</td>
										<td align="center">{{ $kg->tahun }}</td>
										<td align="center">
											<a href="{{ route('dosen_kegiatan_viewdok', ['id' => $kg->id, 'id_dosen' => $dosen->id, 'file' => $kg->file]) }}" class="btn btn-sm btn-primary" target="_blank">Lihat Dokumen</a>
											<a href="{{ route('dosen_kegiatan_delete', ['id' => $kg->id]) }}" onclick="return confirm('Anda yakin ingin menghapus data ini')" class="btn btn-sm btn-danger">Hapus</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>

						@if ( count($kegiatan) > 0 )
							<a href="{{ route('dosen_kegiatan_download', ['id_dosen' => $dosen->id]) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> Download Semua Data</a>
						@endif
					</div>
				</div>
			</section>
		</div>
		
	</div>
	<!-- //content > row-->
		
</div>
<!-- //content-->

@endsection

@section('registerscript')
<script type="text/javascript" src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
		$('#dataTable').DataTable({
			"order": [[ 0, 'asc' ]]
	    });
	    
	})

    function filter(modul, value)
    {
        window.location.href = '{{ route('dosen_kegiatan_filter') }}?modul='+modul+'&val='+value;
    }
</script>
@endsection