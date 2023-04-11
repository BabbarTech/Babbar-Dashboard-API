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
        Schema::create('benchmark_step_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('benchmark_step_id')->index();
            $table->string('batch_id')->index();
            $table->string('status')
                ->default(\App\Enums\StatusEnum::PENDING->value);
            $table->text('error')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedBigInteger('batch_finished_jobs')->default(0);
            $table->unsignedBigInteger('batch_total_jobs')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
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
        Schema::dropIfExists('benchmark_step_batches');
    }
};
