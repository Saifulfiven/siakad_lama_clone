<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PesertaUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peserta_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_mhs_reg');
            $table->integer('id_jdu');

            $table->index('id_jdu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peserta_ujian');
    }
}
