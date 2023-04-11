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

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Benchmark;
use App\Models\Host;
use App\Models\Project;
use App\Services\BenchmarkService\Benchmarks\RankingFactorCompetitorsKeywordsBenchmark;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class KeywordsRankController extends Controller
{
    public function __invoke(Request $request, Project $project): View
    {
        $rankMax = intval(config('benchmarks.keywords-ranks.max', 100));
        $nbPages = ceil($rankMax / 10);

        $datasetSources = Benchmark::with('benchmarkable')
            ->whereIn('type', [
                RankingFactorCompetitorsKeywordsBenchmark::class,
            ])
            ->where('status', StatusEnum::DONE)
            ->get()
            ->map(function ($benchmark) use ($project) {

                /** @var Host $host */
                $host = $benchmark->benchmarkable;

                return [
                    'id' => $host->id,
                    'name' => $host->hostname,
                    'api' => route('api.v1.ranking-factors.keywords-ranks-distribution', [
                        'project' => $project,
                        'host_id' => $host->id
                    ]),
                    'complete' => Arr::has($benchmark->params ?? [], 'keywordRankMax'),
                ];
            })
            ->sortByDesc('complete')
            ->unique('name')
            ->values();

        $perPageChartConfig = collect([
            'xAxis' => [
                'data' => collect(range(1, $nbPages))
                    ->map(function ($page) {
                        return 'Page ' . $page;
                    }),
                'name' => 'Pages',
                'axisLabel' => [
                    'align' => 'center',
                ]
            ],
            'yAxis' => [
                'show' => true,
                'name' => 'Nb of keywords',
            ],
        ]);

        $perRankChartConfig = collect([
            'tooltip' => [
                'trigger' => 'axis',
            ],
            'xAxis' => [
                'data' => collect(range(1, $rankMax)),
                'name' => 'SERP Positions',
                'axisLabel' => [
                    'show' => true,
                    //'formatter' => 'pos {value}',
                ],
            ],
            'yAxis' => [
                'show' => true,
                'name' => 'Nb of keywords',
            ],
        ]);

        return view('resources.ranking-factors.keywords-rank', [
            'project' => $project,
            'datasetSources' => $datasetSources,
            'perPageChartConfig' => $perPageChartConfig,
            'perRankChartConfig' => $perRankChartConfig,
            'rankMax' => $rankMax,
            'nbPages' => $nbPages,
        ]);
    }
}
