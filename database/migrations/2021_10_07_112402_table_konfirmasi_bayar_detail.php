<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableKonfirmasiBayarDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konfirmasi_bayar_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_konfirmasi')->unsigned();
            $table->string('file');
            $table->integer('id_jns_pembayaran')->unsigned();
            $table->char('status', 1)->default('0');

            $table->index('id_konfirmasi');
            $table->index('id_jns_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('konfirmasi_bayar_detail');
    }
}
