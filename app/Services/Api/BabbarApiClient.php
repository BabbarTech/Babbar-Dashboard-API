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

class BabbarApiClient extends AbstractGatewayApiClient
{
    protected string $gatewayName = Gateway::BABBAR;

    protected function makeUrl(string $uri): string
    {
        return parent::makeUrl($uri) . '?api_token=' . $this->getApiToken();
    }
}
