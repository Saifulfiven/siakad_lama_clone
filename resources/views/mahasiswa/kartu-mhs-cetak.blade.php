<!DOCTYPE html>
<html>
<head>
	<title>Cetak Kartu mhs</title>
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
	        	height: 53.9mm;
				margin: 0;
			}

			html, body {
			    height:100%; 
			    margin: 0 !important; 
			    padding: 0 !important;
			    overflow: hidden;
			}

			span {
		  		color: #000fff !important;
			    -webkit-print-color-adjust: exact !important;
			    color-adjust: exact !important;
			}
			.konten-s1 {
				color: #000 !important;
			}
		}

		.kartu {
			position: relative;
			border-radius: 5px;
		}

		.kartu img.bg {
			height: 55mm;
			width: 87.755mm;
		}

		.foto {
			position: absolute;
			left: 21px;
			top: 62px;
			border: 1px solid red;
			background-color: #eee;
			width: 20mm;
			height: 26mm;
		}

		.foto img {
			width: 20mm;
			height: 26mm;
		}

		.foto-s1 {
			position: absolute;
			background: url('<?= url('storage') ?>/foto-mahasiswa/<?= $mhs->mhs->foto_mahasiswa ?>') no-repeat bottom center;
			left: 11px;
			bottom: 8px;
			width: 89px;
			height: 133px;
			background-size: 89px 130px;
		}

		.konten {
			position: absolute;
			left: 110px;
			top: 59px;
			color: #000;
			font-size: 9px;
			font-weight: bold;
		}

		.konten-s1 {
			position: absolute;
			left: 150px;
			top: 50px;
			color: #000 !important;
			font-size: 8px;
			font-weight: bold;
		}

		<?php if ( $lenNama > 22 && $lenNama < 26 ) { ?>
			.konten .nama {
					font-size: 8px !important;
			}
		<?php } elseif ( $lenNama > 25 ) { ?>
			.konten .nama {
					font-size: 7px !important;
			}
		<?php } ?>


		.footer {
			font-size: 9px;
			font-weight: bold;
		}

		<?php if ( $mhs->id_prodi <> '61101' ) { ?>
			.logo {
				position: absolute;
				top: 9px;
				left: 85px;
				margin-right: 10px;
				width: 33px;
				float: left;
			}

			.kop {
				position: absolute;
				top: -8px;
				left: 128px;
				color: #000 !important;
				line-height: 1em;
				-webkit-print-color-adjust: exact;
			}

			.kop p {
				text-align: center;
			}

			.kop .st {
				font-size: 11px;
				font-weight: bolder;
			}

			.nm {
				font-size: 18px;
				font-family: 'Calligraphic';
				font-weight: bolder;
			}

			.jabatan {
				position: absolute;
				left: 210px;
				bottom: 60px;
			}

			.pimpinan {
				position: absolute;
				left: 165px;
				bottom: 17px;
			}

		<?php } else { ?>
			.logo {
				position: absolute;
				top: 7px;
				left: 95px;
				margin-right: 10px;
				width: 40px;
				float: left;
			}

			.kop {
				position: absolute;
				top: -15px;
				left: 145px;
				color: #000 !important;
				line-height: 0.95em;
			}

			.kop p {
				text-align: center;
			}

			.kop .st {
				font-size: 9px;
				font-weight: bolder;
			}

			.nm {
				font-size: 15.5px;
				font-family: 'Calligraphic';
				font-weight: bolder;
				padding-left: 5px;
			}

			.prodi {
				font-size: 11px;
				font-weight: bolder;
			}

			.jabatan {
				position: absolute;
				left: 215px;
				bottom: 60px;
			}

			.pimpinan {
				position: absolute;
				left: 195px;
				bottom: 20px;
			}
		<?php } ?>

	</style>
</head>
<body onload="window.print()">

	<div class="kartu">
		<img class="bg" src="<?= url('resources') ?>/assets/img/ktm/new-ktm-s2.jpg">
		<?php if ( !empty($mhs->mhs->foto_mahasiswa) ) { ?>
			<div class="foto">
				<img src="<?= url('storage') ?>/foto-mahasiswa/<?= $mhs->mhs->foto_mahasiswa ?>">
			</div>
		<?php } ?>
			<div class="konten">
			<table style="margin-top: 12px;margin-left: 22px" border="0">
				<tr>
					<td>NIM</td>
					<td>:</td>
					<td><?= $mhs->nim ?></td>
				</tr>
				<tr>
					<td>Nama</td>
					<td>:</td>
					<td><?= $mhs->mhs->nm_mhs ?></td>
				</tr>
				<tr>
					<td width="62px">Program Studi</td>
					<td>:</td>
					<td><?= $mhs->id_prodi == '61101' ? 'Magister Manajemen' : $mhs->prodi->jenjang.' '.$mhs->prodi->nm_prodi ?></td>
				</tr>
				<tr>
					<td>Tempat Lahir</td>
					<td>:</td>
					<td><?= $mhs->mhs->tempat_lahir ?></td>
				</tr>
				<tr>
					<td>Tanggal Lahir</td>
					<td>:</td>
					<td><?= Rmt::tgl_indo($mhs->mhs->tgl_lahir) ?></td>
				</tr>
			</table>
		</div>

	</div>
</body>
</html>
