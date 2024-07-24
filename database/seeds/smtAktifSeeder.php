<?php

use Illuminate\Database\Seeder;

class smtAktifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('semester_aktif')->truncate();

			$data = [ 
					['id_smt' => '20171', 'id_fakultas' => 1],
					['id_smt' => '20172', 'id_fakultas' => 2]
			];

			DB::table('semester_aktif')->insert($data);
    }
}