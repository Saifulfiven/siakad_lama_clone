<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class XPengumumanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_pengumuman', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->string('kategori',10);
            $table->text('deskripsi');
            $table->text('konten');
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
        Schema::dropIfExists('x_pengumuman');
    }
}
