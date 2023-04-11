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
use Illuminate\Support\Arr;

class BooleanFilter extends AbstractFilter
{
    protected string $method = 'having';

    public function handle(Builder $query, callable $next): Builder
    {
        $property = 'filters.' . $this->property;

        $validated = request()->validate([
            $property => 'sometimes|nullable|boolean',
        ]);

        $value = Arr::get($validated, $property);

        if ($value !== null) {
            $query->{$this->method}($this->column, $value);
        }

        return $next($query);
    }
}
