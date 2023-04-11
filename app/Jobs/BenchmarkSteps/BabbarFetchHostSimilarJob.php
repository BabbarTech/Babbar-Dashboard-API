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

class BabbarFetchHostSimilarJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/host/similar';

    protected function getDefaultRequestBody(): array
    {
        return [
            'host' => $this->getProject()->hostname,
            'n' => $this->nbItemsRequested,
        ];
    }

    protected function process(array $data): void
    {
        $host = $this->getProject()->host;

        collect($data)
            ->chunk(200)
            ->each(function ($chunk) use ($host) {
                $similars = [];

                foreach ($chunk as $item) {
                    $validator = Validator::make($item, [
                        'lang' => 'required|string',
                        'similar' => 'required|string',
                        'score' => 'required|between:0,9.99',
                    ]);

                    if ($validator->fails()) {
                        // Todo : Log ?
                        //var_dump('skip...');
                        continue;
                    }

                    $attributes = $validator->validate();

                    $similarHost = Host::firstOrCreate([
                        'hostname' => $attributes['similar'],
                    ]);

                    $similars[$similarHost->getKey()] = [
                        'score' => $attributes['score'],
                        'lang' => $attributes['lang'],
                    ];
                }

                $host->similars()->syncWithoutDetaching($similars);
            });
    }
}
