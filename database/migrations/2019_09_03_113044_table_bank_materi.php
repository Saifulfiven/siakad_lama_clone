<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableBankMateri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_bank_materi', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_dosen');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file');
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
        Schema::dropIfExists('lms_bank_materi');
    }
}
