<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableTelahKuis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lmsk_telah_kuis', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_peserta');
            $table->integer('id_kuis');
            $table->integer('sisa_waktu');
            $table->timestamps();
        
            $table->index('id_peserta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lmsk_telah_kuis');
    }
}
