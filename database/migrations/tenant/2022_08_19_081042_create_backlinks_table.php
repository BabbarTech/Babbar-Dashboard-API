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
        Schema::create('backlinks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('host_id')->index();
            $table->unsignedBigInteger('source_url_id')->index();
            $table->unsignedBigInteger('target_url_id')->index();
            $table->string('link_type');
            $table->text('link_text');
            $table->string('link_rels');
            $table->unsignedTinyInteger('induced_strength')->nullable();
            $table->string('induced_strength_confidence')->nullable();
            $table->string('language');
            $table->string('ip');
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
        Schema::dropIfExists('backlinks');
    }
};
