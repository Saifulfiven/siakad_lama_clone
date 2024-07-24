@extends('informasi.layout.main')

@section('title', 'Pengaturan Akun')

@section('contents')

	<div class="content">
		<div class="row">
			<div class="col-md-5">
				<div class="widget animated fadeInLeft delay-1">
					<div class="widget-head">
						<h2 class="widget-title">Pengaturan Akun&nbsp;</h2>
					</div>
					<div class="widget-body">
					
						{{ Rmt::AlertSuccess() }}
						{{ Rmt::AlertErrors($errors) }}

						<form action="{{ route('akun_update') }}" method="post" class="form-custom tab-content">
							
							{{ csrf_field() }}

							<div class="row tab-pane active" role="tabpanel" id="profil">
								<div class="form-group col-md-12">
									<label for="">Nama</label>
									<input type="text" name="nama" value="{{ Auth::user()->nama }}" class="form-control">
								</div>

								<div class="form-group col-md-12">
									<label for="">Username</label>
									<input type="text" name="username" value="{{ Auth::user()->username }}" class="form-control">
								</div>

								<div class="form-group col-md-12">
									<label for="">Email</label>
									<input type="email" name="email" value="{{ Auth::user()->email }}" class="form-control">
								</div>

								<div class="form-group col-md-12">
									<label for="">Kata Sandi</label>
									<div class="input-group">
										<?php $tgl_password = Rmt::WaktuLalu(Auth::user()->change_password) ?>
										<input type="text" class="form-control password-status" placeholder="{{ empty(Auth::user()->change_password) ? 'Kata sandi belum pernah diubah' : $tgl_password }}" disabled>
										<span class="input-group-btn">
											<button type="button" class="btn" data-toggle="modal" data-target="#password"><i class="fa fa-pencil"></i></buttun>
										</span>
									</div>
								</div>

								<div class="col-md-12 text-right">
									<br>
									<button class="btn btn-info">Simpan</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('modal')
	<div class="modal fade modal-custom modal-small" id="password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			
			<form action="{{ route('akun_update_password') }}" class="form-custom" id="form-password">

				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Ubah Kata Sandi</h4>
					</div>
					<div class="modal-body">

						<div id="message-password" style="display:none"></div>
						
						<div class="content">
							<div class="form-group">
								<label for="">Kata Sandi Lama</label>
								<div class="input-group">
									<input type="password" name="old_password" class="form-control" required>
									<span class="input-group-btn">
										<button type="button" class="btn see-password" data-name="old_password" type="button"><i class="fa fa-eye"></i></button>
									</span>
								</div>
							</div>
							<div class="form-group">
								<label for="">Kata Sandi Baru</label>
								<div class="input-group">
									<input type="password" name="new_password" class="form-control" required disabled>
									<span class="input-group-btn">
										<button type="button" class="btn see-password" data-name="new_password" type="button"><i class="fa fa-eye"></i></button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-cancel" data-dismiss="modal">Batal</button>
						<button class="btn btn-submit submit-password"><i class="fa fa-floppy-o"></i>Simpan</button>
					</div>
				</div>

			</form>

		</div>
	</div>
@endsection

@section('registerscript')

	<script>

		function parseObj(data){
			arr = []
			for(var event in data){
			    var dataCopy = data[event]
			    for(key in dataCopy){
			        if(key == "start" || key == "end"){
			            dataCopy[key] = new Date(dataCopy[key])
			        }
			    }
			    arr.push(dataCopy)
			}
			return arr;
		}

		$('.see-password').click(function() {
			var name = $(this).data('name');
			var element = $('input[name="' + name + '"]');
			var type = element.attr('type');
			if (type == 'text') {
				element.attr('type', 'password');
				$(this).html('<i class="fa fa-eye"></i>');
			} else {
				element.attr('type', 'text');
				$(this).html('<i class="fa fa-eye-slash"></i>');
			}
		});

		$('input[name="old_password"]').keyup(function(){
			var pwd = $(this).val();
			if ( pwd.length > 0 ) {
				$('input[name="new_password"]').removeAttr('disabled');
			} else {
				$('input[name="new_password"]').attr('disabled','');
			}
		});

		$('#form-password').on('submit', function(){
			$(".submit-password").attr('disabled','');
			$(".submit-password").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> &nbsp;Simpan");
			
			var param = $(this).serialize();

			$.get('{{ route('akun_update_password') }}?'+param)
				.success(function(data){
					var sel = $('#message-password');
					sel.hide();
					sel.html('Berhasil mengubah kata sandi');
					sel.fadeIn(500);
					sel.attr('class','alert alert-success');
					$('.submit-password').removeAttr('disabled');
					$('.submit-password').html('<i class="fa fa-floppy-o"></i>Simpan');
					$('#form-password').find('input').val('');
					$('input[name="new_password"]').attr('disabled','');
					$('.password-status').attr('placeholder','Beberapa detik yang lalu');
				})
				.fail(function(data,status,message){
					var respon = parseObj(data.responseJSON);
					var pesan = '';
					for ( i = 0; i < respon.length; i++ ){
						pesan += respon[i]+"<br>";
					}

					if ( pesan == '' ) {
						pesan = message;
					}
					var sel = $('#message-password');
					sel.hide();
					sel.html(pesan);
					sel.fadeIn(500);
					sel.attr('class','alert alert-danger');

					$('.submit-password').removeAttr('disabled');
					$('.submit-password').html('<i class="fa fa-floppy-o"></i>Simpan');
				});

			return false;
		});

	</script>

@endsection