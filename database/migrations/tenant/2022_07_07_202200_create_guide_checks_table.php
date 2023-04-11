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
        Schema::create('guide_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guide_id')->index();
            $table->unsignedBigInteger('page_analyze_id')->index();
            $table->unsignedSmallInteger('score')->nullable();
            $table->unsignedSmallInteger('danger')->nullable();
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
        Schema::dropIfExists('guide_checks');
    }
};
