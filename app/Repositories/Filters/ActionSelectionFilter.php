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

class ActionSelectionFilter extends AbstractFilter
{
    public function handle(Builder $query, callable $next): Builder
    {
        $property = 'action.selections';

        $validated = request()->validate([
            $property => 'sometimes|nullable|array',
        ]);

        $selection = Arr::get($validated, $property);

        if (! empty($selection)) {
            $query->whereIn($this->column, $selection);
        }

        return $next($query);
    }
}
