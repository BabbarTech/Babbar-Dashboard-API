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

use Illuminate\Support\Collection;

interface BenchmarkStepPayloadCollection
{
    public function nbJobsPerBatch(): int;

    public function total(): int;

    public function getPayloadCollection(int $delta): Collection;
}
