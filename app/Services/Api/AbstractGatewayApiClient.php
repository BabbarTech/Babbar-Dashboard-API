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

use App\Services\Api\Contracts\GatewayApiClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class AbstractGatewayApiClient implements GatewayApiClient
{
    protected string $apiToken;

    protected string $baseUrl;

    protected string $gatewayName;

    public function __construct()
    {
        $config = config('gateways.' . $this->gatewayName, []);
        if (is_array($config)) {
            $this->init($config);
        }
    }

    protected function init(array $config): void
    {
        $baseUrl = Arr::get($config, 'base_url');
        if (is_string($baseUrl)) {
            $this->setBaseUrl($baseUrl);
        }

        $apiToken = Arr::get($config, 'api_token');
        if (is_string($apiToken)) {
            $this->setApiToken($apiToken);
        }
    }

    protected function setBaseUrl(string $baseUrl): AbstractGatewayApiClient
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    protected function getBaseUrl(): string
    {
        if (empty($this->baseUrl)) {
            throw new \Exception('Babbar API base url not defined');
        }

        return $this->baseUrl;
    }

    protected function getApiToken(): string
    {
        if (empty($this->apiToken)) {
            throw new \Exception('Babbar API Token not defined');
        }

        return $this->apiToken;
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function setApiToken(string $apiToken): AbstractGatewayApiClient
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function fetch(string $uri, array $data = [], string $method = 'post'): Response
    {
        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->{$method}($this->makeUrl($uri), $data);

        return $response;
    }

    protected function makeUrl(string $uri): string
    {
        return $this->getBaseUrl() . ltrim($uri, '/');
    }
}
