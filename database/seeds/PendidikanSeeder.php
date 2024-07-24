<?php

use Illuminate\Database\Seeder;

class PendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('pendidikan')->truncate();

			$data = [ 
				['id_pdk' => 1, 'nm_pdk' =>	'PAUD'],
				['id_pdk' => 2, 'nm_pdk' =>	'TK / sederajat'],
				['id_pdk' => 3, 'nm_pdk' =>	'Putus SD'],
				['id_pdk' => 4, 'nm_pdk' =>	'SD / sederajat'],
				['id_pdk' => 5, 'nm_pdk' =>	'SMP / sederajat'],
				['id_pdk' => 6, 'nm_pdk' =>	'SMA / sederajat'],
				['id_pdk' => 7, 'nm_pdk' =>	'Paket A'],
				['id_pdk' => 8, 'nm_pdk' =>	'Paket B'],
				['id_pdk' => 9, 'nm_pdk' =>	'Paket C'],
				['id_pdk' => 20, 'nm_pdk' =>	'D1'],
				['id_pdk' => 21, 'nm_pdk' =>	'D2'],
				['id_pdk' => 22, 'nm_pdk' =>	'D3'],
				['id_pdk' => 23, 'nm_pdk' =>	'D4'],
				['id_pdk' => 30, 'nm_pdk' =>	'S1'],
				['id_pdk' => 35, 'nm_pdk' =>	'S2'],
				['id_pdk' => 40, 'nm_pdk' =>	'S3'],
				['id_pdk' => 90, 'nm_pdk' =>	'Non formal'],
				['id_pdk' => 91, 'nm_pdk' =>	'Informal'],
				['id_pdk' => 32, 'nm_pdk' =>	'Sp-1'],
				['id_pdk' => 37, 'nm_pdk' =>	'Sp-2'],
				['id_pdk' => 31, 'nm_pdk' =>	'Profesi'],
				['id_pdk' => 36, 'nm_pdk' =>	'S2 Terapan'],
				['id_pdk' => 41, 'nm_pdk' =>	'S3 Terapan'],
				['id_pdk' => 99, 'nm_pdk' =>	'Lainnya']
			];

			DB::table('pendidikan')->insert($data);
    }
}
