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

use App\Rules\Boolean;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Closure;

class GroupingFilter extends AbstractFilter
{
    protected string $method = 'groupBy';

    protected ?Closure $apply;

    public function handle(Builder $query, callable $next): Builder
    {
        $property = 'filters.' . $this->property;

        $validated = request()->validate([
            $property => [
                'sometimes',
                'nullable',
                new Boolean(),
            ]
        ]);

        $rawValue = Arr::get($validated, $property);
        $value = filter_var($rawValue, FILTER_VALIDATE_BOOLEAN);

        if ($value === true) {
            if (isset($this->apply)) {
                $query = call_user_func($this->apply, $query);
            } else {
                $query->{$this->method}($this->column);
            }
        }

        return $next($query);
    }

    public function setApply(Closure $apply): Filter
    {
        $this->apply = $apply;

        return $this;
    }
}
