<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MasterKonsentrasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konsentrasi', function (Blueprint $table) {
            $table->increments('id_konsentrasi');
            $table->string('nm_konsentrasi',50);
            $table->char('id_prodi',5);
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
        Schema::dropIfExists('konsentrasi');
    }
}
