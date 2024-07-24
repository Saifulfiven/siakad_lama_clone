<?php

use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('semester')->truncate();

			$data = [ 
					['id_smt' => '20101', 'nm_smt' => '2010/2011 Ganjil', 'smt' => 1,],
					['id_smt' => '20102', 'nm_smt' => '2010/2011 Genap', 'smt' => 2,],

					['id_smt' => '20111', 'nm_smt' => '2011/2012 Ganjil', 'smt' => 1,],
					['id_smt' => '20112', 'nm_smt' => '2011/2012 Genap', 'smt' => 2,],

					['id_smt' => '20121', 'nm_smt' => '2012/2013 Ganjil', 'smt' => 1,],
					['id_smt' => '20122', 'nm_smt' => '2012/2013 Genap', 'smt' => 2,],

					['id_smt' => '20131', 'nm_smt' => '2013/2014 Ganjil', 'smt' => 1,],
					['id_smt' => '20132', 'nm_smt' => '2013/2014 Genap', 'smt' => 2,],

					['id_smt' => '20141', 'nm_smt' => '2014/2015 Ganjil', 'smt' => 1,],
					['id_smt' => '20142', 'nm_smt' => '2014/2015 Genap', 'smt' => 2,],

					['id_smt' => '20151', 'nm_smt' => '2015/2016 Ganjil', 'smt' => 1,],
					['id_smt' => '20152', 'nm_smt' => '2015/2016 Genap', 'smt' => 2,],

					['id_smt' => '20161', 'nm_smt' => '2016/2017 Ganjil', 'smt' => 1,],
					['id_smt' => '20162', 'nm_smt' => '2016/2017 Genap', 'smt' => 2,],

					['id_smt' => '20171', 'nm_smt' => '2017/2018 Ganjil', 'smt' => 1,],
					['id_smt' => '20172', 'nm_smt' => '2017/2018 Genap', 'smt' => 2,],

					['id_smt' => '20181', 'nm_smt' => '2018/2019 Ganjil', 'smt' => 1,],
					['id_smt' => '20182', 'nm_smt' => '2018/2019 Genap', 'smt' => 2,],
			];

			DB::table('semester')->insert($data);
    }
}