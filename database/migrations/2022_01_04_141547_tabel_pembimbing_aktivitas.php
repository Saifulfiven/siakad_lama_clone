<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TabelPembimbingAktivitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('km_pembimbing_aktivitas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aktivitas');
            $table->uuid('id_dosen');
            $table->integer('id_jenis_pembimbing');
            $table->integer('pembimbing_ke')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('km_pembimbing_aktivitas');
    }
}
