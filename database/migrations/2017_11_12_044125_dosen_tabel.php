<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DosenTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_user');
            $table->char('id_prodi', 5);
            $table->string('nidn',15)->nullable();
            $table->string('nm_dosen',50);
            $table->string('gelar_depan',20)->nullable();
            $table->string('gelar_belakang',20)->nullable();
            $table->char('pendidikan_tertinggi',2)->nullable();
            $table->string('nip',18)->nullable();
            $table->string('tempat_lahir',30)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->integer('id_agama')->nullable();
            $table->enum('jenkel',['L','P']);
            $table->char('jenis_dosen',3)->comment('DTY,DPK,DLB');
            $table->char('jabatan_fungsional', 1)->nullable();
            $table->string('golongan', 20)->nullable();
            $table->char('aktivitas', 1)->nullable();
            $table->string('alamat')->nullable();
            $table->string('hp',15)->nullable();
            $table->string('foto',100)->nullable();
            $table->enum('aktif',['1','0']);
            $table->string('ttd', 100)->nullable();
            $table->timestamps();

            $table->index('nidn');
            $table->index('nm_dosen');
            $table->index('aktif');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dosen');
    }
}
