<?php

use Illuminate\Database\Seeder;

class PekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('pekerjaan')->truncate();

			$data = [ 
					['id_pekerjaan' => 1, 'nm_pekerjaan' => 'Tidak bekerja'],
					['id_pekerjaan' => 2, 'nm_pekerjaan' => 'Nelayan'],
					['id_pekerjaan' => 3, 'nm_pekerjaan' => 'Petani'],
					['id_pekerjaan' => 4, 'nm_pekerjaan' => 'Peternak'],
					['id_pekerjaan' => 5, 'nm_pekerjaan' => 'PNS/TNI/Polri'],
					['id_pekerjaan' => 6, 'nm_pekerjaan' => 'Karyawan Swasta'],
					['id_pekerjaan' => 7, 'nm_pekerjaan' => 'Pedagang Kecil'],
					['id_pekerjaan' => 8, 'nm_pekerjaan' => 'Pedagang Besar'],
					['id_pekerjaan' => 9, 'nm_pekerjaan' => 'Wiraswasta'],
					['id_pekerjaan' => 10, 'nm_pekerjaan' => 'Wirausaha'],
					['id_pekerjaan' => 11, 'nm_pekerjaan' => 'Buruh'],
					['id_pekerjaan' => 12, 'nm_pekerjaan' => 'Pensiunan'],
					['id_pekerjaan' => 98, 'nm_pekerjaan' => 'Sudah Meninggal'],
					['id_pekerjaan' => 99, 'nm_pekerjaan' => 'Lainnya']
			];

			DB::table('pekerjaan')->insert($data);
    }
}

