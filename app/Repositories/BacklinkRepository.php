<?php
/*
 * Babbar Dashboard API
 *
 * Licensed under the MIT license. See LICENSE file in the project root for details.
 *
 * @copyright Copyright (c) 2023 Babbar
 * @license   https://opensource.org/license/mit/ MIT License
 *
 */

namespace App\Repositories;

use App\Models\Backlink;
use App\Models\Host;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;

class BacklinkRepository
{
    protected Backlink $model;

    public function __construct(Backlink $model)
    {
        $this->model = $model;
    }

    public function getHostBacklinksQuery(Host $host): Builder
    {
        $latestUrlOverview = DB::connection('tenant')
            ->table('url_overviews')
            ->select([
                '*',
                DB::raw('ROW_NUMBER() OVER (PARTITION BY url_id ORDER BY source_date DESC) AS row_num')
            ]);

        $sourceBacklinks = DB::connection('tenant')
            ->table('backlinks')
            ->select([
                'target_url_id',
                DB::raw('count(target_url_id) as source_nb_backlinks'),
            ])->groupBy('target_url_id');

        return DB::connection('tenant')
            ->table('backlinks as bl')
            ->select([
                'bl.*',
                's.url as source_url',
                't.url as target_url',
                'o.source_date',
                'o.page_value',
                'o.page_trust',
                'o.semantic_value',
                'o.babbar_authority_score',
                DB::raw('count(k.id) as source_nb_keywords_in_top20'),
                'sbl.source_nb_backlinks'
            ])
            ->leftJoinSub($latestUrlOverview, 'o', function ($join) {
                $join->on('bl.source_url_id', '=', 'o.url_id')
                    ->where('row_num', 1);
            })
            ->leftJoinSub($sourceBacklinks, 'sbl', function ($join) {
                $join->on('bl.source_url_id', '=', 'sbl.target_url_id');
            })
            ->join('urls as s', 'bl.source_url_id', '=', 's.id')
            ->join('urls as t', 'bl.target_url_id', '=', 't.id')
            ->leftJoin('hosts_keywords as k', 'bl.source_url_id', '=', 'k.url_id')
            ->where('t.host_id', $host->getKey())
            ->groupBy('bl.source_url_id');
    }


    public function getSourceBacklinksQuery(Backlink $backlink): Builder
    {
        return DB::connection('tenant')
            ->table('backlinks as bl')
            ->select([
                'bl.*',
                's.url as source_url',
                't.url as target_url',
                //'o.source_date',
                //'o.page_value',
                //'o.page_trust',
                //'o.semantic_value',
                //'o.babbar_authority_score',
                //DB::raw('count(k.id) as source_nb_keywords_in_top20')
            ])
            ->join('urls as s', 'bl.source_url_id', '=', 's.id')
            ->join('urls as t', 'bl.target_url_id', '=', 't.id')
            //->leftJoin('hosts_keywords as k', 'bl.source_url_id', '=', 'k.url_id')
            ->where('bl.target_url_id', $backlink->source_url_id)
            //->groupBy('bl.source_url_id')
        ;
    }
}
