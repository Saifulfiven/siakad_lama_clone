<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableUjianAkhir extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ujian_akhir', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt', 5)->nullable();
            $table->uuid('id_mhs_reg');
            $table->date('tgl_ujian')->nullable();
            $table->string('pukul', 30)->nullable();
            $table->string('ruangan', 10)->nullable();
            $table->string('judul_tmp')->nullable();
            $table->enum('jenis',['P','H','S']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ujian_akhir');
    }
}
