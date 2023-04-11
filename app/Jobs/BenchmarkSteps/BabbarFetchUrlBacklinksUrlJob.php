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

class BabbarFetchUrlBacklinksUrlJob extends BabbarFetchHostBacklinksUrlJob
{
    protected string $uri = '/url/backlinks/url';

    protected function getDefaultRequestBody(): array
    {
        return [
            'url' => null,
            'limit' => $this->nbBacklinksLimit,
            'sort' => 'desc',
            'type' => 'semanticValue',
        ];
    }
}
