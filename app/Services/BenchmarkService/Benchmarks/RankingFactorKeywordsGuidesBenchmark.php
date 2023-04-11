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

use App\Jobs\BenchmarkSteps\TrafilaturaFetchAnalyzeOnPageJob;
use App\Jobs\BenchmarkSteps\BabbarFetchKeywordJob;
use App\Jobs\BenchmarkSteps\BabbarFetchUrlOverviewMainJob;
use App\Jobs\BenchmarkSteps\YourTextGuruGuideCheckJob;
use App\Jobs\BenchmarkSteps\YourTextGuruGuideCreateJob;
use App\Jobs\BenchmarkSteps\YourTextGuruGuideGetJob;
use App\Jobs\BenchmarkSteps\YourTextGuruGuideSerpJob;
use App\Models\BenchmarkStep;
use App\Models\Keyword;
use App\Models\Url;
use App\Services\BenchmarkService\AbstractBenchmark;
use Illuminate\Database\Eloquent\Collection;

class RankingFactorKeywordsGuidesBenchmark extends AbstractBenchmark
{
    protected int $keywordRankMax = 20;

    public function steps(): array
    {
        return [
            [
                'job' => YourTextGuruGuideCreateJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $keyword = $this->getKeyword($step);

                    return [
                        'query' => $keyword->keywords,
                        'type' => 'premium',
                        'group_id' => 0,
                    ];
                },
            ],

            [
                'job' => BabbarFetchKeywordJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $keyword = $this->getKeyword($step);

                    // Skip if keyword serp already fetched
                    if ($keyword->serp->isNotEmpty()) {
                        return null;
                    }

                    return [
                        'keyword' => $keyword->keywords,
                        'min' => 1,
                        'max' => $this->keywordRankMax,
                    ];
                },
            ],

            [
                'job' => BabbarFetchUrlOverviewMainJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $keyword = $this->getKeyword($step);

                    return $this->getSerpTop10($keyword)
                        ->map(function ($url) {
                            /** @var Url $url */
                            return [
                                'url' => $url->url,
                            ];
                        });
                },
            ],

            [
                'job' => YourTextGuruGuideGetJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $keyword = $this->getKeyword($step);
                    $guide = $keyword->latestGuide;

                    if (empty($guide)) {
                        throw new \Exception('Guide not found');
                    }

                    return [
                        'guide_id' => $guide->yourtextguru_guide_id,
                    ];
                },
            ],

            [
                'job' => YourTextGuruGuideSerpJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $keyword = $this->getKeyword($step);
                    $guide = $keyword->latestGuide;

                    if (empty($guide)) {
                        throw new \Exception('Guide not found');
                    }

                    return [
                        'guide_id' => $guide->yourtextguru_guide_id,
                    ];
                },
            ],

            [
                'job' => TrafilaturaFetchAnalyzeOnPageJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $keyword = $this->getKeyword($step);

                    return $this->getSerpTop10($keyword)
                        ->map(function ($url) {
                            /** @var Url $url */

                            // Skip if url already analysed
                            if ($url->latestPageAnalyze) {
                                return null;
                            }

                            return [
                                'url' => $url->url,
                            ];
                        });
                },
            ],

            [
                'job' => YourTextGuruGuideCheckJob::class,
                'payload' => function (BenchmarkStep $step) {
                    $keyword = $this->getKeyword($step);
                    $guide = $keyword->latestGuide;

                    if (empty($guide)) {
                        throw new \Exception('Guide not found');
                    }

                    return $this->getSerpTop10($keyword)
                        ->map(function ($url) use ($guide) {
                            /** @var Url $url */

                            // Skip YTG check if relevant_text is missing
                            if (! $url->latestPageAnalyze) {
                                return null;
                            }

                            return [
                                'page_analyze_id' => $url->latestPageAnalyze->id,
                                'guide_id' => $guide->yourtextguru_guide_id,
                                'content' => $url->latestPageAnalyze->relevant_text,
                            ];
                        })->filter()
                        ->values();
                },
            ],
        ];
    }


    protected function getKeyword(BenchmarkStep $step): Keyword
    {
        $keyword = $step->benchmarked();
        if (! $keyword instanceof Keyword) {
            throw new \Exception('Benchmarked model is not an Keyword model');
        }

        return $keyword;
    }


    /**
     * @param Keyword $keyword
     * @extends Collection<int, Url>
     */
    protected function getSerpTop10(Keyword $keyword): Collection
    {
        return $keyword->serp()
            ->where('rank', '<=', 10)
            ->get();
    }
}
