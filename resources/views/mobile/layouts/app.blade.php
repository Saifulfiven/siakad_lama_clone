<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta information -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex">
<!-- Title-->
<title>@yield('title',config('app.name'))</title>
<!-- Favicons -->
<link rel="shortcut icon" href="{{ url('/') }}/favicon.ico">

<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300,800' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Ubuntu:300,400,700' rel='stylesheet' type='text/css'>

<!-- CSS Stylesheet-->
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/bootstrap/bootstrap-themes.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/plugins/tooltipster/css/tooltipster.bundle.min.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/plugins/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/style-mobile.css" />
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-46277156-2');

  document.cookie = "SSN=d567e8wsdf;expires=Fri, 31 Dec 9999 23:59:59 GMT;path=/m/;SameSite=None;Secure;httponly";

</script>
@yield('heading')
</head>
<body>



<div id="wrapper">
	
	<div id="caplet-overlay" style="display: none">
		<div class="spinner"></div>
	</div>
	
	<div id="main">
		
		@yield('content')		
			
	</div>
	<!-- //main-->

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
<!-- Library Chart-->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/chart/chart.js"></script>
<!-- Library  5+ plugins for bootstrap -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/pluginsForBS/pluginsForBS.js"></script>

<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/tooltipster/js/tooltipster.bundle.min.js"></script>
<!-- Library 10+ miscellaneous plugins -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/miscellaneous/miscellaneous.js"></script>
<!-- Library Themes Customize-->
<script type="text/javascript" src="{{ url('resources') }}/assets/js/caplet.custom.js"></script>

<script>
	$(function(){
		$('#caplet-overlay').show();

		$(window).load(function() {
			$("#caplet-overlay").fadeOut("slow");
		});

		$('.btn-loading a, a.btn-loading').click(function(){
            $('#caplet-overlay').show();
        });
	});

$('.petunjuk').tooltipster({
    animation: 'fade',
    delay: 200,
    theme: 'tooltipster-punk',
    trigger: 'click',
    contentAsHTML: true
});

function parseObj(r){arr=[];for(var e in r){var a=r[e];for(key in a)("start"==key||"end"==key)&&(a[key]=new Date(a[key]));arr.push(a)}return arr}
function showSuccess(msg, duration = 5000, posisi = 'bottom')
{
	$.notific8(msg,{ life:duration,horizontalEdge:posisi, theme:"primary" ,heading:" Pesan "});
}

function goBack() {
  window.history.back();
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
	return ".xlsx,.xls,.csv,.docx,.pdf,.pptx,.ppt,.ppsx,.odp,.zip,.rar";
}
</script>
@yield('registerscript')
</body>
</html>