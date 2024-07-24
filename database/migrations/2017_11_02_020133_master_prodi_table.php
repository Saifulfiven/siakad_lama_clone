<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MasterProdiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prodi', function (Blueprint $table) {
            $table->char('id_prodi',5)->primary();
            $table->integer('id_fakultas');
            $table->string('nm_prodi',25);
            $table->char('jenjang',2);
            $table->char('kode_nim',2);
            $table->string('gelar',30);
            $table->string('sk_akreditasi',50);
            $table->string('ketua_prodi', 50);
            $table->string('nip_ketua_prodi', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prodi');
    }
}
