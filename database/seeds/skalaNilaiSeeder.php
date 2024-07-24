<?php

use Illuminate\Database\Seeder;

class skalaNilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('skala_nilai')->truncate();

			$data = [ 
				['id_prodi' => '61201', 'nilai_huruf' => 'A', 'nilai_indeks' => 4.00],
				['id_prodi' => '61201', 'nilai_huruf' => 'B', 'nilai_indeks' => 3.00],
				['id_prodi' => '61201', 'nilai_huruf' => 'C', 'nilai_indeks' => 2.00],
				['id_prodi' => '61201', 'nilai_huruf' => 'D', 'nilai_indeks' => 1.00],
				['id_prodi' => '61201', 'nilai_huruf' => 'E', 'nilai_indeks' => 0.00],
				['id_prodi' => '62201', 'nilai_huruf' => 'A', 'nilai_indeks' => 4.00],
				['id_prodi' => '62201', 'nilai_huruf' => 'B', 'nilai_indeks' => 3.00],
				['id_prodi' => '62201', 'nilai_huruf' => 'C', 'nilai_indeks' => 2.00],
				['id_prodi' => '62201', 'nilai_huruf' => 'D', 'nilai_indeks' => 1.00],
				['id_prodi' => '62201', 'nilai_huruf' => 'E', 'nilai_indeks' => 0.00],
				['id_prodi' => '61101', 'nilai_huruf' => 'A', 'nilai_indeks' => 4.00],
				['id_prodi' => '61101', 'nilai_huruf' => 'A-', 'nilai_indeks' => 3.75],
				['id_prodi' => '61101', 'nilai_huruf' => 'B+', 'nilai_indeks' => 3.50],
				['id_prodi' => '61101', 'nilai_huruf' => 'B', 'nilai_indeks' => 3.00],
				['id_prodi' => '61101', 'nilai_huruf' => 'B-', 'nilai_indeks' => 2.75],
				['id_prodi' => '61101', 'nilai_huruf' => 'C+', 'nilai_indeks' => 2.50],
				['id_prodi' => '61101', 'nilai_huruf' => 'C', 'nilai_indeks' => 2.00],
				['id_prodi' => '61101', 'nilai_huruf' => 'C-', 'nilai_indeks' => 1.75],
				['id_prodi' => '61101', 'nilai_huruf' => 'D', 'nilai_indeks' => 1.00],
				['id_prodi' => '61101', 'nilai_huruf' => 'E', 'nilai_indeks' => 0.00],
			];

			DB::table('skala_nilai')->insert($data);
    }
}
