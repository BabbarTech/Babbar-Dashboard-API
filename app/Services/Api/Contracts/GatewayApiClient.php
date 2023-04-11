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

namespace App\Services\Api\Contracts;

use Illuminate\Http\Client\Response;

interface GatewayApiClient
{
    public function fetch(string $uri, array $data = [], string $method = 'post'): Response;

    public function setApiToken(string $apiToken): GatewayApiClient;

    public function getGatewayName(): string;
}
