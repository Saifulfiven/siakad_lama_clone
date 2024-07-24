<?php

use Illuminate\Database\Seeder;

class ruanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('ruangan')->truncate();

			$data = [
					['id' => '304','nm_ruangan' => '304'],
					['id' => '311','nm_ruangan' => '311'],
					['id' => '401','nm_ruangan' => '401'],
					['id' => '402','nm_ruangan' => '402'],
					['id' => '403','nm_ruangan' => '403'],
					['id' => '404','nm_ruangan' => '404'],
					['id' => '405','nm_ruangan' => '405'],
					['id' => '406','nm_ruangan' => '406'],
					['id' => '407','nm_ruangan' => '407'],
					['id' => '501','nm_ruangan' => '501'],
					['id' => '502','nm_ruangan' => '502'],
					['id' => '503','nm_ruangan' => '503'],
					['id' => '504','nm_ruangan' => '504'],
					['id' => '505','nm_ruangan' => '505'],
					['id' => '506','nm_ruangan' => '506'],
					['id' => '507','nm_ruangan' => '507'],
					['id' => '509','nm_ruangan' => '509'],
					['id' => '510','nm_ruangan' => '510'],
					['id' => '511','nm_ruangan' => '511'],
					['id' => 'LA','nm_ruangan' => 'Lab Akuntansi'],
					['id' => 'LB','nm_ruangan' => 'Lab Bahasa'],
					['id' => 'LK','nm_ruangan' => 'Lab Komputer'],
			];

			DB::table('ruangan')->insert($data);
    }
}
