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

class OptionFilter extends AbstractFilter
{
    protected string $method = 'where';

    protected bool $multiple = false;

    public function handle(Builder $query, callable $next): Builder
    {
        $property = 'filters.' . $this->property;

        $validated = request()->validate([
            $property => $this->getRules(),
        ]);

        $value = Arr::get($validated, $property);

        if (! empty($value)) {
            $query->{$this->method}($this->column, $value);
        }

        return $next($query);
    }

    /**
     * @return $this
     */
    public function multiple()
    {
        $this->multiple = true;

        return $this;
    }

    protected function getRules(): array
    {
        $rules = [
            'sometimes',
            'nullable',
        ];

        if ($this->multiple) {
            $rules[] = 'array';
        } else {
            $rules[] = 'string';
        }

        return $rules;
    }
}
