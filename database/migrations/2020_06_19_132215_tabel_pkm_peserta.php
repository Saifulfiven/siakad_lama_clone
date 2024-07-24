<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TabelPkmPeserta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkm_peserta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pkm');
            $table->uuid('id_mhs_reg');
            $table->string('jabatan', 20);

            $table->index('id_pkm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pkm_peserta');
    }
}
