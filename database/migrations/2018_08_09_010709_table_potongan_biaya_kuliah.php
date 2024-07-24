<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePotonganBiayaKuliah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('potongan_biaya_kuliah', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('id_mhs_reg');
            $table->integer('potongan');
            $table->enum('jenis_potongan', ['BPP','SPP']);
            $table->string('ket',100);

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
        Schema::dropIfExists('potongan_biaya_kuliah');
    }
}
