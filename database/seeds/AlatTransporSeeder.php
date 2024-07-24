<?php

use Illuminate\Database\Seeder;

class AlatTransporSeeder extends Seeder
{

    public function run()
    {
      DB::table('alat_transpor')->truncate();

			$data = [ 
				['id_alat_transpor' =>1, 'nm_alat_transpor' => 'Jalan kaki'],
				['id_alat_transpor' =>2, 'nm_alat_transpor' => 'Kendaraan pribadi'],
				['id_alat_transpor' =>3, 'nm_alat_transpor' => 'Angkutan umum/bus/pete-pete'],
				['id_alat_transpor' =>4, 'nm_alat_transpor' => 'Mobil/bus antar jemput'],
				['id_alat_transpor' =>5, 'nm_alat_transpor' => 'Kereta api'],
				['id_alat_transpor' =>6, 'nm_alat_transpor' => 'Ojek'],
				['id_alat_transpor' =>7, 'nm_alat_transpor' => 'Andong/bendi/sado/dokar/delman/becak'],
				['id_alat_transpor' =>8, 'nm_alat_transpor' => 'Perahu penyeberangan/rakit/getek'],
				['id_alat_transpor' =>11, 'nm_alat_transpor' => 'Kuda'],
				['id_alat_transpor' =>12, 'nm_alat_transpor' => 'Sepeda'],
				['id_alat_transpor' =>13, 'nm_alat_transpor' => 'Sepeda motor'],
				['id_alat_transpor' =>14, 'nm_alat_transpor' => 'Mobil pribadi'],
				['id_alat_transpor' =>99, 'nm_alat_transpor' => 'Lainnya']
			];

			DB::table('alat_transpor')->insert($data);
    }
}


