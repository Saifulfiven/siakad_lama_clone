<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKrsMhs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('krs_mhs', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_mhs_reg');
            $table->uuid('id_mkur');
            $table->char('id_smt', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('krs_mhs');
    }
}
