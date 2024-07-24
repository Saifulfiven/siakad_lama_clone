<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableTopikJawaban extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_topik_jawaban', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_topik');
            $table->char('people', 3)->default('dsn');
            $table->uuid('id_user')->comment('id_mhs_reg or id_dosen');
            $table->text('konten');
            $table->tinyInteger('is_deleted')->default(0);
            $table->timestamps();

            $table->index('id_topik');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lms_topik_jawaban');
    }
}
