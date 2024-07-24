<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableLogNilai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_nilai', function (Blueprint $table) {
            $table->increments('id');
            $table->string('matakuliah',30);
            $table->string('dosen', 50);
            $table->char('id_smt',5);
            $table->char('id_prodi', 5);
            $table->uuid('id_user');
            $table->string('nm_pengubah', 30);
            $table->string('nil_awal', 2)->nullable();
            $table->string('nil_akhir', 2)->nullable();
            $table->string('level', 10);
            $table->char('ip',15)->nullable();
            $table->string('komputer', 150)->nullable();
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
        Schema::dropIfExists('log_nilai');
    }
}
