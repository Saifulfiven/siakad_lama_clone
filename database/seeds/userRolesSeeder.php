<?php

use Illuminate\Database\Seeder;

class userRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('user_roles')->truncate();

			$data = [ 
				['id_user' => 'd143cc0a-5c10-4999-b161-c85d5befa53a', 'id_prodi' => '61201'],
				['id_user' => 'd143cc0a-5c10-4999-b161-c85d5befa53a', 'id_prodi' => '62201'],
				['id_user' => 'v143cc0a-9w10-4999-b161-c85f5befa53a', 'id_prodi' => '61101'],
				['id_user' => 'a293cc0a-5c10-4889-a161-r85d5befa53a', 'id_prodi' => '61201'],
                ['id_user' => 'd141dc0a-7e10-4759-b061-c85d5befa52a', 'id_prodi' => '62201'],
                ['id_user' => 'd141dc0a-7e10-4759-b061-c85d5befa52a', 'id_prodi' => '61101'],
                ['id_user' => 'd141dc0a-7e10-4759-b061-c85d5befa52a', 'id_prodi' => '61201'],
                
			];

			DB::table('user_roles')->insert($data);
    }
}
