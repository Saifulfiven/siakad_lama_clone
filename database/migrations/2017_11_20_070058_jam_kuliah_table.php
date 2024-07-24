<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JamKuliahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jam_kuliah', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_prodi');
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->string('ket',5)->comment('PAGI,SIANG,MALAM');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jam_kuliah');
    }
}
