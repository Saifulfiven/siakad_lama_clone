<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MasterSkalaNilaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skala_nilai', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_prodi',5);
            $table->string('nilai_huruf',2);
            $table->float('nilai_indeks',3,2);
            $table->string('range_nilai', 10);
            $table->float('range_atas', 4,1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skala_nilai');
    }
}
