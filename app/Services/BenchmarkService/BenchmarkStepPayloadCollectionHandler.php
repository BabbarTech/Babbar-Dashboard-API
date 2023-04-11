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

namespace App\Services\BenchmarkService;

use App\Models\BenchmarkStep;
use App\Services\BenchmarkService\Contracts\BenchmarkStepPayloadCollection;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Support\Collection;
use Closure;

class BenchmarkStepPayloadCollectionHandler implements BenchmarkStepPayloadCollection
{
    protected BuilderContract $query;

    protected Closure $closure;

    protected BenchmarkStep $benchmarkStep;

    protected int $nbJobsPerBatch = 20;

    public function __construct(BuilderContract $query, Closure $closure)
    {
        $this->query = $query;
        $this->closure = $closure;
    }

    public function nbJobsPerBatch(): int
    {
        return $this->nbJobsPerBatch;
    }

    public function total(): int
    {
        return $this->query->count();
    }

    protected function fetchChunk(int $delta): Collection
    {
        $query = $this->query;

        // Add default ordering if any orderBy() is specified
        if (empty($query->orders)) {
            $query->orderBy('id');
        }

        return $query->limit($this->nbJobsPerBatch)
            ->offset($delta * $this->nbJobsPerBatch)
            ->get();
    }

    public function getPayloadCollection(int $delta): Collection
    {
        return $this->fetchChunk($delta)
            ->map(function ($item) {
                return call_user_func($this->closure, $item);
            })
            ->filter()
            ->values();
    }
}
