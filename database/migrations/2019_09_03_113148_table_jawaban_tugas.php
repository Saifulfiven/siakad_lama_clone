<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableJawabanTugas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('lms_jawaban_tugas', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_peserta')->comment('id_mhs_reg');
            $table->integer('id_tugas');
            $table->string('file')->nullable();
            $table->text('jawaban')->nullable();
            $table->dateTime('tgl_kumpul')->nullable();;
            $table->float('nilai', 5,2)->nullable();
            $table->text('comment')->nullable();
            $table->integer('attempt')->nullable();
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
        Schema::dropIfExists('lms_jawaban_tugas');
    }
}
