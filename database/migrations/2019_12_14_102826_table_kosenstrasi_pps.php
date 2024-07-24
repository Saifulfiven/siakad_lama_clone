<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKosenstrasiPps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konsentrasi_pps', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt', 5);
            $table->uuid('id_mhs_reg');
            $table->integer('id_konsentrasi');
            $table->string('kelas', 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('konsentrasi_pps');
    }
}
