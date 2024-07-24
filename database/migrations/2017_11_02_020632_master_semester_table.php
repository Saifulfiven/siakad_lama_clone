<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MasterSemesterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->char('id_smt',5)->primary();
            $table->string('nm_smt',20);
            $table->char('smt',1)->comment('1 : ganjil, 2: genap');
            $table->char('aktif',1)->comment('1: aktif, 0: non-aktif');
            // $table->char('buka',1)->comment('1: Tampilkan periode pada pemilihan periode aktif');
        });
    }

    /**
     * Reverse the migrations.
     *     * @return void

     */
    public function down()
    {
        Schema::dropIfExists('semester');
    }
}
