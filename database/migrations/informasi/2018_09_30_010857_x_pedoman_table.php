<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class XPedomanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_pedoman_akademik', function (Blueprint $table) {
            $table->increments('id');
            $table->string('judul');
            $table->text('konten');
            $table->integer('urutan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('x_pedoman_akademik');
    }
}
