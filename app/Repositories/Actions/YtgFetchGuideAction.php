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

namespace App\Repositories\Actions;

use App\Enums\StatusEnum;
use App\Exceptions\ActionNotProcessedSelectionMissing;
use App\Models\Guide;
use App\Models\Keyword;
use App\Services\BenchmarkService\Benchmarks\RankingFactorKeywordsGuidesBenchmark;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class YtgFetchGuideAction extends AbstractAction
{
    protected string $benchmarkType = RankingFactorKeywordsGuidesBenchmark::class;

    public function handle(Builder $builder): mixed
    {
        $selections = $this->getSelectionsFromPayload() ?? $builder->pluck('keyword_id');

        if (empty($selections)) {
            throw new ActionNotProcessedSelectionMissing();
        }

        $keywords = Keyword::whereIntegerInRaw('id', $selections)
            ->whereDoesntHave('benchmarks', function (EloquentBuilder $query) {
                $query->where('type', $this->benchmarkType)
                    ->whereNotIn('status', [StatusEnum::ERROR, StatusEnum::CANCELLED]);
            })
            ->get();

        $keywords->each(function (Keyword $keyword) {
            $benchmark = $keyword->benchmarks()->create([
                'type' => $this->benchmarkType,
            ]);

            $benchmark->process();
        });


        $message = sprintf(
            '%s New %s added to queue',
            $keywords->count(),
            class_basename($this->benchmarkType)
        );

        return response()->json([
            'message' => $message,
        ]);
    }
}
