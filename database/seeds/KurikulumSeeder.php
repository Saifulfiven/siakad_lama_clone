<?php

use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('kurikulum')->truncate();

			$data = [ 
				['id' => 'd141dc0a-7e10-4759-b061-c85d5befaz4z', 'nm_kurikulum' => 'S1 Manajemen 2017', 'id_prodi' => '61201', 'jml_sks_lulus' => 144, 'jml_sks_wajib' => 144, 'jml_sks_pilihan' => 0, 'aktif' => 1],
                ['id' => 'd141dc0a-7e10-4759-b061-c85d5befav4v', 'nm_kurikulum' => 'S1 Akuntansi 2017', 'id_prodi' => '62201', 'jml_sks_lulus' => 144, 'jml_sks_wajib' => 144, 'jml_sks_pilihan' => 0, 'aktif' => 1],
				['id' => 'd141dc0a-7e10-4759-b061-c85d5befay3y', 'nm_kurikulum' => 'S2 Manajemen 2017', 'id_prodi' => '61101', 'jml_sks_lulus' => 44, 'jml_sks_wajib' => 44, 'jml_sks_pilihan' => 0, 'aktif' => 1]
			];

			DB::table('kurikulum')->insert($data);
    }
}
