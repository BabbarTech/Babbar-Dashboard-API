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

use App\Jobs\Contracts\GatewayFetchJob;
use App\Models\Guide;
use App\Models\Url;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class YourTextGuruGuideSerpJob extends AbstractYourTextGuruFetchJob
{
    protected string $method = GatewayFetchJob::METHOD_GET;

    protected function getUri(): string
    {
        return '/serp/' . $this->getGuideId();
    }

    protected function process(array $data): void
    {
        $validated = Validator::make($data, [
            'date' => 'required|date',
            'serps' => 'required|array',
        ])->validate();

        $collection = [];

        foreach ($validated['serps'] as $item) {
            $validator = Validator::make($item, [
                'position' => 'required|integer',
                'url' => 'required|url',
                'scores.soseo_all_content' => 'required|integer',
                'scores.dseo_all_content' => 'required|integer',
            ]);

            if ($validator->fails()) {
                // Todo : Log ?
                //var_dump('validator failed');
                continue;
            }

            $result = $validator->validate();

            $url = Url::firstOrCreate([
                'url' => $result['url']
            ]);

            $collection[$url->getKey()] = [
                //'url_id' => $url->id,
                'position' => $result['position'],
                'soseo_all_content' => Arr::get($result, 'scores.soseo_all_content'),
                'dseo_all_content' => Arr::get($result, 'scores.dseo_all_content'),
                'source_date' => $validated['date'],
            ];
        }

        $guide = Guide::where('yourtextguru_guide_id', $this->getGuideId())
            ->firstOrFail();

        $guide->serps()->syncWithoutDetaching($collection);
    }

    protected function getGuideId(): mixed
    {
        return Arr::get($this->requestBody, 'guide_id');
    }
}
