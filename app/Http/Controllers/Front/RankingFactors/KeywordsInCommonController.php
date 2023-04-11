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

namespace App\Http\Controllers\Front\RankingFactors;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonKeywordCompetitorHostResource;
use App\Models\Project;
use App\Repositories\Actions\BabbarFetchHostKeywordsAction;
use App\Repositories\Actions\ExportToCsvAction;
use App\Repositories\Actions\YtgFetchGuideAction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeywordsInCommonController extends Controller
{
    public function __invoke(Request $request, Project $project): View
    {
        $mainDatasetPayload = collect([
            'filters' => [
                'hostname' => [
                    'include' => null,
                    'exclude' => null,
                ],
                'nb_kw_top20' => [
                    'min' => null,
                    'max' => 19999,
                ],
                'nb_keywords_in_common' => [
                    'min' => null,
                    'max' => null,
                ],
                'similar_score_percent' => [
                    'min' => 1,
                    'max' => null,
                ],
            ],
            'orderBy' => '-nb_keywords_in_common'
        ]);

        $detailDatasetPayload = collect([
            'filters' => [
                'keywords' => [
                    'include' => null,
                    'exclude' => null,
                ],
                'bks' => [
                    'min' => null,
                    'max' => null,
                ],
                'current_rank' => [
                    'min' => null,
                    'max' => null,
                ],
                'competitor_rank' => [
                    'min' => null,
                    'max' => null,
                ],
                'competitor_has_better_rank' => null,
                'group_keywords' => null,
            ],
            'orderBy' => 'competitor.rank'
        ]);

        $rankMax = intval(config('benchmarks.keywords-ranks.max', 100));
        $mainActions = collect([
            (new BabbarFetchHostKeywordsAction())
                ->setTitle(___('actions.babbar_fetch_host_keywords') . ' / TOP 20'),
            (new BabbarFetchHostKeywordsAction())
                ->setTitle(___('actions.babbar_fetch_host_keywords') . ' / TOP ' . $rankMax)
                ->setParams([
                    'keywordRankMax' => $rankMax,
                ]),
            (new ExportToCsvAction())
                ->setParams([
                    'transformer' => CommonKeywordCompetitorHostResource::class,
                ]),
        ]);

        $keywordsActions = collect([
            new YtgFetchGuideAction(),
            new ExportToCsvAction(),
        ]);

        return view('resources.ranking-factors.keywords-in-common', [
            'project' => $project,
            'mainDatasetPayload' => $mainDatasetPayload,
            'detailDatasetPayload' => $detailDatasetPayload,
            'mainActions' => $mainActions,
            'keywordsActions' => $keywordsActions,
        ]);
    }
}
