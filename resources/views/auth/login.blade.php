<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta information -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<!-- Title-->
<title>Siakad Institut Teknologi & Bisnis Nobel</title>
<!-- Favicons -->
<link rel="shortcut icon" href="{{ url('favicon.png') }}">
<link rel="canonical" href="http://siakad.stienobel-indonesia.ac.id/login" />
<meta name="description" content="Sistem Informasi Akademik Institut Teknologi dan Bisnis Nobel. SIAKAD merupakan sarana untuk memberikan kemudahan kepada Pengguna khususnya dosen dan mahasiswa dalam kegiatan administrasi akademik secara online.">
<!-- CSS Stylesheet-->
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap-themes2.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/login.css" />

</head>
<body class="full-lg">
<div id="wrapper">

<div id="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
				<div class="account-wall">
						
						<form id="form-login" method="post" action="/login" class="form-signin">
							<section class="align-lg-center">
								<!-- <div class="site-logo"></div> -->
								<img src="/resources/assets/img/logo-bg-br.png" width="100px" alt="SIAKAD Indonesia Institut">
								<h1 class="login-title" style="font-size: 18px"><span>SIAKAD</span> <br>NOBEL Indonesia Institute <small> Masukkan username & password anda</small></h1>
								<small class="error-msg" id="msg">Username/Password salah</small>
								</section>
								{{ csrf_field() }}
							<section>
								<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input  type="text" required class="form-control" name="username" value="{{ old('username') }}" autofocus placeholder="Username">
								</div>
								<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-key"></i></div>
										<input type="password" required class="form-control"  name="password" placeholder="Password">
								</div>
								<button class="btn btn-lg btn-theme-inverse btn-block" type="submit" id="sign-in"><i class="fa fa-sign-in"></i> Masuk</button>
							</section>
							<section class="clearfix">
								<div class="iCheck pull-left"  data-color="red">
								<input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : ''}}>
								<label style="color: #fff" for="remember">Ingat saya</label>
								</div>
								<a href="{{ url('/password/reset')}}" class="pull-right help" style="color: #fff">Reset Password? </a>
							</section>		
						</form>
						<!-- <a href="http://stienobel-indonesia.ac.id" class="footer-link">&copy; 2018 STIE Nobel Indonesia </a> -->
				</div>	
				<!-- //account-wall-->
					
			</div>
			<!-- //col-sm-6 col-md-4 col-md-offset-4-->
		</div>
		<!-- //row-->
	</div>
	<!-- //container-->

</div>
<!-- //main-->

		

</div>

<!-- //wrapper-->
<!-- Jquery Library -->
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.ui.min.js"></script> -->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap.min.js"></script> -->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/plugins/miscellaneous/miscellaneous.js"></script> -->
<!-- Modernizr Library For HTML5 And CSS3 -->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/js/modernizr/modernizr.js"></script> -->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/plugins/mmenu/jquery.mmenu.js"></script> -->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/js/styleswitch.js"></script> -->
<!-- Library 10+ Form plugins-->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/plugins/form/form.js"></script> -->
<!-- Datetime plugins -->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datetime/datetime.js"></script> -->
<!-- Library Chart-->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/plugins/chart/chart.js"></script> -->
<!-- Library  5+ plugins for bootstrap -->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/plugins/pluginsForBS/pluginsForBS.js"></script> -->
<!-- Library 10+ miscellaneous plugins -->
<!-- Library Themes Customize-->
<!-- <script type="text/javascript" src="{{ url('resources') }}/assets/js/caplet.custom.js"></script> -->
<script type="text/javascript">
$(function() {

	@if ($errors->has('username'))
		$('#msg').fadeIn();
		$('input[name="password"]').val('');
	@endif

	function toCenter(){
		var mainH=$("#main").outerHeight();
		var accountH=$(".account-wall").outerHeight();
		var marginT=(mainH-accountH)/2;
	   if(marginT>30){
		   $(".account-wall").css("margin-top",marginT-15);
		}else{
			$(".account-wall").css("margin-top",30);
		}
	}
	toCenter();
	var toResize;
	$(window).resize(function(e) {
		clearTimeout(toResize);
		toResize = setTimeout(toCenter(), 500);
	});

	
	$("#form-login").submit(function(event){
		$('#msg').fadeOut();
		$('#sign-in').html('<i class="fa fa-spinner fa-spin"></i>');
		$('#sign-in').attr('disabled','');
	});

});
</script>
</body>
</html>