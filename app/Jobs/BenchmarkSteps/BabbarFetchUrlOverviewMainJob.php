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

use App\Models\Url;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class BabbarFetchUrlOverviewMainJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/url/overview/main';

    protected bool $allowFailures = true;

    protected function process(array $data): void
    {
        $validator = Validator::make($data, [
            'pageValue' => 'sometimes|required|nullable|integer',
            'pageTrust' => 'sometimes|required|nullable|integer',
            'semanticValue' => 'sometimes|required|nullable|integer',
            'babbarAuthorityScore' => 'sometimes|required|nullable|integer',
            'lastUpdate' => 'required',
        ]);

        if ($validator->fails()) {
            // Todo : Log ?
            //var_dump($validator->errors());
            return;
        }

        $attributes = $validator->validate();

        if ($attributes['lastUpdate'] === false) {
            return ;
        }

        /** @var string $urlValue */
        $urlValue = Arr::get($this->requestBody, 'url');

        $url = Url::where('url', $urlValue)->firstOrFail();

        $overview = $url->overviews()->firstOrCreate([
            'page_value' => Arr::get($attributes, 'pageValue'),
            'page_trust' => Arr::get($attributes, 'pageTrust'),
            'semantic_value' => Arr::get($attributes, 'semanticValue'),
            'babbar_authority_score' => Arr::get($attributes, 'babbarAuthorityScore'),
            'source_date' => date('Y-m-d', $attributes['lastUpdate']),
        ]);
    }
}
