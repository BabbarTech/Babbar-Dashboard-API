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

use App\Models\Benchmark;
use App\Models\Project;
use App\Services\BenchmarkService\Contracts\BenchmarkService;

abstract class AbstractBenchmark implements BenchmarkService
{
    protected Benchmark $benchmark;

    abstract public function steps(): array;

    public function setBenchmarkInstance(Benchmark $benchmark): BenchmarkService
    {
        $this->benchmark = $benchmark;

        return $this;
    }

    public function getBenchmarkInstance(): Benchmark
    {
        return $this->benchmark;
    }

    public function getStep(int $delta): ?array
    {
        return $this->steps()[$delta] ?? null;
    }

    protected function getProject(): Project
    {
        return app('currentProject');
    }
}
