<?php

use Illuminate\Database\Seeder;

class jalurMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('jalur_masuk')->truncate();

			$data = [ 
				['id_jalur_masuk' => 1, 'nm_jalur_masuk'	=> 'SBMPTN'],
				['id_jalur_masuk' => 2, 'nm_jalur_masuk'	=> 'SNMPTN'],
				['id_jalur_masuk' => 3, 'nm_jalur_masuk' => 'PMDK'],
				['id_jalur_masuk' => 4, 'nm_jalur_masuk' => 'Prestasi'],
				['id_jalur_masuk' => 5, 'nm_jalur_masuk' => 'Seleksi Mandiri PTN'],	
				['id_jalur_masuk' => 6, 'nm_jalur_masuk' => 'Seleksi Mandiri PTS'],
				['id_jalur_masuk' => 7, 'nm_jalur_masuk' => 'Ujian Masuk Bersama PTN (UMB-PT)'],
				['id_jalur_masuk' => 8, 'nm_jalur_masuk' => 'Ujian Masuk Bersama PTS (UMB-PTS)'],
				['id_jalur_masuk' => 9, 'nm_jalur_masuk' => 'Program Internasional'],
				['id_jalur_masuk' => 11, 'nm_jalur_masuk' => 'Program Kerjasama Perusahaan/Institusi/Pemerintah']
			];

			DB::table('jalur_masuk')->insert($data);
    }
}	