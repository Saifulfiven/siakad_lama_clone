<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableDokumenMahasiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dokumen_mahasiswa', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_mhs');
            $table->string('judul', 50)->nullable();
            $table->string('file', 150);
            $table->timestamps();

            $table->index('id_mhs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dokumen_mahasiswa');
    }
}
