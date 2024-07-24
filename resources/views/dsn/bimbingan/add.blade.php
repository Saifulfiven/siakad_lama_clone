@extends('layouts.app')

@section('title','Komentar Bimbingan Skripsi/Tesis')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Tambah Komentar Bimbingan Skripsi/Tesis
				</header>

				<div class="panel-body">

					<div class="row">
						<div class="col-md-12">
							<form action="{{ route('dsn_bim_komentar') }}" id="form-add-komentar" method="post" enctype="multipart/form-data">
					            {{ csrf_field() }}
					            <input type="hidden" name="id_bimbingan" value="{{ $bim->id }}">
					            <input type="hidden" name="id_mhs_reg" value="{{ $mhs->id }}">
					            <input type="hidden" name="jabatan" value="{{ Request::get('jb') }}">

					            <div class="form-group row">
					                <div class="col-md-4">
					                    <label class="control-label">Tanggal <span>*</span></label>
					                    <input type="date" class="form-control" name="tanggal"  parsley-required="true" value="{{ Carbon::now()->format('Y-m-d') }}">
					                </div>

					                <div class="col-md-6">
					                    <label class="control-label">File (Optional)<span>&nbsp;</span></label>
					                    <input type="file" name="file" class="form-control">
					               </div>

					            </div>

					            <div class="form-group row">
					                <div class="col-md-12">
					                	<label class="control-label">Sub Pokok Bahasan <span>*</span></label>
					                    <input type="text" class="form-control" name="sub_pokok_bahasan" parsley-required="true">
					                </div>
					            </div>

					            <div class="form-group row">
					                <div class="col-md-12">
					                    <label class="control-label">Komentar/Saran <span>*</span></label>
					               		<textarea cols="10" id="komentar" class="form-control" name="komentar" rows="12"></textarea>
					               </div>
					            </div>

					            <hr>
					        	<button type="button" onclick="goBack()" class="btn btn-danger btn-sm pull-left"><i class="fa fa-times"></i> BATAL</button>
					            <button type="submit" id="btn-submit-add-komentar" class="pull-right btn btn-primary btn-sm" onclick="CKupdate()"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
					        </form>
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
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/ckeditor-4-full/ckeditor.js"></script>
<script>

    $(document).ready(function(){

	    CKEDITOR.replace( 'komentar', {
            startupFocus : true,
            uiColor: '#FFFFFF',
            customConfig: '/resources/assets/plugins/ckeditor-4-full/custom_config.js'
        });

        
    });

    function filter(modul, value)
    {
        window.location.href = '{{ route('dsn_bim_filter') }}?go=detail&modul='+modul+'&val='+value;
    }

    function submit(modul)
    {

        var options = {
            beforeSend: function() 
            {
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                window.location.href='{{ route('dsn_bim_detail', ['id_mhs_reg' => $mhs->id, 'id_smt' => Request::get('ta')]) }}';
            },
            error: function(data, status, message)
            {
            	$("#btn-submit-"+modul).removeAttr('disabled');
                $("#btn-submit-"+modul).html("<i class='fa fa-save'></i> SIMPAN");

                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2('add-komentar', pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }

    submit('add-komentar');

    function CKupdate(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances[instance].updateElement();
    }
</script>
@endsection