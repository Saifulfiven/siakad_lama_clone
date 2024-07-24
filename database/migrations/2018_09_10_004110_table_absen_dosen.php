<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableAbsenDosen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absen_dosen', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_dosen')->comment('mengatasi jika lebih dari 1 dosen mengajar');
            $table->uuid('id_jdk');
            $table->tinyInteger('pertemuan');
            $table->char('masuk', 1)->default(0)->comment('masuk : 1, 0 : tidak masuk');
            $table->date('tgl')->nullable();
            $table->char('jam_masuk', 5)->nullable();
            $table->char('jam_keluar', 5)->nullable();
            $table->text('pokok_bahasan')->nullable();

            $table->index('id_jdk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absen_dosen');
    }
}
