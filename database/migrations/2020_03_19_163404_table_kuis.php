<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKuis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lmsk_kuis', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jadwal');
            $table->uuid('id_dosen');
            $table->string('judul', 100);
            $table->dateTime('tgl_mulai');
            $table->dateTime('tgl_tutup');
            $table->integer('waktu_kerja')->nullable();
            $table->enum('jenis',['kuis','ujian']);
            $table->text('ket')->nullable();
            $table->char('acak', 1);
            $table->string('tampilan', 6)->comment('single, all');
            $table->timestamps();

            $table->index('id_jadwal');
            $table->index('id_dosen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lmsk_kuis');
    }
}
