<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MahasiswaRegTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mahasiswa_reg', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('id_prodi',5);
            $table->integer('id_konsentrasi')->nullable();
            $table->uuid('id_mhs');
            $table->char('jenis_daftar',2);
            $table->enum('jam_kuliah',['PAGI','SIANG','MALAM']);
            $table->integer('jalur_masuk')->nullable();
            $table->string('nim',12)->unique();
            $table->date('tgl_daftar');
            $table->uuid('dosen_pa')->nullable();
            $table->uuid('id_kurikulum');
            $table->integer('id_jenis_keluar')->default(0);
            $table->date('tgl_keluar')->nullable();
            $table->string('ket_keluar',100)->nullable();
            $table->char('semester_mulai',5);
            $table->char('semester_keluar',5)->nullable();
            $table->char('jalur_skripsi',1)->nullable();
            $table->string('judul_skripsi')->nullable();
            $table->date('awal_bimbingan')->nullable();
            $table->date('akhir_bimbingan')->nullable();
            $table->string('sk_yudisium',30)->nullable();
            $table->date('tgl_sk_yudisium')->nullable();
            $table->float('ipk')->nullable();
            $table->string('seri_ijazah',40)->nullable();
            $table->string('pin',40)->nullable();
            $table->date('tgl_ijazah')->nullable();
            $table->string('nm_pt_asal',50)->nullable();
            $table->string('nm_prodi_asal',50)->nullable();
            $table->uuid('id_maba')->nullable();
            $table->string('kelas', 11)->default('REGULER');
            $table->string('kode_kelas', 11)->nullable();
            $table->string('jurnal_file', 25)->nullable();
            $table->timestamps('updated_jurnal')->nullable();
            $table->text('pesan_revisi')->nullable();
            $table->string('jurnal_approved', 1)->default('0');
            $table->string('jurnal_published', 1)->default('0');
            $table->integer('id_jenis_pembiayaan')->nullable();
            $table->integer('biaya_masuk')->nullable();
            $table->integer('biaya_kuliah')->nullable();
            $table->integer('id_pt_asal')->nullable();
            $table->integer('id_prodi_asal')->nullable();
            $table->string('feeder_status', 1)->nullable();
            $table->string('feeder_ket', 100)->nullable();
            $table->timestamps();

            $table->index('id_prodi');
            $table->index('nim');
            $table->index('id_jenis_keluar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mahasiswa_reg');
    }
}
