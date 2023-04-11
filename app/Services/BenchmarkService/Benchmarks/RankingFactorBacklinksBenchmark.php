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

use App\Jobs\BenchmarkSteps\BabbarFetchHostBacklinksUrlListJob;
use App\Jobs\BenchmarkSteps\BabbarFetchUrlBacklinksUrlJob;
use App\Jobs\BenchmarkSteps\BabbarFetchUrlInducedStrengthJob;
use App\Jobs\BenchmarkSteps\BabbarFetchUrlKeywordsJob;
use App\Jobs\BenchmarkSteps\BabbarFetchUrlOverviewMainJob;
use App\Models\Backlink;
use App\Repositories\HostRepository;
use App\Services\BenchmarkService\AbstractBenchmark;
use App\Services\BenchmarkService\BenchmarkStepPayloadCollectionHandler;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RankingFactorBacklinksBenchmark extends AbstractBenchmark
{
    protected HostRepository $repository;

    public function __construct(HostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function steps(): array
    {
        return [
            [
                'job' => BabbarFetchHostBacklinksUrlListJob::class,
                'payload' => [
                    'host' => $this->getProject()->hostname,
                ],
            ],

            [
                'job' => BabbarFetchUrlInducedStrengthJob::class,
                'payload' => new BenchmarkStepPayloadCollectionHandler(
                    $this->getBacklinks(),
                    function ($backlink) {
                        if (! empty($backlink->induced_strength)) {
                            return null;
                        }

                        if (empty($backlink->sourceUrl)) {
                            throw new \Exception('Source URL not found');
                        }

                        if (empty($backlink->targetUrl)) {
                            throw new \Exception('Target URL not found');
                        }

                        return [
                            'source' => $backlink->sourceUrl->url,
                            'target' => $backlink->targetUrl->url,
                        ];
                    }
                ),
            ],

            [
                'job' => BabbarFetchUrlKeywordsJob::class,
                'payload' => new BenchmarkStepPayloadCollectionHandler(
                    $this->getBacklinks(),
                    function ($backlink) {
                        /** @var Backlink $backlink */

                        if (empty($backlink->sourceUrl)) {
                            throw new \Exception('Source URL not found');
                        }

                        return [
                            'url' => $backlink->sourceUrl->url,
                            'n' => 500,
                        ];
                    }
                ),
            ],

            [
                'job' => BabbarFetchUrlOverviewMainJob::class,
                'payload' => new BenchmarkStepPayloadCollectionHandler(
                    $this->getBacklinks(),
                    function ($backlink) {
                        if (empty($backlink->sourceUrl)) {
                            throw new \Exception('Source URL not found');
                        }

                        return [
                            'url' => $backlink->sourceUrl->url,
                        ];
                    }
                ),
            ],

            [
                'job' => BabbarFetchUrlBacklinksUrlJob::class,
                'payload' => new BenchmarkStepPayloadCollectionHandler(
                    $this->getBacklinks(),
                    function ($backlink) {
                        if (empty($backlink->sourceUrl)) {
                            throw new \Exception('Source URL not found');
                        }

                        return [
                            'url' => $backlink->sourceUrl->url,
                            'limit' => 500,
                        ];
                    }
                ),
            ],
        ];
    }

    protected function getBacklinks(): HasMany
    {
        return $this->getProject()->host
            ->backlinkUrls()
            ->with([
                'sourceUrl',
                'targetUrl',
            ]);
    }
}
