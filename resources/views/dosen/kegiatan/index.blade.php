@extends('layouts.app')

@section('title','Kegiatan Dosen')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					kegiatan Dosen
				</header>

				<div class="panel-body">


					{{ Rmt::AlertError() }}
					{{ Rmt::AlertSuccess() }}
					
					<div class="table-responsive">
						<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="dataTable">
							<thead class="custom">
								<tr>
									<th width="20px">No.</th>
									<th>Nama</th>
									<th>Jumlah Kegiatan</th>
									<th>Aksi</th>
								</tr>
							</thead>
						</table>
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
	        processing: true,
	        serverSide: true,
	        columnDefs: [
			    {"className": "text-center", "targets": [0,2,3]},
			    { orderable: false, targets: "_all" }
      		],
	        ajax: "{{ route('dosen_kegiatan_data') }}",
	        columns: [
	            { data: 'no' },
	            { data: 'nm_dosen' },
	            { data: 'jml_kegiatan' },
	            { data: 'aksi' },
	        ],
	        cache: false
	    });
	    
	})
</script>
@endsection