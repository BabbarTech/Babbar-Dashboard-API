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
use App\Models\Host;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class YourTextGuruGuideCheckJob extends AbstractYourTextGuruFetchJob
{
    protected function getUri(): string
    {
        return '/check/' . $this->getGuideId();
    }

    protected function getGuideId(): mixed
    {
        return Arr::get($this->requestBody, 'guide_id');
    }

    protected function process(array $data): void
    {
        $attributes = Validator::make($data, [
            'score' => 'required|nullable|integer',
            'danger' => 'required|nullable|integer',
        ])->validate();

        $guide = Guide::where('yourtextguru_guide_id', $this->getGuideId())
            ->firstOrFail();

        $attributes['page_analyze_id'] = Arr::get($this->requestBody, 'page_analyze_id');

        $check = $guide->checks()->firstOrCreate($attributes);
    }
}
