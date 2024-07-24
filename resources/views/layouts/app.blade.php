<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta information -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Title-->
<title>@yield('title',config('app.name'))</title>
<!-- Favicons -->
<link rel="shortcut icon" href="{{ url('/') }}/favicon.png">

<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300,800' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Ubuntu:300,400,700' rel='stylesheet' type='text/css'>

<!-- CSS Stylesheet-->
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap-themes.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/plugins/tooltipster/css/tooltipster.bundle.min.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/plugins/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/style.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/styleTheme3.css" />
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-46277156-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-46277156-2');
</script>

@yield('heading')
</head>
<body class="leftMenu nav-collapse">
<div id="wrapper">
	
	<div id="caplet-overlay" style="display: none">
		<div class="spinner"></div>
	</div>

	<div id="header">
			<div class="logo-area clearfix">
				<!-- <a href="#" class="logo"></a> -->
			</div>
			<!-- //logo-area-->
			<div class="tools-bar">
				<ul class="nav navbar-nav nav-main-xs">
					<li><a href="#" class="icon-toolsbar nav-mini" id="nav-mini"><i class="fa fa-bars"></i></a></li>
				</ul>

				@yield('topMenu')

				<ul class="nav navbar-nav navbar-right tooltip-area">
					@if ( Sia::adminOrAkademik() || Sia::ketua1() )
						<li>
							<a href="#menu-right" onclick="getPeriode()" data-toggle="tooltip" title="Ubah Periode" data-container="body" data-placement="left">
								<b>SEMESTER:</b> {{ Sia::sessionPeriode('nama') }}
							</a>
						</li>
						<li><a class="avatar-header">
							<img alt="" src="{{ url('resources') }}/assets/img/logo_n.png"  class="circle">
							</a>
						</li>
					@elseif ( Sia::keuangan() )
						<li>
							<a data-container="body" data-placement="left">
								<b>TAHUN AKADEMIK : </b> {{ Sia::sessionPeriode('nama') }}
							</a>
						</li>
						<li><a class="avatar-header">
							<img alt="" src="{{ url('resources') }}/assets/img/avatar.png"  class="circle">
							</a>
						</li>
					@elseif ( Sia::mhs() )

						<!-- <li>
							<a href="#menu-right" data-toggle="tooltip" title="Ubah NIM" data-container="body" data-placement="left">
								<b>{{ Session::get('nim') }}</b>
							</a>
						</li> -->

						<li><a class="avatar-header">
							<?php 
								if ( empty(Sia::sessionMhs('foto')) ) {
									$foto_mhs = Sia::sessionMhs('jenkel') == 'P' ? 'user-women.png':'user-man.png';
								} else {
									$foto_mhs = Sia::sessionMhs('foto');
								}
							?> 
							<?php $file_foto = config('app.url-foto-mhs').'/thumb/'.$foto_mhs; ?>
							<img style="height: 34px;max-height: 34px;max-width: 34px" src="{{ $file_foto }}"  class="circle">
							</a>
						</li>

					@else
						<li><a class="avatar-header">
							<img src="{{ url('resources') }}/assets/img/avatar.png"  class="circle">
							</a>
						</li>
					@endif

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
							<em>{{ str_limit(Auth::user()->nama,15) }} - {{ Session::get('nim') }}</em> <i class="dropdown-icon fa fa-angle-down"></i>
						</a>

						<ul class="dropdown-menu pull-right icon-right arrow">
							<?php 

								if ( Session::has('current_admin') ) { ?>
									<li><a href="{{ route('relogin', ['id_user' => Session::get('current_admin')]) }}"><i class="fa fa-sign-in"></i> Kembali ke Admin</a></li>
								<?php }

								$level = Auth::user()->level;

								switch( $level ) {
									case "admin":
									case "akademik":
									case "personalia":
									case "cs":
									case "keuangan":
									case "ketua":
									case "ketua 1":
									case "jurnal":
									 ?>
										<li><a href="{{ route('users_profil') }}"><i class="fa fa-user"></i> Profile</a></li>
									<?php break;
									case "mahasiswa": ?>
										<li><a href="{{ route('mhs_profil') }}"><i class="fa fa-user"></i> Profile</a></li>
									<?php break;
									default:
									break;
								}

							?>

							@if ( Sia::adminOrAkademik() )
								<li><a href="{{ route('set') }}"><i class="fa fa-cog"></i> Setting </a></li>
							@endif
							<li class="divider"></li>
							<li>
								<a href="{{ url('/logout') }}"
                					onclick="event.preventDefault();
                         					document.getElementById('logout-form').submit();">
               						<i class="fa fa-sign-out"></i> Logout
           						</a>

		                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
		                            {{ csrf_field() }}
		                        </form>
		                    </li>
						</ul>
						<!-- //dropdown-menu-->
					</li>

					<li class="visible-lg">
						<a href="#" class="h-seperate fullscreen" data-toggle="tooltip" title="Full Screen" data-container="body"  data-placement="left">
							<i class="fa fa-expand"></i>
						</a>
					</li>
				</ul>
			</div>
			<!-- //tools-bar-->
			
	</div>
	<!-- //header-->
	
	<div id="main">
		
		@yield('content')		
			
	</div>
	<!-- //main-->
	
	<!-- Left Nav -->
	@include('layouts.left-nav')
	
	@if ( Sia::adminOrAkademik() || Sia::ketua1() )
		<!-- Right Nav -->
		<nav id="menu-right">
			<ul>
				<li class="Label label-lg">Ubah Semester</li>
				<li style="padding: 5px 20px !important">
					<form action="{{ route('ubah_periode') }}">
						<select class="form-control" name="smt">
							@foreach( Sia::listSemester('filter') as $res )
								<option value="{{ $res->id_smt }}" {{ Sia::sessionPeriode() == $res->id_smt ? 'selected':'' }}>{{ $res->nm_smt }}</option>
							@endforeach
						</select>
						<br>
						<button class="btn btn-primary btn-sm">SIMPAN</button>
					</form>
				</li>
			</ul>
		</nav>
		<!-- //nav right menu-->
	@elseif ( Sia::mhs() )
		<!-- Right Nav -->
		<nav id="menu-right">
			<ul>
				<li class="Label label-lg">Ubah NIM</li>
				<li style="padding: 5px 20px !important">
					<p>Apabila anda memiliki riwayat study di Nobel, maka anda dapat mengganti
					 nim anda dengan nim lama untuk melihat history pendidikan anda
					  sebelumnya. </p>
					<form action="{{ route('ubah_nim') }}">
						<select class="form-control" name="nim">
							@foreach( Sia::listNim() as $res )
								<option value="{{ $res->nim }}" {{ Session::get('nim') == $res->nim ? 'selected':'' }}>{{ $res->nim }}</option>
							@endforeach
						</select>
						<br>
						<button class="btn btn-primary btn-sm">SIMPAN</button>
					</form>
				</li>
			</ul>
		</nav>
		<!-- //nav right menu-->
	@endif

    <div id="modal-error2" class="modal fade" tabindex="-1" style="top:30%">
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

