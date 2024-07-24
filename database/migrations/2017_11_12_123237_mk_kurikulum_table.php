<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MkKurikulumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mk_kurikulum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_kurikulum');
            $table->uuid('id_mk');
            $table->char('periode',1)->comment('1: ganjil, 2:genap');
            $table->tinyInteger('smt');

            $table->index('id_kurikulum');
            $table->index('id_mk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mk_kurikulum');
    }
}
