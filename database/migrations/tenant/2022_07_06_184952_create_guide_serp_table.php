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
        Schema::create('guide_serps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guide_id')->index();
            $table->unsignedTinyInteger('position');
            $table->unsignedBigInteger('url_id');
            $table->unsignedSmallInteger('soseo_all_content');
            $table->unsignedSmallInteger('dseo_all_content');
            $table->date('source_date')->index()->nullable();
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
        Schema::dropIfExists('guide_serps');
    }
};
