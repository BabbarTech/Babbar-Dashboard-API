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

use App\Models\Guide;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class GuideRepository
{
    protected Guide $model;

    public function __construct(Guide $model)
    {
        $this->model = $model;
    }

    public function getGuidesQuery(): Builder
    {
        return DB::connection('tenant')
            ->table('guides as g')
            ->select([
                'g.id',
                'g.yourtextguru_guide_id',
                'g.keyword_id',
                'g.status',
                'k.keywords',
                'k.bks',
            ])
            ->join('keywords as k', 'k.id', '=', 'g.keyword_id');
    }

    public function getGuideStatsQuery(int $yourtextguruGuideId): Builder
    {
        $guide = Guide::findByGuideId($yourtextguruGuideId);

        return DB::connection('tenant')
            ->table('serps as s')
            ->select([
                's.rank',
                'u.url',
                'o.page_trust',
                'o.page_value',
                'o.semantic_value',
                'o.babbar_authority_score',
                'c.score as soseo_t',
                'c.danger as dseo_t',
                'gs.soseo_all_content as soseo_y',
                'gs.dseo_all_content as dseo_y',
                'gs.position as yourtextguru_rank',
            ])
            ->where('s.keyword_id', $guide->keyword_id)
            ->where('s.rank', '<=', 10)
            ->join('urls as u', 'u.id', '=', 's.url_id')
            ->leftJoin('url_overviews as o', 'o.url_id', '=', 's.url_id')
            ->leftJoin('guide_serps as gs', function ($join) use ($guide) {
                $join->on('gs.url_id', '=', 's.url_id')
                    ->where('gs.guide_id', $guide->id);
            })
            ->leftJoin('page_analyzes as pa', 'pa.url_id', '=', 's.url_id')
            ->leftJoin('guide_checks as c', function ($join) use ($guide) {
                $join->on('c.page_analyze_id', '=', 'pa.id')
                    ->where('c.guide_id', $guide->id);
            });
    }
}