</div>
<!-- //wrapper-->

<!-- Jquery Library -->
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.ui.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap.min.js"></script>
<!-- Modernizr Library For HTML5 And CSS3 -->
<script type="text/javascript" src="{{ url('resources') }}/assets/js/modernizr/modernizr.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/mmenu/jquery.mmenu.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/styleswitch.js"></script>
<!-- Library 10+ Form plugins-->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/form/form.js"></script>
<!-- Datetime plugins -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datetime/datetime.js"></script>

<!-- Library  5+ plugins for bootstrap -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/pluginsForBS/pluginsForBS.js"></script>

<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/tooltipster/js/tooltipster.bundle.min.js"></script>
<!-- Library 10+ miscellaneous plugins -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/miscellaneous/miscellaneous.js"></script>
<!-- Library Themes Customize-->
<script type="text/javascript" src="{{ url('resources') }}/assets/js/caplet.custom.js"></script>

<script>

$('.petunjuk').tooltipster({
    animation: 'fade',
    delay: 200,
    theme: 'tooltipster-punk',
    trigger: 'click',
    contentAsHTML: true
});

function tombolHapus(id,msg) {

	if ( confirm(msg) ) {
		document.getElementById('delete-form-'+id).submit();
	}
}

function parseObj(r){arr=[];for(var e in r){var a=r[e];for(key in a)("start"==key||"end"==key)&&(a[key]=new Date(a[key]));arr.push(a)}return arr}
function showSuccess(msg, duration = 5000, posisi = 'bottom')
{
	$.notific8(msg,{ life:duration,horizontalEdge:posisi, theme:"primary" ,heading:" Pesan "});
}

function goBack() {
  window.history.back();
}
function scrollTo(id) {
	var elmnt = document.getElementById(id);
	elmnt.scrollIntoView();
}
function showMessage2(modul,pesan = '')
{
    $('#caplet-overlay').hide();

    if ( pesan !== '' ) {
	    $('.ajax-message').html(pesan);
	    $('#modal-error2').modal('show');  	
    }

    $('#btn-submit-'+modul).removeAttr('disabled');
    $('#btn-submit-'+modul).html('<i class="fa fa-save"></i> Simpan');
}
function getIcon(ekstensi) {
	var gambar;

	switch (ekstensi) {
		case 'docx':
		case 'doc':
			gambar = 'doc.svg';
		break;
		case 'xls':
		case 'xlsx':
		case 'csv':
			gambar = 'excel.svg';
		break;
		case 'pdf':
			gambar = 'pdf.svg';
		break;
		case 'jpg':
		case 'jpeg':
			gambar = 'jpg.svg';
		break;
		case 'png':
			gambar = 'png.svg';
		break;
		case 'zip':
		case 'rar':
		case 'iso':
			gambar = 'zip.svg';
		break;
		case 'txt':
			gambar = 'txt.svg';
		break;
		case 'ppt':
		case 'pptx':
			gambar = 'ppt.svg';
		break;
		
		default:
			gambar = 'file.png';
		break;
	}

	return gambar;
}

function fileAccept() {
	return ".xlsx,.xls,.csv,.docx,.pdf,.pptx,.ppt,.ppsx,.odp,.zip,.rar,.mp3,.wav,.ogg,.acc,.wma";
}
</script>
@yield('registerscript')
</body>
</html>