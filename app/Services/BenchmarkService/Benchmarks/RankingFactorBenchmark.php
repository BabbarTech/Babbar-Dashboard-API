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

use App\Jobs\BenchmarkSteps\BabbarFetchHostKeywordsDistributionJob;
use App\Jobs\BenchmarkSteps\BabbarFetchHostKeywordsJob;
use App\Jobs\BenchmarkSteps\BabbarFetchHostSimilarJob;
use App\Jobs\BenchmarkSteps\BabbarFetchKeywordJob;
use App\Repositories\HostRepository;
use App\Services\BenchmarkService\AbstractBenchmark;
use App\Services\BenchmarkService\BenchmarkStepPayloadCollectionHandler;

class RankingFactorBenchmark extends AbstractBenchmark
{
    protected HostRepository $repository;

    protected int $keywordRankMax = 20;

    public function __construct(HostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function steps(): array
    {
        return [
            [
                'job' => BabbarFetchHostKeywordsJob::class,
                'payload' => [
                    'host' => $this->getProject()->hostname,
                    'max' => $this->keywordRankMax,
                ],
            ],

            [
                'job' => BabbarFetchKeywordJob::class,
                'payload' => new BenchmarkStepPayloadCollectionHandler(
                    $this->getProject()->keywords()->orderBy('keywords.id'),
                    function ($item) {
                        /** @var \stdClass $item */
                        return [
                            'keyword' => $item->keywords,
                            'min' => 1,
                            'max' => $this->keywordRankMax
                        ];
                    }
                ),
            ],

            [
                'job' => BabbarFetchHostSimilarJob::class,
                'payload' => [
                    'host' => $this->getProject()->hostname,
                    'n' => 5000,
                ],
            ],

            [
                'job' => BabbarFetchHostKeywordsJob::class,
                'payload' => [
                    'host' => $this->getProject()->hostname,
                    'min' => ($this->keywordRankMax + 1),
                    'max' => intval(config('benchmarks.keywords-ranks.max', 100)),
                ],
            ],

            [
                'job' => BabbarFetchHostKeywordsDistributionJob::class,
                'payload' => new BenchmarkStepPayloadCollectionHandler(
                    $this->repository->getHostsWithSameKeywordsQuery($this->getProject()->host),
                    function ($item) {
                        // Skip if hostname is an IP adress
                        if (filter_var($item->hostname, FILTER_VALIDATE_IP)) {
                            return null;
                        }

                        return [
                            'host' => $item->hostname,
                        ];
                    }
                ),
            ],
        ];
    }
}
