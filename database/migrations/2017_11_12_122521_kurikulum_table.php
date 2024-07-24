<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KurikulumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nm_kurikulum',30);
            $table->char('mulai_berlaku', 5)->comment('20151 etc');
            $table->char('id_prodi',5);
            $table->integer('jml_sks_lulus')->nullable();
            $table->integer('jml_sks_wajib')->nullable();
            $table->integer('jml_sks_pilihan')->nullable();
            $table->enum('aktif',['1','0']);
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
        Schema::dropIfExists('kurikulum');
    }
}
