<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TablePembayaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt',5);
            $table->uuid('id_mhs_reg');
            $table->integer('id_jns_pembayaran')->comment('99 : Semester pendek');
            $table->date('tgl_bayar');
            $table->string('jenis_bayar',7);
            $table->char('id_bank',1)->nullable();
            $table->integer('jml_bayar');
            $table->string('ket',100)->nullable();
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
        Schema::dropIfExists('pembayaran');
    }
}
