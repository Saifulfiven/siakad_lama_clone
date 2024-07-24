<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableTugas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_tugas', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jadwal');
            $table->uuid('id_dosen');
            $table->string('judul');
            $table->string('file')->nullable();
            $table->text('deskripsi')->nullable();
            $table->dateTime('mulai_berlaku')->nullable();
            $table->dateTime('tgl_berakhir')->nullable();
            $table->dateTime('tgl_tutup')->nullable();
            $table->string('jenis_pengiriman', 4)->default('file');
            $table->integer('min_teks')->nullable();
            $table->integer('max_teks')->nullable();
            $table->integer('max_file_upload')->nullable();
            $table->integer('max_attempt')->nullable();
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
        Schema::dropIfExists('lms_tugas');
    }
}
