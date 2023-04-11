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

namespace App\Jobs\BenchmarkSteps;

use App\Models\Gateway;
use App\Services\Api\TrafilaturaApiClient;

abstract class AbstractTrafilaturaFetchJob extends AbstractGatewayFetchJob
{
    protected string $gatewayName = Gateway::TRAFILATURA;

    protected string $gatewayClientClass = TrafilaturaApiClient::class;
}
