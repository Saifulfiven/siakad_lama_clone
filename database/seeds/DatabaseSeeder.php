<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(AgamaSeeder::class);
        $this->call(JenisKeluarSeeder::class);
        // $this->call(JenisPembayaran::class);
        $this->call(Konsentrasi::class);
        $this->call(PekerjaanSeeder::class);
        $this->call(PenghasilanSeeder::class);
        $this->call(Prodi::class);
        $this->call(SemesterSeeder::class);
        $this->call(StatusMhsSeeder::class);
        $this->call(JenisTinggalSeeder::class);
        $this->call(AlatTransporSeeder::class);
        $this->call(jalurMasukSeeder::class);
        $this->call(jenisPendaftaranSeeder::class);
        $this->call(InfoNobelSeeder::class);
        $this->call(PendidikanSeeder::class);
        $this->call(userRolesSeeder::class);
        $this->call(optionsSeeder::class);
        $this->call(ruanganSeeder::class);
        $this->call(jamkuliahSeeder::class);
        $this->call(KurikulumSeeder::class);
        $this->call(fakultasSeeder::class);
        $this->call(smtAktifSeeder::class);
        $this->call(skalaNilaiSeeder::class);
    }
}

class UsersTableSeeder extends Seeder
{
	public function run()
	{
		App\User::truncate();

		$data = [
            [
    			'id' => 'd141dc0a-7e10-4759-b061-c85d5befa52a',
    			'nama' => 'Rahmat',
    	        'email' => 'admin@mail.com',
    	        'username' => 'admin',
    	        'password' => bcrypt('admin'),
                'level' => 'admin',
                'naik_smt' => '0',
    	        'remember_token' => str_random(10)
            ],
            [
                'id' => 'd143cc0a-5c10-4999-b161-c85d5befa53a',
                'nama' => 'Akademik S1',
                'email' => 'akademik@mail.com',
                'username' => 'akademiks1',
                'password' => bcrypt('akademik'),
                'level' => 'akademik',
                'naik_smt' => '1',
                'remember_token' => str_random(10)
            ],
            [
                'id' => 'v143cc0a-9w10-4999-b161-c85f5befa53a',
                'nama' => 'Akademik S2',
                'email' => 'akademiks2@mail.com',
                'username' => 'akademiks2',
                'password' => bcrypt('akademik'),
                'level' => 'akademik',
                'naik_smt' => '1',
                'remember_token' => str_random(10)
            ],
            [
                'id' => 'a293cc0a-5c10-4889-a161-r85d5befa53a',
                'nama' => 'Jurusan',
                'email' => 'jur@mail.com',
                'username' => 'jurusan',
                'password' => bcrypt('jurusan'),
                'level' => 'jurusan',
                'naik_smt' => '0',
                'remember_token' => str_random(10)
            ],
            [
                'id' => 'v293cacb-5c10-4877-a188-r85d5befa999',
                'nama' => 'Keungan',
                'email' => 'uang@mail.com',
                'username' => 'uang',
                'password' => bcrypt('uang123'),
                'level' => 'keuangan',
                'naik_smt' => '0',
                'remember_token' => str_random(10)
            ]
		];

		// App\User::create($data);
        DB::table('users')->insert($data);
	}
}