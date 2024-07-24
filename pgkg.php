<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$jenis = @$_GET['jenis'];

switch( $jenis ) {
	case "beranda":
		$data = [
			'error' => 0,
			'data' => [
				'jml_aktivitas' => 10,
				'jml_event' => 5,
				'aktivitas' => 'Bermain'
			]
		];
	break;

	case "login":
		$level = @$_GET['level'];

		if ( $level == 'murid' ) {
			$data = [
				'error' => 0,
				'data' => [
					'id' => 1,
					'nama' => 'Ahmad Afnan',
					'level' => 'murid'
				]
			];
		} else {
			$data = [
				'error' => 0,
				'data' => [
					'id' => 2,
					'nama' => 'Abd Rahmat Ika',
					'level' => 'guru'
				]
			];
		}

	break;

	case "kalender":

		$data = [
			'error' => 0,
			'data' => [
				[
					'ket' => 'Keterangan kalender 1',
					'waktu' => '10 - 11 September 2020'
				],
				[
					'ket' => 'Keterangan kalender 2',
					'waktu' => '10 - 11 Oktober 2020'
				],
				[
					'ket' => 'Keterangan kalender 3',
					'waktu' => '10 - 11 Desember 2020'
				],
			]
		];

	break;

	case "pemberitahuan":

		$pem = [
			[
				'id' => 1,
				'judul' => 'Judul pemberitahuan yang sangat panjang banget sampai sana',
				'ket' => '<p>Konten Pemberitahuan dan seterusnya</p><p>Lorem ipsum dolor sit met consenctetur adipicising elit.</p> <p>pemberitahuan yang sangat panjang banget sampai sana</p>',
				'waktu' => '11 September 2020'
			],
			[
				'id' => 2,
				'judul' => 'Judul pemberitahuan 2',
				'ket' => 'Konten Pemberitahuan dan seterusnya',
				'waktu' => '11 September 2020'
			],
			[
				'id' => 3,
				'judul' => 'Judul pemberitahuan 3',
				'ket' => 'Konten Pemberitahuan dan seterusnya',
				'waktu' => '11 September 2020'
			],
		];

		if ( !empty(@$_GET['id']) ) {
			$pem = $pem[0]['ket'];
		}

		$data = [
			'error' => 0,
			'data' => $pem,
		];

	break;

	case "profil":

		$data = [
			'error' => 0,
			'data' => [
				'nama_ortu' => 'Abd Rahmat Ika',
				'alamat' => 'Jl. Sultan Alauddin No. 212 Kota Makassar',
				'hp_ortu' => '0823 2222 0000'
			]
		];

	break;

	case "add-aktivitas":

		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$data = $request;
		// $data = $request->kelas;
	break;

	case "aktivitas":

		$data = [
			'error' => 0,
			'data' => [
				[
					'jam' => '10.00',
					'judul' => 'Lingkunganku',
					'keterangan' => 'Mengenal silsila keluarga',
				],
				[
					'jam' => '09.00',
					'judul' => 'Binatang',
					'keterangan' => 'Mengenal Binatang Air',
				],
				[
					'jam' => '08.00',
					'judul' => 'Lingkunganku',
					'keterangan' => 'Berinteraksi dengan teman',
				],
			]
		];

	break;

	default: 
		$data = [];
	break;
}

echo json_encode($data);