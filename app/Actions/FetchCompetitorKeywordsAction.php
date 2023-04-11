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

namespace App\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchCompetitorKeywordsRequest;
use App\Models\Host;
use App\Models\Project;
use App\Repositories\HostRepository;
use App\Services\BenchmarkService\Benchmarks\RankingFactorCompetitorsKeywordsBenchmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class FetchCompetitorKeywordsAction extends Controller
{
    protected HostRepository $repository;

    public function __invoke(FetchCompetitorKeywordsRequest $request, Project $project): RedirectResponse
    {
        /** @var array $validated */
        $validated = $request->validated();

        $host = Host::firstOrCreate([
            'hostname' => Arr::get($validated, 'hostname'),
        ]);

        $attributes = $additionalAttributes = [
            'type' => RankingFactorCompetitorsKeywordsBenchmark::class,
        ];

        $keywordRankMax = 20;
        if (Arr::get($validated, 'type') === 'full') {
            $keywordRankMax = intval(config('benchmarks.keywords-ranks.max', 100));
            $attributes['params->keywordRankMax'] = $keywordRankMax;
            $additionalAttributes['params'] = [
                'keywordRankMax' => $keywordRankMax,
            ];
        }

        $benchmark = $host->benchmarks()->firstOrCreate($attributes, $additionalAttributes);

        if ($benchmark->wasRecentlyCreated) {
            $benchmark->process();
            return redirect()
                ->back()
                ->withSuccess(__('benchmark.fetch-hostname-keywords', [
                    'hostname' => $host->hostname,
                    'top' => $keywordRankMax,
                    'url' => route('projects.benchmarks.show', [$project, $benchmark]),
                ]));
        }

        return redirect()
            ->back()
            ->withDanger(__('benchmark.fetch-hostname-keywords.already-processed', [
                'hostname' => $host->hostname,
                'top' => $keywordRankMax,
                'url' => route('projects.benchmarks.show', [$project, $benchmark]),
            ]));
    }
}
