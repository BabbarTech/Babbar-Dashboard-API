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

namespace App\Actions\Traits;

use Illuminate\Support\Collection;

trait KeywordsRankHelpers
{
    protected function keywordsRanksPerPage(Collection $data): Collection
    {
        $rankMax = intval(config('benchmarks.keywords-ranks.max', 100));
        $nbPages = intval(ceil($rankMax / 10));

        $distribution = $data->groupBy(function ($item) {
            /** @var \stdClass $item */
            return ($item->rank - 1) / 10;
        })->map(function ($keywords, $delta) {
            return [
                'page' => $delta + 1,
                ...$this->getKeywordStats($keywords),
            ];
        })->keyBy('page');

        return collect(range(1, $nbPages))
            ->map(function ($page) use ($distribution) {
                return $distribution->get($page);
            });
    }


    protected function keywordsRanksPerPosition(Collection $data): Collection
    {
        $rankMax = intval(config('benchmarks.keywords-ranks.max', 100));

        $distribution = $data->groupBy(function ($item) {
            /** @var \stdClass $item */
            return $item->rank;
        })->map(function ($keywords, $delta) {
            return [
                'rank' => $delta,
                ...$this->getKeywordStats($keywords),
            ];
        });

        return collect(range(1, $rankMax))
            ->map(function ($page) use ($distribution) {
                return $distribution->get($page);
            });
    }

    protected function getKeywordStats(Collection $keywordCollection): array
    {
        $medianBKS = $keywordCollection->median('bks');
        $averageBKS = $keywordCollection->avg('bks');
        $averageNbWords = $keywordCollection->avg('nb_words');

        return [
            'nb_keywords' => $keywordCollection->count(),
            'median_bks' => $this->numberFormat($medianBKS),
            'average_bks' => $this->numberFormat($averageBKS),
            'average_nb_words' => $this->numberFormat($averageNbWords),
        ];
    }

    protected function numberFormat(int|float|null $number, int $decimals = 2, string $decimalSeparator = ','): ?string
    {
        if ($number) {
            return number_format($number, $decimals, $decimalSeparator);
        }

        return null;
    }
}
