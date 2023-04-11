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

namespace App\Services\BenchmarkService\Benchmarks;

use App\Jobs\BenchmarkSteps\BabbarFetchHostKeywordsJob;
use App\Models\BenchmarkStep;
use App\Models\Host;
use App\Repositories\HostRepository;
use App\Services\BenchmarkService\AbstractBenchmark;
use Illuminate\Support\Arr;

class RankingFactorCompetitorsKeywordsBenchmark extends AbstractBenchmark
{
    protected HostRepository $repository;

    protected int $keywordRankMax = 20;

    protected int $n = 500;

    public function __construct(HostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function steps(): array
    {
        return [
            [
                'job' => BabbarFetchHostKeywordsJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $host = $step->benchmarked();
                    if (! $host instanceof Host) {
                        throw new \Exception('Benchmarked model is not an Host model');
                    }

                    $keywordRankMax = Arr::get($step, 'benchmark.params.keywordRankMax', $this->keywordRankMax);

                    return [
                        'host' => $host->hostname,
                        'n' => $this->n,
                        'max' => $keywordRankMax,
                    ];
                },
            ],

            // Todo : recalculer valeur du nombre de kw en top 20

        ];
    }
}
