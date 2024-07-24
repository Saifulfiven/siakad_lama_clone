<?php

use Illuminate\Database\Seeder;

class StatusMhsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('status_mhs')->truncate();

			$data = [ 
				['id_stat_mhs' => 'A', 'nm_stat_mhs' => 'AKTIF'],	
				['id_stat_mhs' => 'C', 'nm_stat_mhs' => 'CUTI'],	
				['id_stat_mhs' => 'D', 'nm_stat_mhs' => 'SEDANG DOUBLE DEGREE'],
				['id_stat_mhs' => 'N', 'nm_stat_mhs' => 'NON-AKTIF'],
			];

			DB::table('status_mhs')->insert($data);
    }
}