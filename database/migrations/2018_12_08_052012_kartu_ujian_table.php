<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KartuUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kartu_ujian', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_mhs_reg');
            $table->char('id_smt', 5);
            $table->enum('jenis', ['UTS','UAS']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kartu_ujian');
    }
}
