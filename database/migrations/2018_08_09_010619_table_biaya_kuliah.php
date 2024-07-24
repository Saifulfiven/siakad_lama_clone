<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableBiayaKuliah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biaya_kuliah', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_prodi',5);
            $table->char('tahun',4);
            $table->integer('spp')->nullable();
            $table->integer('bpp')->nullable();
            $table->integer('seragam')->nullable();
            $table->integer('lainnya')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biaya_kuliah');
    }
}
