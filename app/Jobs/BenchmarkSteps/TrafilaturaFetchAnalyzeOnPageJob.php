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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class TrafilaturaFetchAnalyzeOnPageJob extends AbstractTrafilaturaFetchJob
{
    protected string $uri = '/analyze-on-page';

    public int $maxExceptions = 3;

    protected bool $throwApiException = false;

    protected bool $allowFailures = true;

    protected function process(array $data): void
    {
        $validator = Validator::make($data, [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            // Todo : Log ?
            return ;
        }

        $attributes = $validator->validate();

        /** @var string $urlValue */
        $urlValue = Arr::get($this->requestBody, 'url');

        $url = Url::where('url', $urlValue)->firstOrFail();

        $pageAnalyze = $url->pageAnalyzes()->firstOrCreate([
            'relevant_text' => Arr::get($attributes, 'content'),
        ]);
    }
}
