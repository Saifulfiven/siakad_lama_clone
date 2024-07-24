<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TabelUserActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('user_id');
            $table->string('nama', 30);
            $table->string('level', 20)->nullable();
            $table->string('aktivitas', 50);
            $table->string('url');
            $table->string('ip', 20);
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
        Schema::dropIfExists('user_activity');
    }
}
