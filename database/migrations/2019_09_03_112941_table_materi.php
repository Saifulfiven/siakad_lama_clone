<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableMateri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_materi', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jadwal');
            $table->uuid('id_bank_materi');
            $table->uuid('id_dosen');
            $table->timestamps();

            $table->index('id_jadwal');
            $table->index('id_bank_materi');
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
        Schema::dropIfExists('lms_materi');
    }
}
