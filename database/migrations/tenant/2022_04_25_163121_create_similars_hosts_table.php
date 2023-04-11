<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('similar_hosts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('host_id')->index();
            $table->unsignedBigInteger('similar_host_id')->index();
            $table->float('score', 3, 2, true)->index();
            $table->string('lang', 2);
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
        Schema::dropIfExists('similar_hosts');
    }
};
