<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableNilaiSeminar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_seminar', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_penguji')->unsigned();
            $table->tinyInteger('kriteria_penilaian');
            $table->float('nilai', 5,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_seminar');
    }
}
