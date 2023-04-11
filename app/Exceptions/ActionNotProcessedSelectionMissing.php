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

namespace App\Exceptions;

use Exception;

class ActionNotProcessedSelectionMissing extends Exception
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        if (empty($message)) {
            /** @var string $message */
            $message = __('error.action.selection-required');
        }

        parent::__construct($message, $code, $previous);
    }
}
