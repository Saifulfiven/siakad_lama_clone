<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKuesHasil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kues_hasil', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_kues');
            $table->integer('id_komponen_isi');
            $table->integer('penilaian')->default(0)->comment('jika pilihan ganda');
            $table->text('penilaian_text')->nullable()->comment('Jika essay');
            $table->char('approve_komen')->default(0)->comment('1: disetujui, 0: tidak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kues_hasil');
    }
}
