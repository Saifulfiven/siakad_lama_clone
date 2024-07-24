<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKuesKomponenIsi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kues_komponen_isi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_komponen');
            $table->text('pertanyaan');
            $table->char('aktif',1)->default(1);
            $table->tinyInteger('urutan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kues_komponen_isi');
    }
}
