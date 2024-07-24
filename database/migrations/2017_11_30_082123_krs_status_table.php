<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KrsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('krs_status', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt',5);
            $table->uuid('id_mhs_reg');
            $table->enum('valid',['0','1'])->comment('persetujuan dosen wali. Tidak dipakai jika tidak ada dosen wali');
            $table->enum('status_krs',['0','1'])->comment('1: krs terkunci, 0:krs terbuka');
            $table->enum('jenis',['KULIAH','SP']);
            $table->enum('jalur', ['manual','online']);

            $table->index('id_smt');
            $table->index('id_mhs_reg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('krs_status');
    }
}
