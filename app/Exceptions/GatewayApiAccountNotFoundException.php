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

class GatewayApiAccountNotFoundException extends Exception
{
    /** @var string */
    protected $message = 'Gateway API account not found';

    public static function for(string $gatewayName = null): GatewayApiAccountNotFoundException
    {
        return (new self($gatewayName . ' API account not found'));
    }
}
