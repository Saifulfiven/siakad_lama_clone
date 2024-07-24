<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JadwalPertemuanS2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_pertemuan_s2', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jdk');
            $table->date('tgl');
            $table->string('jam', 13);
            $table->tinyInteger('pertemuan_ke');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_pertemuan_s2');
    }
}
