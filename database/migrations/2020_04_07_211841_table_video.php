<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_video', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_jadwal');
            $table->uuid('id_dosen');
            $table->string('judul', 100);
            $table->string('file', 200)->nullable();
            $table->string('video_id', 50)->nullable();
            $table->enum('uploaded', ['y','n']);
            $table->enum('siap', ['n','y']);
            $table->text('ket')->nullable();
            $table->timestamps();

            $table->index('id_jadwal');
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
        Schema::dropIfExists('lms_video');
    }
}
