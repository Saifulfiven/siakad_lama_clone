<?php

use Illuminate\Database\Seeder;

class jamkuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('jam_kuliah')->truncate();

			$data = [ 
					['id_prodi' => '61201', 'jam_masuk' => '08:30', 'jam_keluar' => '10:30', 'ket' => 'PAGI'],
					['id_prodi' => '61201', 'jam_masuk' => '10:30', 'jam_keluar' => '12:30', 'ket' => 'PAGI'],
					['id_prodi' => '61201', 'jam_masuk' => '12:30', 'jam_keluar' => '14:30', 'ket' => 'SIANG'],
					['id_prodi' => '61201', 'jam_masuk' => '14:30', 'jam_keluar' => '16:30', 'ket' => 'SIANG'],
					['id_prodi' => '61201', 'jam_masuk' => '16:30', 'jam_keluar' => '18:30', 'ket' => 'MALAM'],
					['id_prodi' => '61201', 'jam_masuk' => '19:00', 'jam_keluar' => '21:00', 'ket' => 'MALAM'],
					['id_prodi' => '62201', 'jam_masuk' => '08:30', 'jam_keluar' => '10:30', 'ket' => 'PAGI'],
					['id_prodi' => '62201', 'jam_masuk' => '10:30', 'jam_keluar' => '12:30', 'ket' => 'PAGI'],
					['id_prodi' => '62201', 'jam_masuk' => '12:30', 'jam_keluar' => '14:30', 'ket' => 'SIANG'],
					['id_prodi' => '62201', 'jam_masuk' => '14:30', 'jam_keluar' => '16:30', 'ket' => 'SIANG'],
					['id_prodi' => '61101', 'jam_masuk' => '08:30', 'jam_keluar' => '10:30', 'ket' => 'PAGI'],
					['id_prodi' => '61101', 'jam_masuk' => '10:30', 'jam_keluar' => '12:30', 'ket' => 'PAGI'],
					['id_prodi' => '61101', 'jam_masuk' => '12:30', 'jam_keluar' => '14:30', 'ket' => 'SIANG'],
					['id_prodi' => '61101', 'jam_masuk' => '14:30', 'jam_keluar' => '16:30', 'ket' => 'SIANG'],
					['id_prodi' => '61101', 'jam_masuk' => '16:30', 'jam_keluar' => '18:30', 'ket' => 'MALAM'],
					['id_prodi' => '62201', 'jam_masuk' => '16:30', 'jam_keluar' => '18:30', 'ket' => 'MALAM'],
					['id_prodi' => '62201', 'jam_masuk' => '19:00', 'jam_keluar' => '18:30', 'ket' => 'MALAM'],
			];

			DB::table('jam_kuliah')->insert($data);
    }
}
