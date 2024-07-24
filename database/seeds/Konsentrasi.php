<?php

use Illuminate\Database\Seeder;

class Konsentrasi extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('konsentrasi')->truncate();

			$data = [ 
					['nm_konsentrasi' => 'Ekonomi dan Bisnis Internasional', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Manajemen Bisnis Pariwisata & Perhotelan', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Manajemen Bisnis Telekomunikasi & Informatika', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Bisnis Perbankan & Keuangan Mikro', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Manajemen Keuangan dan Perbankan Syariah', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Manajemen Keuangan dan Perbankan', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Manajemen Bisnis Investasi', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Manajemen Keuangan dan Microfinance', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Bisnis & Perdagangan Internasional', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Bisnis Retail', 'id_prodi' => '61201'],
					['nm_konsentrasi' => 'Akuntansi Korporasi', 'id_prodi' => '62201'],
					['nm_konsentrasi' => 'Akuntansi Sektor Publik & Keuangan Daerah', 'id_prodi' => '62201'],
					['nm_konsentrasi' => 'Bisnis Perbankan & Keuangan Mikro', 'id_prodi' => '62201'],
					['nm_konsentrasi' => 'Teknologi Sistem Informasi Akuntansi', 'id_prodi' => '62201'],
					['nm_konsentrasi' => 'Manajemen Bisnis & Kewirausahaan', 'id_prodi' => '61101'],
					['nm_konsentrasi' => 'Manajemen Keuangan', 'id_prodi' => '61101'],
					['nm_konsentrasi' => 'Manajemen Pemasaran', 'id_prodi' => '61101'],
					['nm_konsentrasi' => 'Manajemen Pemerintahan & Keuangan Daerah', 'id_prodi' => '61101'],
					['nm_konsentrasi' => 'Manajemen Pendidikan', 'id_prodi' => '61101'],
					['nm_konsentrasi' => 'Manajemen Sumber Daya Manusia', 'id_prodi' => '61101'],
					['nm_konsentrasi' => 'Manajemen Keperawatan', 'id_prodi' => '61101'],
					['nm_konsentrasi' => 'Bisnis Properti', 'id_prodi' => '61101'],
			];

			DB::table('konsentrasi')->insert($data);
    }
}


