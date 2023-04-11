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

use App\Models\Guide;
use App\Models\Keyword;
use Illuminate\Support\Facades\Validator;

class YourTextGuruGuideCreateJob extends AbstractYourTextGuruFetchJob
{
    protected string $uri = '/guide';

    protected function getDefaultRequestBody(): array
    {
        return [
            'query' => null,
            'type' => 'premium',
            'lang' => $this->getProject()->getYourTextGuruLang(),
            'group_id' => 0,
        ];
    }

    protected function process(array $data): void
    {
        $validator = Validator::make($data, [
            'status' => 'required|string|in:ok',
            'query' => 'required|string',
            'lang' => 'required|string|in:fr_fr,en_gb,es_es',
            'guide_id' => 'required|integer',
            'type' => 'required|in:premium,oneshot',
            'group_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            // Todo : Log ?
            //var_dump($validator->errors());
            info($validator->errors());
            return;
        }

        $attributes = $validator->validate();

        /** @var Keyword $keyword */
        $keyword = $this->getBenchmarkStep()->benchmarked();

        $guide = $keyword->guides()->firstOrCreate([
            'keyword_id' => $keyword->id,
            'yourtextguru_guide_id' => $attributes['guide_id'],
            'lang' => $attributes['lang'],
            'group_id' => $attributes['group_id'],
        ]);
    }
}
