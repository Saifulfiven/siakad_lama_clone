<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableSeminarValidasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seminar_validasi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_seminar')->unsigned();
            $table->uuid('id_dosen');
            $table->tinyInteger('disetujui', 1)->default(0);

            $table->index('id_seminar');
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
        Schema::dropIfExists('seminar_validasi');
    }
}
