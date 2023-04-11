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
use App\Services\Api\BabbarApiClient;

abstract class AbstractBabbarFetchJob extends AbstractGatewayFetchJob
{
    protected string $gatewayName = Gateway::BABBAR;

    protected string $gatewayClientClass = BabbarApiClient::class;
}
