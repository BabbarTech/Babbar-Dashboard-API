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

namespace App\Repositories\Filters;

use Illuminate\Database\Query\Builder;

interface Filter
{
    public function handle(Builder $query, callable $next): Builder;

    /**
     * @param string $column
     * @return $this|Filter
     */
    public function setColumn(string $column);
}
