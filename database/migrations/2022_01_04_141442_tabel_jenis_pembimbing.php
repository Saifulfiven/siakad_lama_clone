<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TabelJenisPembimbing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('km_jenis_pembimbing', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nm_kategori', 255);
            $table->integer('induk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('km_jenis_pembimbing');
    }
}
