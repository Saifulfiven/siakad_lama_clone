<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePenguji extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penguji', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt', 5)->nullable();;
            $table->uuid('id_mhs_reg');
            $table->uuid('id_dosen');
            $table->string('jabatan',15)->comment('Ketua,Sekretaris,anggota,anggota_2');
            $table->float('nilai',4,2)->nullable();
            $table->enum('jenis',['P','H','S'])->comment('P:proposal,H:hasil,S:skripsi');

            $table->index('id_mhs_reg');
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
        Schema::dropIfExists('penguji');
    }
}
