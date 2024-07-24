@extends('layouts.app')

@section('title','Penilaian Ujian Seminar')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Penilaian Ujian Seminar
				</header>

				<div class="panel-body">

					<div class="col-md-12">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<div class="table-responsive">
							<table border="0" width="100%" style="min-width: 550px;margin-bottom: 10px">
								<tr>
									<td width="160">
										<select class="form-control" style="max-width: 160px" onchange="filter('smt', this.value)">
											@foreach( Sia::listSemester() as $sm )
												<option value="{{ $sm->id_smt }}" {{ Session::get('seminar.smt') == $sm->id_smt ? 'selected':'' }}>{{ $sm->nm_smt }}</option>
											@endforeach
										</select>
									</td>
									<td></td>
									<td width="250px">
										<form action="{{ route('dsn_seminar_cari') }}" method="post" id="form-cari">
											<div class="input-group pull-right">
												{{ csrf_field() }}
												<input type="text" class="form-control" name="cari" value="{{ Session::get('seminar.cari') }}">
												<div class="input-group-btn">
													<button class="btn btn-default" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
													<button  class="btn btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>
								</tr>
							</table>

							<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
								<thead class="custom">
									<tr>
										<th width="20px">No.</th>
										<th>Nim</th>
										<th>Nama</th>
										<th>Prodi</th>
										<th width="150">Aksi</th>
									</tr>
								</thead>
								<tbody align="center">
									@foreach( $seminar as $bim )
										<tr>
											<td>{{ $loop->iteration }}</td>
											<td align="left">{{ $bim->nim }}</td>
											<td align="left">{{ $bim->nm_mhs }}</td>
											<td>{{ $bim->jenjang }} - {{ $bim->nm_prodi }}</td>
											<td>
												<a href="{{ route('dsn_seminar_detail', ['id_mhs_reg' => $bim->id_mhs_reg, 'id_smt' => $bim->id_smt]) }}" class="btn btn-primary btn-sm"><i class="fa fa-star"></i> Berikan Penilaian</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>

							@if ( $seminar->total() == 0 )
								&nbsp; Tidak ada data
							@endif

							@if ( $seminar->total() > 0 )
								<div class="pull-left">
									Jumlah data : {{ $seminar->total() }}
								</div>
							@endif

							<div class="pull-right"> 
								{{ $seminar->render() }}
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

        $('#upload-file').change(function(){
        	$('#form-upload-file').submit();
        });
    });

    function filter(modul, value)
    {
        window.location.href = '{{ route('dsn_seminar_filter') }}?modul='+modul+'&val='+value;
    }

    function submit(modul)
    {
    	var btn = '<?= !empty($data_bim) ? 'UPLOAD PERUBAHAN':'UPLOAD FILE' ?>';

        var options = {
            beforeSend: function() 
            {
            	let filename = $('input[type=file]').val().split('\\').pop();
            	$('#nama-file').html(filename);
            	$('body').modalmanager('loading');
                $("#btn-upload-file").attr('disabled','');
                $("#btn-upload-file").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengupload...");
            },
            success:function(data, status, message) {
                window.location.reload();
            },
            error: function(data, status, message)
            {
            	$('#nama-file').html('');
            	$('#btn-upload-file').html('<i class="fa fa-plus"></i> '+btn);
            	$('#btn-upload-file').removeAttr('disabled');

                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2('pelatihan', pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }

    submit('upload-file');
</script>
@endsection