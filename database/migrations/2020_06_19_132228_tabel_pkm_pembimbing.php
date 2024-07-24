<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TabelPkmPembimbing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pkm_pembimbing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_pkm');
            $table->uuid('id_dosen');
            $table->tinyInteger('pembimbing_ke');

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
        Schema::dropIfExists('pkm_pembimbing');
    }
}
