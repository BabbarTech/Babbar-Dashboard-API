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

class GatewayApiTooManyRequestsException extends Exception
{
    protected int $secondsRemaining;

    public static function until(int $secondsRemaining, string $gatewayName = null): GatewayApiTooManyRequestsException
    {
        return (new self('Too many requests have been fired to ' . $gatewayName . ' API'))
            ->setSecondsRemaining($secondsRemaining);
    }

    public function setSecondsRemaining(int $secondsRemaining): GatewayApiTooManyRequestsException
    {
        $this->secondsRemaining = $secondsRemaining;

        return $this;
    }

    public function getSecondsRemaining(): int
    {
        return $this->secondsRemaining;
    }
}
