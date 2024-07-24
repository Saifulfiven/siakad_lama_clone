<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BrivaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('briva', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_smt',5);
            $table->string('nim',12);
            $table->string('cust_code', 13);
            $table->string('nama', 50);
            $table->integer('jml');
            $table->string('ket',100)->nullable();
            $table->integer('jenis_bayar')->default(0);
            $table->dateTime('exp_date');
            $table->char('status', 1)->default('N')->comment('N: Belum, Y:selesai, B: Batal');
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
        Schema::dropIfExists('kartu_ujian');
    }
}
