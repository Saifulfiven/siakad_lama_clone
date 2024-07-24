<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FakultasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fakultas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nm_fakultas',50);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fakultas');
    }
}
