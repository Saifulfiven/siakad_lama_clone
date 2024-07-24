<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AktivitasKuliah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_kuliah', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt', 5);
            $table->uuid('id_mhs_reg');
            $table->float('ips',3,2);
            $table->tinyInteger('sks_smt')->comment('total sks yang diambil semester ini');
            $table->float('ipk',3,2);
            $table->tinyInteger('sks_total')->comment('total sks lulus selama kuliah');
            $table->char('status_mhs',1);

            $table->index('id_mhs_reg');
            $table->index('id_smt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktivitas_kuliah');
    }
}
