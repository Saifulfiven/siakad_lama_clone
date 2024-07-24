<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableNilaiTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_transfer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mhs_reg');
            $table->uuid('id_mk');
            $table->string('kode_mk_asal',30);
            $table->string('nm_mk_asal',100);
            $table->tinyInteger('sks_asal');
            $table->char('nilai_huruf_asal',2);
            $table->char('nilai_huruf_diakui',2);
            $table->float('nilai_indeks',3,2);
            $table->string('feeder_status', 1)->nullable();
            $table->string('feeder_ket', 100)->nullable();

            $table->index('id_mhs_reg');
            $table->index('id_mk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_transfer');
    }
}
