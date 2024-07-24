<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JadwalKuliahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_kuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mkur');
            $table->uuid('id_mk');
            $table->char('id_prodi',5);
            $table->char('id_smt',5);
            $table->string('kode_kls',5);
            $table->string('ket_kls',5)->nullable();
            $table->string('ruangan',10)->nullable();
            $table->date('tgl')->nullable();
            $table->integer('id_jam')->nullable();
            $table->tinyInteger('hari')->nullable();
            $table->tinyInteger('kapasitas_kls')->nullable();
            $table->char('jenis',1)->default(1)->comment('1: jadwal kuliah, 2: jadwal antara');
            $table->char('kelas_khusus',1)->default(1)->comment('1: Khusu Muslim, 2: Non Muslim');
            $table->timestamps();

            $table->index('id_prodi');
            $table->index('id_smt');
            $table->index('hari');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_kuliah');
    }
}
