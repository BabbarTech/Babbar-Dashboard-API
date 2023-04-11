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

namespace App\Models\Traits;

use App\Models\Benchmark;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBenchmarks
{
    public function benchmarks(): MorphMany
    {
        return $this->morphMany(Benchmark::class, 'benchmarkable');
    }
}
