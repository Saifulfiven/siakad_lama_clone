<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TabelAktivitasKm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('km_aktivitas', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt', 5);
            $table->integer('id_jenis_aktivitas');
            $table->text('judul_aktivitas');
            $table->string('lokasi', 100)->nullable();
            $table->string('no_sk')->nullable();
            $table->date('tgl_sk')->nullable();
            $table->enum('jenis_anggota', ['0','1'])->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('km_aktivitas');
    }
}
