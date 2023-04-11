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

namespace App\Services\Api;

use App\Models\Gateway;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TrafilaturaApiClient extends AbstractGatewayApiClient
{
    protected string $gatewayName = Gateway::TRAFILATURA;

    public function fetch(string $uri, array $data = [], string $method = 'get'): Response
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])
            ->{$method}($this->makeUrl($uri), $data);

        return $response;
    }
}
