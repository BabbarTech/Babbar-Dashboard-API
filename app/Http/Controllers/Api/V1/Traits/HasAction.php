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

namespace App\Http\Controllers\Api\V1\Traits;

use App\Enums\ActionEnum;
use App\Repositories\Actions\ActionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

trait HasAction
{
    protected function processingAction(Request $request, Builder $builder, string $column = 'id'): mixed
    {
        $validated = $request->validate([
            'action' => 'sometimes|nullable|array',
            'action.handler' => 'required_with:action|string', // in enum
            'action.selections' => 'nullable|array',
            'action.selections.*' => 'integer',
            'action.params' => 'sometimes|nullable|array',
        ]);

        if (empty($validated)) {
            return null;
        }

        $actionPayload = $validated['action'];

        if (! empty($actionPayload['selections'])) {
            $builder->whereIntegerInRaw($column, $actionPayload['selections']);
        }

        $handlerClassname = ActionEnum::from($actionPayload['handler'])->handler();

        /** @var ActionInterface $handler */
        $handler = new $handlerClassname($actionPayload);

        return $handler->handle($builder);
    }
}
