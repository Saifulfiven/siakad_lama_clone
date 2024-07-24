<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableDosenKegiatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dosen_kegiatan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_kegiatan', 100);
            $table->uuid('id_dosen');
            $table->integer('id_kategori');
            $table->date('tgl_kegiatan');
            $table->integer('tahun');
            $table->integer('smt');
            $table->string('file');
            $table->timestamps();

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
        Schema::dropIfExists('dosen_kegiatan');
    }
}
