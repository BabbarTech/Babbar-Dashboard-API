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

namespace App\Services\BenchmarkService\Contracts;

use App\Models\Benchmark;

interface BenchmarkService
{
    public function setBenchmarkInstance(Benchmark $benchmark): BenchmarkService;

    public function steps(): array;

    public function getStep(int $delta): ?array;
}
