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
        Schema::create('serps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('keyword_id')->index();
            $table->unsignedSmallInteger('rank')->nullable();
            $table->unsignedBigInteger('url_id')->index()->nullable();
            $table->unsignedBigInteger('host_id')->index();
            $table->date('source_date')->index()->nullable();
            $table->date('fetch_date')->index()->nullable();
            $table->text('title')->nullable();
            $table->text('breadcrumb')->nullable();
            $table->text('snippet')->nullable();
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
        Schema::dropIfExists('serps');
    }
};
