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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('domain');
            $table->string('hostname');
            $table->unsignedBigInteger('host_id')->index();
            $table->text('url')->nullable();
            $table->string('tenant_key')->unique();
            $table->string('database')->unique();
            $table->string('serp', 5)
                ->default(\App\Enums\SerpEnum::fr_FR->value);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
