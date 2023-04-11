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
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->string('hostname')->index();
            $table->unsignedBigInteger('nb_kw_pos_1_10')->index()->nullable();
            $table->unsignedBigInteger('nb_kw_pos_11_20')->index()->nullable();
            $table->unsignedBigInteger('nb_kw_pos_21plus')->index()->nullable();
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
        Schema::dropIfExists('hosts');
    }
};
