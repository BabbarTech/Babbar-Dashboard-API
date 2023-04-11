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

class OrderBy
{
    protected ?string $column;
    protected string $direction;

    public function __construct(string $column = null, string $direction = 'asc')
    {
        $this->column = $column;
        $this->direction = $direction;
    }

    public function handle(Builder $query, callable $next): Builder
    {
        $orderBy = request()->input('orderBy');

        if (is_string($orderBy) && !empty($orderBy)) {
            $this->direction = preg_match('/^-/', $orderBy) ? 'desc' : 'asc';
            $this->column = preg_replace('/^-/', '', $orderBy);
        }

        if ($this->column) {
            $query->orderBy($this->column, $this->direction);
        }

        return $next($query);
    }
}
