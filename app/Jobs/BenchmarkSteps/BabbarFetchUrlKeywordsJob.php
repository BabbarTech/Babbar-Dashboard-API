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

use App\Models\Host;
use App\Models\Url;

class BabbarFetchUrlKeywordsJob extends BabbarFetchHostKeywordsJob
{
    protected string $uri = '/url/keywords';

    protected function getDefaultRequestBody(): array
    {
        return [
            'url' => null,
            'lang' => $this->getProject()->getLang(),
            'country' => $this->getProject()->getCountry(),
            //'date' => '2021-11-01',
            'offset' => $this->offset,
            'n' => $this->nbItemsRequested,
            'min' => 1,
            'max' => $this->keywordRankMax,
        ];
    }

    protected function findHostByRequestBody(): ?Host
    {
        $requestBody = $this->getRequestBody();

        if (isset($requestBody['url'])) {
            $url = Url::where('url', $requestBody['url'])
                ->firstOrFail();

            return $url->host;
        }

        return null;
    }
}
