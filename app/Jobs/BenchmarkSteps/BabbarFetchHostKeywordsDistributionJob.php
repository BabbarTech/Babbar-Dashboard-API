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
use Illuminate\Support\Facades\Validator;

class BabbarFetchHostKeywordsDistributionJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/host/keywords/distribution';

    protected bool $allowFailures = true;

    protected function getDefaultRequestBody(): array
    {
        return [
            'host' => null,
            'lang' => $this->getProject()->getLang(),
            'country' => $this->getProject()->getCountry(),
            'n' => 50000,
        ];
    }

    protected function process(array $data): void
    {
        $requestBody = $this->getRequestBody();

        $entries = data_get($data, 'distribution.entries');
        if (! is_array($entries)) {
            return ;
        }

        $validator = Validator::make($entries, [
            '1-10' => 'nullable|integer',
            '11-20' => 'nullable|integer',
            '21+' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            // Todo : Log ?
            return ;
        }
        $result = $validator->validate();

        $host = Host::updateOrCreate([
            'hostname' => $requestBody['host'],
        ], [
            'nb_kw_pos_1_10' => $result['1-10'],
            'nb_kw_pos_11_20' => $result['11-20'],
            'nb_kw_pos_21plus' => $result['21+'],
        ]);
    }
}
