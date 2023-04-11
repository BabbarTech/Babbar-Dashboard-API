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

abstract class AbstractFilter implements Filter
{
    protected string $property;

    protected string $column;

    protected string $method;

    public function __construct(string $property, string $column = null)
    {
        $this->property = $property;
        $this->column = $column ?: $property;
    }

    abstract public function handle(Builder $query, callable $next): Builder;

    /**
     * @param string $column
     * @return $this
     */
    public function setColumn(string $column)
    {
        $this->column = $column;

        return $this;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }
}
