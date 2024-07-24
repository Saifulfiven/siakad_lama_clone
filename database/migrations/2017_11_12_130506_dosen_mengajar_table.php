<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DosenMengajarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dosen_mengajar', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jdk');
            $table->uuid('id_dosen');
            $table->tinyInteger('jml_tm');
            $table->tinyInteger('jml_real');

            $table->index('id_jdk');
            $table->index('id_dosen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dosen_mengajar');
    }
}
