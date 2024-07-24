<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTabelBimbinganDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bimbingan_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_bimbingan_mhs')->unsigned();
            $table->uuid('id_dosen');
            $table->string('jabatan_penguji', 15)->comment('KETUA: pbb 1, SEKRETARIS: pbb 2');
            $table->string('sub_bahasan');
            $table->text('komentar');
            $table->string('file');
            $table->date('tgl_bimbingan');
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
        Schema::dropIfExists('bimbingan_detail');
    }
}
