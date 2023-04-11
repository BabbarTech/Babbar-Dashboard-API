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

use App\Models\Backlink;
use App\Models\Url;
use Illuminate\Support\Facades\Validator;

class BabbarFetchUrlInducedStrengthJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/url/fi';

    protected bool $allowFailures = true;

    protected function getDefaultRequestBody(): array
    {
        return [
            'source' => null,
            'target' => null,
        ];
    }

    protected function process(array $data): void
    {
        $validator = Validator::make($data, [
            'fi' => 'integer',
            'confidence' => 'string',
            'source' => 'url',
            'target' => 'url',
        ]);

        if ($validator->fails()) {
            info('CAN NOT FETCH DATA', $this->getDefaultRequestBody());
            info($validator->errors());
            return ;
        }

        $entry = $validator->validate();

        $sourceUrl = Url::firstOrCreate([
            'url' => $entry['source']
        ]);

        $targetUrl = Url::firstOrCreate([
            'url' => $entry['target']
        ]);

        $backlinks = Backlink::where([
            'host_id' => $targetUrl->host_id,
            'source_url_id' => $sourceUrl->getKey(),
            'target_url_id' => $targetUrl->getKey(),
        ])->update([
            'induced_strength' => $entry['fi'],
            'induced_strength_confidence' => $entry['confidence'],
        ]);
    }
}
