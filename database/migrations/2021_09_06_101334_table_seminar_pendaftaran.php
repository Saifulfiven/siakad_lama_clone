<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableSeminarPendaftaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seminar_pendaftaran', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_mhs_reg');
            $table->char('id_smt', 5);
            $table->enum('jenis',['P','H','S'])->comment('P:proposal, H:hasil, S:skripsi/tesis');
            $table->char('validasi_bauk', 1)->default(0);
            $table->char('validasi_ndc', 1)->default(0);
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
        Schema::dropIfExists('seminar_pendaftaran');
    }
}