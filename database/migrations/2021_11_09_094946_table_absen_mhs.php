<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableAbsenMhs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absen_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jdk');
            $table->integer('pertemuan_ke')->unsigned();
            $table->integer('waktu')->unsigned()->comment('Satuan menit');
            $table->timestamps(); // Updated at adalah waktu berakhir
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absen_mhs');
    }
}
