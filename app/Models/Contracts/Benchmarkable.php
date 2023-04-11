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

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/** @property string $title  */
interface Benchmarkable
{
    public function benchmarks(): MorphMany;

    public function getTitleAttribute(): string;
}
