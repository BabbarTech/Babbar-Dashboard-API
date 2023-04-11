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
        Schema::table('keywords', function (Blueprint $table) {

            DB::statement('
                DELETE FROM hosts_keywords
                WHERE
                    keyword_id IN (
                    SELECT
                        id
                    FROM (
                        SELECT
                            id,
                            ROW_NUMBER() OVER (
                                PARTITION BY keywords
                                ORDER BY id) AS row_num
                        FROM
                            keywords

                    ) t
                    WHERE row_num > 1
                )'
            );


            DB::statement('
                DELETE FROM keywords
                WHERE
                    id IN (
                    SELECT
                        id
                    FROM (
                        SELECT
                            id,
                            ROW_NUMBER() OVER (
                                PARTITION BY keywords
                                ORDER BY id) AS row_num
                        FROM
                            keywords

                    ) t
                    WHERE row_num > 1
                )'
            );


            $table->unique('keywords', 'keywords_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropUnique('keywords_unique');
        });
    }
};
