<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableLmsCatatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_catatan', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_dosen');
            $table->uuid('id_jadwal');
            $table->text('konten');
            $table->timestamps();

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
        Schema::dropIfExists('lms_catatan');
    }
}
