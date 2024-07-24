<?php

use Illuminate\Database\Seeder;

class MatakuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('matakuliah')->truncate();

			$data = [ 
				['id' => 'd141dc0a-7e10-4759-b061-c85d5befaxxx', 'id_prodi' => '61201', 'kode_mk' => 'MBB101', 'nm_mk' => 'Bahasa Indonesia', 'jenis_mk' => 'A', 'kelompok_mk' => 'F', 'sks_mk' => 3, 'sks_tm' => 3, 'sks_prak' => 0, 'sks_prak_lap' => 0, 'sks_sim' => 0],
				['id' => 'd141dc0a-7e10-4759-b061-c85d5befaxx1', 'id_prodi' => '62201', 'kode_mk' => 'MBB102', 'nm_mk' => 'Bahasa Inggris', 'jenis_mk' => 'A', 'kelompok_mk' => 'F', 'sks_mk' => 3, 'sks_tm' => 3, 'sks_prak' => 0, 'sks_prak_lap' => 0, 'sks_sim' => 0],
				['id' => 'd141dc0a-7e10-4759-b061-c85d5befaxx2', 'id_prodi' => '61201', 'kode_mk' => 'MBB103', 'nm_mk' => 'Pengantar Manajemen', 'jenis_mk' => 'A', 'kelompok_mk' => 'F', 'sks_mk' => 3, 'sks_tm' => 3, 'sks_prak' => 0, 'sks_prak_lap' => 0, 'sks_sim' => 0],
				['id' => 'd141dc0a-7e10-4759-b061-c85d5befaxx3', 'id_prodi' => '62201', 'kode_mk' => 'MBB104', 'nm_mk' => 'Pengantar Akuntansi', 'jenis_mk' => 'A', 'kelompok_mk' => 'F', 'sks_mk' => 3, 'sks_tm' => 3, 'sks_prak' => 0, 'sks_prak_lap' => 0, 'sks_sim' => 0]
			];

			DB::table('matakuliah')->insert($data);
    }
}
