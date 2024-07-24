<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableBankSoal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lmsk_bank_soal', function (Blueprint $table) {
            $table->increments('id');
            $table->char('jenis_soal', 2)->comment('PG: Pil ganda, ES: Essay');
            $table->uuid('id_dosen');
            $table->string('kode_mk', 15);
            $table->string('judul', 100);
            $table->text('soal');
            $table->string('jawaban_a')->nullable();
            $table->string('jawaban_b')->nullable();
            $table->string('jawaban_c')->nullable();
            $table->string('jawaban_d')->nullable();
            $table->string('jawaban_e')->nullable();
            $table->string('jawaban_benar',5)->nullable();
            $table->enum('format',['text','gambar']);

            $table->text('keyword')->nullable();
            $table->integer('min_kata')->nullable();
            $table->integer('max_kata')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_dosen');
            $table->index('kode_mk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lmsk_bank_soal');
    }
}
