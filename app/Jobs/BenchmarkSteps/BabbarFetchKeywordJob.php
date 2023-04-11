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

use App\Models\Keyword;
use App\Models\Url;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class BabbarFetchKeywordJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/keyword';

    protected function getDefaultRequestBody(): array
    {
        return [
            'keyword' => null,
            'lang' => $this->getProject()->getLang(),
            'country' => $this->getProject()->getCountry(),
            'feature' => 'ORGANIC',
            //'date' => '2021-11-01',
            'offset' => $this->offset,
            'n' => $this->nbItemsRequested,
            'min' => 1,
            'max' => 100
        ];
    }

    protected function process(array $data): void
    {
        $keyword = Keyword::where('keywords', $this->getRequestBody()['keyword'])
            ->firstOrFail();

        //var_dump($keyword->keywords . ' #' . $keyword->getKey());

        $validated = Validator::make($data, [
            'data.results' => 'required|array',
            'data.request.date' => 'required|date',
            'request.metadata.fetchDate' => 'required|date',
        ])->validate();

        $serpUrls = [];
        $results = Arr::get($validated, 'data.results', []);
        $sourceDate = Arr::get($validated, 'data.request.date', []);
        $fetchDate = Arr::get($validated, 'request.metadata.fetchDate', []);

        if (!is_array($results)) {
            return ;
        }

        foreach ($results as $item) {
            $organic = data_get($item, 'feature.organic');
            if (!is_array($organic)) {
                continue ;
            }

            $validator = Validator::make($organic, [
                'position' => 'required|integer',
                'url' => 'required|url',
                'title' => 'string',
                'breadcrumb' => 'string',
                'snippet' => 'string',
            ]);

            if ($validator->fails()) {
                // Todo : Log ?
                //var_dump('validator failed');
                continue;
            }

            $result = $validator->validate();
            //var_dump($result['url']);

            $url = Url::firstOrCreate([
                'url' => $result['url']
            ]);

            $serpUrls[$url->getKey()] = [
                'host_id' => $url->host_id,
                'rank' => $result['position'],
                'title' => $result['title'] ?? null,
                'breadcrumb' => $result['breadcrumb'] ?? null,
                'snippet' => $result['snippet'] ?? null,
                'source_date' => $sourceDate,
                'fetch_date' => $fetchDate,
            ];
        }

        $keyword->serp()->syncWithoutDetaching($serpUrls);
    }
}
