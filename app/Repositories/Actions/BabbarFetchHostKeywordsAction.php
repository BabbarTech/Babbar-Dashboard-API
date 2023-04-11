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
use App\Models\Host;
use App\Services\BenchmarkService\Benchmarks\RankingFactorCompetitorsKeywordsBenchmark;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;

class BabbarFetchHostKeywordsAction extends AbstractAction
{
    protected string $benchmarkType = RankingFactorCompetitorsKeywordsBenchmark::class;

    public function handle(Builder $builder): mixed
    {
        $selections = $this->getSelectionsFromPayload() ?? $builder->pluck('host_id');
        $actionParams = $this->getActionParamsFromPayload();

        if (empty($selections)) {
            throw new ActionNotProcessedSelectionMissing();
        }

        $hosts = Host::whereIntegerInRaw('id', $selections)
            ->whereDoesntHave('benchmarks', function (EloquentBuilder $query) use ($actionParams) {
                $query->where('type', $this->benchmarkType)
                    ->where('status', '!=', StatusEnum::ERROR)
                    ->where('params', $actionParams);
            })
            ->get();

        $hosts->each(function (Host $host) use ($actionParams) {
            $benchmark = $host->benchmarks()->create([
                'type' => $this->benchmarkType,
                'params' => $actionParams,
            ]);

            $benchmark->process();
        });


        $message = sprintf(
            '%s New %s added to queue',
            $hosts->count(),
            class_basename($this->benchmarkType)
        );

        return response()->json([
            'message' => $message,
        ]);
    }
}
