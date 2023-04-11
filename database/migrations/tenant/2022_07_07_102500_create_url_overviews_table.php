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
        Schema::create('url_overviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('url_id')->index();
            $table->date('source_date')->index()->nullable();
            $table->unsignedTinyInteger('page_value')->nullable();
            $table->unsignedTinyInteger('page_trust')->nullable();
            $table->unsignedTinyInteger('semantic_value')->nullable();
            $table->unsignedTinyInteger('babbar_authority_score')->nullable();
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
        Schema::dropIfExists('url_overviews');
    }
};
