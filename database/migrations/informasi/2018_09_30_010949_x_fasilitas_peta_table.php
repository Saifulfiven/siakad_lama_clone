<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class XFasilitasPetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_fasilitas_peta', function (Blueprint $table) {
            $table->increments('id');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('jenis',['fasilitas','peta']);
            $table->integer('urutan');
            $table->string('gambar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('x_fasilitas_peta');
    }
}
