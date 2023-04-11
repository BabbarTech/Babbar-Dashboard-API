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

namespace App\Repositories\Actions;

use Illuminate\Database\Query\Builder;

interface ActionInterface
{
    public function handle(Builder $builder): mixed;

    public function getTitle(): string;

    public function setTitle(string $title): ActionInterface;

    public function setParams(array $params): ActionInterface;

    public function getActionKey(): string;
}
