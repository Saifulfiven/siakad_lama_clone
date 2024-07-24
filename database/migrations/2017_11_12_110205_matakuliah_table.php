<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MatakuliahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matakuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mk_terganti')->nullable();
            $table->char('id_prodi',5);
            $table->string('kode_mk',15);
            $table->string('nm_mk',100);
            $table->char('ujian_akhir',1)->nullable()->comment('P : proposal, H: hasil, S: skripsi');
            $table->char('jenis_mk',1)->comment('A:wajib,B:pilihan,E:skripsi/tesis');
            $table->char('kelompok_mk',1)->nullable();
            $table->integer('id_konsentrasi')->nullable();
            $table->tinyInteger('sks_mk');
            $table->tinyInteger('sks_tm')->nullable();
            $table->tinyInteger('sks_prak')->nullable();
            $table->tinyInteger('sks_prak_lap')->nullable();
            $table->tinyInteger('sks_sim')->nullable();
            $table->enum('a_sap',['0','1']);
            $table->enum('a_silabus',['0','1']);
            $table->enum('a_bahan_ajar',['0','1']);
            $table->enum('acara_praktek',['0','1']);
            $table->enum('a_diktat',['0','1']);
            $table->date('tgl_mulai_efektif')->nullable();
            $table->date('tgl_akhir_efektif')->nullable();
            $table->integer('id_jenis_bayar')->nullable();
            $table->timestamps();

            $table->index('id_prodi');
            $table->index('kode_mk');
            $table->index('nm_mk');
            $table->index('id_konsentrasi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matakuliah');
    }
}
