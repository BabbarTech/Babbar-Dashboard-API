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
use Illuminate\Support\Facades\DB;

class MinMaxFilter extends AbstractFilter
{
    protected string $method = 'where';

    protected float $ratio = 1;

    public function setRatio(float $ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }

    /**
     * @param int $value
     * @return float|int
     */
    protected function getValue(int $value)
    {
        if ($this->ratio == 1) {
            return $value;
        }

        return $this->ratio * $value;
    }

    public function handle(Builder $query, callable $next): Builder
    {
        $minProperty = "filters.$this->property.min";
        $maxProperty = "filters.$this->property.max";

        $validated = request()->validate([
            $minProperty => 'sometimes|nullable|integer',
            $maxProperty => 'sometimes|nullable|integer',
        ]);

        $min = Arr::get($validated, $minProperty);
        $max = Arr::get($validated, $maxProperty);

        if (!blank($min)) {
            //$query->{$this->method}($this->column, '>=', $this->getValue((int) $min));
            $query->{$this->method}(DB::raw('ifnull(' . $this->column . ', 0)'), '>=', $this->getValue(intval($min)));
        }

        if (!blank($max)) {
            //$query->{$this->method}($this->column, '<=', $this->getValue((int) $max));
            $query->{$this->method}(DB::raw('ifnull(' . $this->column . ', 0)'), '<=', $this->getValue(intval($max)));
        }

        return $next($query);
    }
}
