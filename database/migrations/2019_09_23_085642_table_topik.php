<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableTopik extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_topik', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jadwal');
            $table->uuid('id_dosen');
            $table->uuid('creator')->comment('id_mhs_reg or id_dosen');
            $table->string('judul', 100);
            $table->text('konten');
            $table->char('is_closed', 1)->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('lms_topik');
    }
}
