<?php

use Illuminate\Database\Seeder;

class PenghasilanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('penghasilan')->truncate();

			$data = [ 
				['id_penghasilan' => 11, 'nm_penghasilan' => 'Kurang dari Rp. 500,000'],
				['id_penghasilan' => 12, 'nm_penghasilan' => 'Rp. 500,000 - Rp. 999,999'],
				['id_penghasilan' => 13, 'nm_penghasilan' => 'Rp. 1,000,000 - Rp. 1,999,999'],
				['id_penghasilan' => 14, 'nm_penghasilan' => 'Rp. 2,000,000 - Rp. 4,999,999'],
				['id_penghasilan' => 15, 'nm_penghasilan' => 'Rp. 5,000,000 - Rp. 20,000,000'],
				['id_penghasilan' => 16, 'nm_penghasilan' => 'Lebih dari Rp. 20,000,000']
			];

			DB::table('penghasilan')->insert($data);
    }
}

