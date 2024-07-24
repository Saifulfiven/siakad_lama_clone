<?php

use Illuminate\Database\Seeder;

class jenisPendaftaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('jenis_pendaftaran')->truncate();

			$data = [ 
				['id_jns_pendaftaran' => 2, 'nm_jns_pendaftaran' => 'Pindahan'],
				['id_jns_pendaftaran' => 1, 'nm_jns_pendaftaran' => 'Peserta didik baru'],
				['id_jns_pendaftaran' => 11, 'nm_jns_pendaftaran' => 'Alih Jenjang'],
				['id_jns_pendaftaran' => 12, 'nm_jns_pendaftaran' => 'Lintas Jalur']
			];

			DB::table('jenis_pendaftaran')->insert($data);
    }
}