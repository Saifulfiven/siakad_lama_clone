<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NilaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_mhs_reg');
            $table->uuid('id_jdk');
            $table->tinyInteger('semester_mk')->comment('untuk urutan mk di transkrip dll');
            $table->float('nil_kehadiran', 5,2)->nullable();
            $table->float('nil_tugas', 5,2)->nullable();
            $table->float('nil_mid', 5,2)->nullable();
            $table->float('nil_final', 5,2)->nullable();
            $table->float('nilai_angka',5,2)->nullable();
            $table->char('nilai_huruf',2)->default('');
            $table->float('nilai_indeks',3,2)->nullable();
            $table->tinyInteger('a_1',1)->default(0);
            $table->tinyInteger('a_2',1)->default(0);
            $table->tinyInteger('a_3',1)->default(0);
            $table->tinyInteger('a_4',1)->default(0);
            $table->tinyInteger('a_5',1)->default(0);
            $table->tinyInteger('a_6',1)->default(0);
            $table->tinyInteger('a_7',1)->default(0);
            $table->tinyInteger('a_8',1)->default(0);
            $table->tinyInteger('a_9',1)->default(0);
            $table->tinyInteger('a_10',1)->default(0);
            $table->tinyInteger('a_11',1)->default(0);
            $table->tinyInteger('a_12',1)->default(0);
            $table->tinyInteger('a_13',1)->default(0);
            $table->tinyInteger('a_14',1)->default(0);

            $table->index('id_mhs_reg');
            $table->index('id_jdk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai');
    }
}
