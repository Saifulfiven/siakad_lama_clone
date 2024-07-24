<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TabelAnggotaAktivitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('km_anggota_aktivitas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aktivitas');
            $table->uuid('id_mhs_reg');
            $table->enum('jenis_peran', ['1','2','3']); // 1: Ketua, 2: Anggota, 3: Personal
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('km_anggota_aktivitas');
    }
}
