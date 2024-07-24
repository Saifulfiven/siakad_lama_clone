<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JadwalUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_ujian', function (Blueprint $table) {
            $table->Increments('id');
            $table->uuid('id_jdk');
            $table->tinyInteger('jml_peserta');
            $table->date('tgl_ujian');
            $table->char('hari',1);
            $table->time('jam_masuk');
            $table->time('jam_selesai');
            $table->string('id_ruangan',3);
            $table->integer('id_pengawas');
            $table->enum('jenis_ujian',['UTS','UAS']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_ujian');
    }
}
