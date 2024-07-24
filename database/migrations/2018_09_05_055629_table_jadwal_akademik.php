<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableJadwalAkademik extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_akademik', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_fakultas');
            $table->date('awal_pembayaran');
            $table->date('akhir_pembayaran');
            $table->date('awal_krs');
            $table->date('akhir_krs');
            $table->date('awal_kuliah');
            $table->tinyInteger('input_nilai_sp')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_akademik');
    }
}
