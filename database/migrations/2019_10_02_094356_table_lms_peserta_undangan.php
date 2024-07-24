<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableLmsPesertaUndangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_peserta_undangan', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_peserta');
            $table->uuid('id_jadwal');
            $table->char('aktif', 1)->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_peserta_undangan');
    }
}
