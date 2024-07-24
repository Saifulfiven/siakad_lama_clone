<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class XKalenderAkademikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_kalender_akademik', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('deskripsi');
            $table->string('tanggal');
            $table->char('kategori',1);
            $table->integer('urutan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('x_kalender_akademik');
    }
}
