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
use Illuminate\Support\Str;

class SearchIncludeExcludeFilter extends AbstractFilter
{
    public function handle(Builder $query, callable $next): Builder
    {
        $includeProperty = "filters.$this->property.include";
        $excludeProperty = "filters.$this->property.exclude";

        $validated = request()->validate([
            $includeProperty => 'sometimes|nullable|string',
            $excludeProperty => 'sometimes|nullable|string',
        ]);

        /** @var ?string $includes */
        $includes = Arr::get($validated, $includeProperty);

        /** @var ?string $excludes */
        $excludes = Arr::get($validated, $excludeProperty);

        if (empty($includes) && empty($excludes)) {
            return $next($query);
        }

        $query = $query->where(function ($q) use ($includes, $excludes) {
            if (!empty($includes)) {
                $q = $this->addWhereCondition($q, $includes);
            }

            if (!empty($excludes)) {
                $q = $this->addWhereCondition($q, $excludes, 'not like');
            }

            return $q;
        });

        return $next($query);
    }

    protected function addWhereCondition(Builder $query, string $values, string $operator = 'like'): Builder
    {
        $method = $operator === 'like' ? 'orWhere' : 'where';
        return $query->where(function ($q) use ($method, $values, $operator) {
            foreach (explode(';', $values) as $search) {
                $search = trim($search);
                if (empty($search)) {
                    continue;
                }

                // Handle jokers * at beginning or end
                $search = '%' . $search . '%';
                //$search = preg_replace('/(^\*|\*$)/', '%', $search);

                $q->{$method}($this->column, $operator, $search);
            }
        });
    }
}
