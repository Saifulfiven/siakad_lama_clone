<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableMateriPasca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materi_kuliah_pasca', function (Blueprint $table) {
            $table->increments('id');
            $table->string('judul', 100);
            $table->string('kode_mk', 20);
            $table->string('file_materi', 200);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materi_kuliah_pasca');
    }
}
