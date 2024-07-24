<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTabelBimbinganMhs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bimbingan_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_mhs_reg');
            $table->string('jenis',1)->comment('P:proposal, H:hasil, S:skripsi');
            $table->string('id_smt', 5);
            $table->string('file');
            $table->integer('versi')->default(1)->unsigned();
            $table->char('pembimbing_1',1)->default('0')->comment('0:belum, 1:selesai');
            $table->char('pembimbing_2',1)->default('0')->comment('0:belum, 1:selesai');
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
        Schema::dropIfExists('bimbingan_mhs');
    }
}
