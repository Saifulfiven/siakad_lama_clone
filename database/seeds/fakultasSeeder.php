<?php

use Illuminate\Database\Seeder;

class fakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('fakultas')->truncate();

			$data = [ 
				['nm_fakultas' => 'Ekonomi'],
				['nm_fakultas' => 'Pascasarjan Nobel']
			];

			DB::table('fakultas')->insert($data);
    }
}
