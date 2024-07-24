<?php

use Illuminate\Database\Seeder;

class JenisPembayaran extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('jenis_pembayaran')->truncate();

			$data = [ 
				['id_jns_pembayaran' => '1', 'id_prodi' => '61201', 'ket' => 'Magang II', 'nominal' => 300000],
                ['id_jns_pembayaran' => '2', 'id_prodi' => '61201', 'ket' => 'Magang I', 'nominal' => 300000],
                ['id_jns_pembayaran' => '3', 'id_prodi' => '61201', 'ket' => 'BPP Manajemen', 'nominal' => 5000000],
                ['id_jns_pembayaran' => '4', 'id_prodi' => '62201', 'ket' => 'BPP Akuntansi', 'nominal' => 5000000],
				['id_jns_pembayaran' => '5', 'id_prodi' => '61101', 'ket' => 'BPP Pasca', 'nominal' => 4000000]
			];

			DB::table('jenis_pembayaran')->insert($data);    
		}
}