<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KrsTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('krs_tmp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mhs_reg');
            $table->char('id_smt',5);
            $table->uuid('id_jdk');

            $table->index('id_mhs_reg');
            $table->index('id_smt');
            $table->index('id_jdk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('krs_tmp');
    }
}
