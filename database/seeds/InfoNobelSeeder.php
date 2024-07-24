<?php

use Illuminate\Database\Seeder;

class InfoNobelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('info_nobel')->truncate();

			$data = [ 
				['id_info_nobel' => 1, 'nm_info' => 'Koran'],
				['id_info_nobel' => 2, 'nm_info' => 'Website Nobel'],
				['id_info_nobel' => 3, 'nm_info' => 'Facebook'],
				['id_info_nobel' => 4, 'nm_info' => 'Teman'],
				['id_info_nobel' => 5, 'nm_info' => 'Kerabat/Keluarga'],
				['id_info_nobel' => 6, 'nm_info' => 'Brosur/Spanduk'],
				['id_info_nobel' => 7, 'nm_info' => 'Promosi Sekolah'],
				['id_info_nobel' => 8, 'nm_info' => 'Lainnya']
			];

			DB::table('info_nobel')->insert($data);
    }
}