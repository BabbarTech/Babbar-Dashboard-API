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

namespace App\Jobs\Contracts;

use App\Services\Api\Contracts\GatewayApiClient;

interface GatewayFetchJob
{
    public const METHOD_GET = 'get';

    public const METHOD_POST = 'post';

    public function __construct(int $benchmarkStepId, array $requestBody);

    public function getClient(): GatewayApiClient;
}
