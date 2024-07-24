<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKuisSoal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lmsk_kuis_soal', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_kuis');
            $table->integer('id_bank_soal');

            $table->index('id_kuis');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lmsk_kuis_soal');
    }
}
