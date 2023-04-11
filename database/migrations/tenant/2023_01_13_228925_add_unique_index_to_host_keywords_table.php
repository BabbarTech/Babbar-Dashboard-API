<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('hosts_keywords', function (Blueprint $table) {

            // Remove duplicate
            DB::statement('
                DELETE L
                FROM hosts_keywords L
                INNER JOIN hosts_keywords R
                     ON L.host_id = R.host_id
                    AND L.keyword_id = R.keyword_id
                    AND L.url_id = R.url_id
                    AND L.source_date = R.source_date
                WHERE L.id > R.id
            ');

            $table->unique([
                'host_id',
                'keyword_id',
                'url_id',
                'source_date',
            ], 'host_kw_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hosts_keywords', function (Blueprint $table) {
            $table->dropUnique('host_kw_unique');
        });
    }
};
