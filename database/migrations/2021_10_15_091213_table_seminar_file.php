<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableSeminarFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seminar_file', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_seminar');
            $table->string('file');
            $table->string('jenis_file', 30)->nullable();
            $table->string('ket', 255)->nullable();

            $table->index('id_seminar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seminar_file');
    }
}
