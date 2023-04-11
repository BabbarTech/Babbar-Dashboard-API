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
        Schema::create('hosts_keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('host_id')->index();
            $table->unsignedBigInteger('keyword_id')->index();
            $table->unsignedSmallInteger('rank')->nullable();
            $table->unsignedSmallInteger('subrank')->nullable();
            $table->unsignedBigInteger('url_id')->index()->nullable();
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
        Schema::dropIfExists('hosts_keywords');
    }
};
