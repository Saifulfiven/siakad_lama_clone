<?php

use Illuminate\Database\Seeder;

class optionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('options')->truncate();

		$data = [ 
				['id' => 'kunci_edit', 'value' => 0],
				['id' => 'nip_kabag_akademik', 'value' => '091100111'],
				['id' => 'kabag_akademik', 'value' => 'Anugrah, SS'],
				['id' => 'ketua', 'value' => 'Dr. Mashur Razak, SE.,MM'],
                ['id' => 'ketua_1', 'value' => 'Dr. Ahmad Firman, SE., M.Si'],
                ['id' => 'kunci_mhs_ekonomi', 'value' => 0],
                ['id' => 'kunci_mhs_pasca', 'value' => 0],
                ['id' => 'semester_berjalan_fakultas_1', 'value' => 20171],
				['id' => 'semester_berjalan_fakultas_2', 'value' => 20172]
		];

		DB::table('options')->insert($data);
    }
}
