<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKuisHasil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lmsk_kuis_hasil', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_peserta')->comment('id_mhs_reg');
            $table->integer('id_kuis_soal');
            $table->text('jawaban')->nullable();
            $table->string('penilaian', 100);
            $table->text('komentar_pengajar')->nullable();
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
        Schema::dropIfExists('lmsk_kuis_hasil');
    }
}
