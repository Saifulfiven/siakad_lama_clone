<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MahasiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_user');
            $table->string('nm_mhs',50);
            $table->string('gelar_depan',20);
            $table->string('gelar_belakang',20);
            $table->enum('jenkel',['L','P']);
            $table->string('nik',16);
            $table->string('nisn',15)->nullable();
            $table->string('npwp',25)->nullable();
            $table->string('tempat_lahir',40);
            $table->date('tgl_lahir');
            $table->integer('id_agama');
            $table->string('alamat',100)->nullable();
            $table->string('dusun',20)->nullable();
            $table->string('des_kel',30);
            $table->string('rt',2)->nullable();
            $table->string('rw',2)->nullable();
            $table->char('id_wil',8);
            $table->string('pos',5)->nullable();
            $table->string('hp',15)->nullable();
            $table->string('email',50)->nullable();
            $table->char('kewarganegaraan',2)->nullable();
            $table->string('nm_sekolah',50)->nullable();
            $table->char('tahun_lulus_sekolah',4)->nullable();
            $table->string('nik_ibu',16)->nullable();
            $table->string('nm_ibu',50)->nullable();
            $table->date('tgl_lahir_ibu')->nullable();
            $table->integer('id_pdk_ibu')->nullable();
            $table->integer('id_pekerjaan_ibu')->nullable();
            $table->integer('id_penghasilan_ibu')->nullable();
            $table->string('hp_ibu',15)->nullable();
            $table->string('nik_ayah',16)->nullable();
            $table->string('nm_ayah',50)->nullable();
            $table->date('tgl_lahir_ayah')->nullable();
            $table->integer('id_pdk_ayah')->nullable();
            $table->integer('id_pekerjaan_ayah')->nullable();
            $table->string('hp_ayah',15)->nullable();
            $table->integer('id_penghasilan_ayah')->nullable();
            $table->string('alamat_ortu',50)->nullable();
            $table->string('nm_wali',30)->nullable();
            $table->date('tgl_lahir_wali')->nullable();
            $table->integer('id_pdk_wali')->nullable();
            $table->integer('id_pekerjaan_wali')->nullable();
            $table->integer('id_penghasilan_wali')->nullable();
            $table->string('hp_wali',15)->nullable();
            $table->integer('jenis_tinggal')->nullable();
            $table->integer('alat_transpor')->nullable();
            $table->string('foto_mahasiswa',12)->nullable();
            $table->string('foto_lulus',12)->nullable();
            $table->integer('id_info_nobel')->nullable();
            $table->timestamps();

            $table->index('nm_mhs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mahasiswa');
    }
}
