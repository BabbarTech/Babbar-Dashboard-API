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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('keyword_id');
            $table->unsignedBigInteger('yourtextguru_guide_id')->nullable();
            $table->string('lang', 5);
            $table->string('status')
                ->default(\App\Enums\StatusEnum::PENDING->value);
            $table->unsignedBigInteger('group_id')->nullable();
            $table->json('grammes1')->nullable();
            $table->json('grammes2')->nullable();
            $table->json('grammes3')->nullable();
            $table->json('entities')->nullable();
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
        Schema::dropIfExists('guides');
    }
};
