<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKuesKomponen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kues_komponen', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_prodi',5);
            $table->string('judul', 100);
            $table->tinyInteger('urutan');
            $table->enum('jenis',['essay','pg']);
            $table->char('aktif',1)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kues_komponen');
    }
}
