@extends('layouts.app')

@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
		<li class="h-seperate"></li>
		<li><a>NAIK SEMESTER</a></li>
	</ul>
@endsection

@section('content')

	<div id="content">
	
		<div class="row">
		
			<div class="col-md-12" >
				<section class="panel" style="min-height: 400px">
					<header class="panel-heading">
						
					</header>

					<div class="panel-body">
						<form action="{{ route('naik_smt_store') }}" method="post" id="form-naik-smt">
							{{ csrf_field() }}
							<button id="btn-submit-naik-smt" class="btn btn-primary"><i class="fa fa-refresh"></i> NAIKKAN SEMESTER</button>
						</form>
					</div>
				</section>
			</div>
				
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->

	<div id="modal-success" class="modal fade" tabindex="-1">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
	        <h4 class="modal-title">INFORMASI</h4>
	    </div>
	    <!-- //modal-header-->
	    <div class="modal-body">
	    	<div class="alert alert-info">
	    		Berhasil menaikkan semester
	    	</div>

	        <center>
	            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
	        </center>
	    </div>
	    <!-- //modal-body-->
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
	$(function(){
		$('#modal-success').on('hidden.bs.modal', function(){
			window.location.reload();
		});
	});

    function showMessage(modul,pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit-'+modul).removeAttr('disabled');
        $('#btn-submit-'+modul).html('<i class="fa fa-refresh"></i> NAIKKAN SEMESTER');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menaikkan semester...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(modul, data.msg);
                } else {
                    $('#modal-success').modal({
                    	backdrop: 'static',
                    	keyboard: false
                    });
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
    submit('naik-smt');
</script>

@endsection