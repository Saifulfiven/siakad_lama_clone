<?php

use Illuminate\Database\Seeder;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('agama')->truncate();

			$data = [ 
					['id_agama' => '1', 'nm_agama' => 'Islam'],
					['id_agama' => '2', 'nm_agama' => 'Kristen'],
					['id_agama' => '3', 'nm_agama' => 'Katholik'],
					['id_agama' => '4', 'nm_agama' => 'Hindu'],
					['id_agama' => '5', 'nm_agama' => 'Budha'],
					['id_agama' => '6', 'nm_agama' => 'Konghucu'],
					['id_agama' => '99', 'nm_agama' => 'Lainnya']
			];

			DB::table('agama')->insert($data);
    }
}
