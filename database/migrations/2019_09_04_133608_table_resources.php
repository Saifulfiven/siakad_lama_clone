<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jadwal');
            $table->integer('id_resource')->comment('Materi, tugas, etc');
            $table->string('jenis', 10);
            $table->tinyInteger('pertemuan_ke');
            $table->tinyInteger('urutan');

            $table->index('id_jadwal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_resources');
    }
}
