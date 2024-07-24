<?php

use Illuminate\Database\Seeder;

class JenisTinggalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('jenis_tinggal')->truncate();

			$data = [ 
				['id_jns_tinggal' => '1', 'nm_jns_tinggal' => 'Bersama orang tua'],
				['id_jns_tinggal' => '2', 'nm_jns_tinggal' => 'Wali'],
				['id_jns_tinggal' => '3', 'nm_jns_tinggal' => 'Kost'],
				['id_jns_tinggal' => '4', 'nm_jns_tinggal' => 'Asrama'],
				['id_jns_tinggal' => '5', 'nm_jns_tinggal' => 'Panti asuhan'],
				['id_jns_tinggal' => '99', 'nm_jns_tinggal' => 'Lainnya']
			];

			DB::table('jenis_tinggal')->insert($data);
    }
}