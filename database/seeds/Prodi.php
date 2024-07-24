<?php

use Illuminate\Database\Seeder;

class Prodi extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('prodi')->truncate();

			$data = [ 
				['id_prodi' => '61201', 'id_fakultas' => 1, 'nm_prodi' => 'Manajemen', 'jenjang' => 'S1'],
				['id_prodi' => '62201', 'id_fakultas' => 1, 'nm_prodi' => 'Akuntansi', 'jenjang' => 'S1'],
				['id_prodi' => '61101', 'id_fakultas' => 2, 'nm_prodi' => 'Manajemen', 'jenjang' => 'S2']
			];

			DB::table('prodi')->insert($data);
    }
}
