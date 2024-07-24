<?php
$jenjang = Request::get('j');
?>
<!DOCTYPE html>
<html lang='id'>

<head>

	<style>

		html, body {
			width: 100%;
			height: 100%;
			position: relative;
			margin: 0;
			padding: 0;
			font-family: 'Arial';
			overflow: hidden;
		}

		@media print and (color) {
			* {
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
		}
		@media print {
	        @page {
	        	size: landscape;
	        	width: 85.5mm;
	        	height: 53.6mm;
				margin: 0cm 0cm 0cm 0cm;
			}
		}

		.kartu {
			position: relative;
			border-radius: 5px;
		}

		.kartu .bg {
			height: 55mm;
			width: 87.755mm;
		}

		<?php if ( $jenjang == 's1' ) { ?>
			.konten {
				position: absolute;
				text-align: center;
				left: 190px;
				top: 80px;
				color: #000;
				font-size: 16px;
				font-family: 'November';
				font-weight: bolder;
			}
		<?php } else { ?>
			.konten {
				position: absolute;
				text-align: center;
				left: 190px;
				top: 60px;
				color: #000;
				font-size: 16px;
				font-family: 'November';
				font-weight: bolder;
			}
		<?php } ?>

		.logo {
			position: absolute;
			top: 9px;
			left: 118px;
			margin-right: 10px;
			width: 33px;
			float: left;
		}

		.kop {
			position: absolute;
			top: -8px;
			left: 160px;
			line-height: 1em;
		}

		img.logo-tulisan {
			width: 65px;
			position: absolute;
			left: 10px;
			top: 10px;
		}

		.kop span {
			color: #fff !important;
		}

		.kop p {
			text-align: center;
		}

		.kop .st {
			font-size: 9px;
			font-weight: bolder;
		}

		.nm {
			font-size: 15px;
			font-family: 'Calligraphic';
			font-weight: bolder;
		}
/*
		.kop .inst {
			font-size: 8.5px;
			font-weight: bolder;
		}

		.company {
			font-size: 15px;
			font-family: 'Calligraphic';
			font-weight: bolder;
		}*/

		.footer {
			font-size: 8px;
			font-weight: bold;
		}

		.jabatan {
			position: absolute;
			left: 230px;
			bottom: 60px;
		}

		.pimpinan {
			position: absolute;
			left: 190px;
			bottom: 20px;
		}

	</style>
</head>

<body onload="window.print()">

	<div class="kartu">


			<img class="bg" src="{{ url('resources') }}/assets/img/ktm/ktm-depan.jpg">


	</div>

</body>
</html>
