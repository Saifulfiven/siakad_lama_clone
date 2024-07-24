<?php

use Illuminate\Database\Seeder;

class JenisKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('jenis_keluar')->truncate();

			$data = [ 
					['id_jns_keluar' => '0', 'ket_keluar' => 'AKTIF'],
					['id_jns_keluar' => '1', 'ket_keluar' => 'Lulus'],
					['id_jns_keluar' => '2', 'ket_keluar' => 'Mutasi'],
					['id_jns_keluar' => '3', 'ket_keluar' => 'Dikeluarkan'],
					['id_jns_keluar' => '4', 'ket_keluar' => 'Mengundurkan Diri'],
					['id_jns_keluar' => '5', 'ket_keluar' => 'Putus Sekolah'],
					['id_jns_keluar' => '6', 'ket_keluar' => 'Wafat'],
					['id_jns_keluar' => '7', 'ket_keluar' => 'Hilang'],
					['id_jns_keluar' => '8', 'ket_keluar' => 'Alih Fungsi'],
					['id_jns_keluar' => '9', 'ket_keluar' => 'Pensiun'],
					['id_jns_keluar' => '99', 'ket_keluar' => 'Lainnya']
			];

			DB::table('jenis_keluar')->insert($data);
    }
}
