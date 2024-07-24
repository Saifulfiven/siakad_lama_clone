<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKuesJadwal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kues_jadwal', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('ket',['MID','FINAL']);
            $table->char('id_prodi',5);
            $table->char('id_smt',5);
            $table->char('aktif',1)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kues_jadwal');
    }
}
