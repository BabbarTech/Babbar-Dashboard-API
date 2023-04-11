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

use App\Enums\StatusEnum;
use App\Jobs\Contracts\GatewayFetchJob;
use App\Models\Guide;
use App\Models\Url;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class YourTextGuruGuideGetJob extends AbstractYourTextGuruFetchJob
{
    protected string $method = GatewayFetchJob::METHOD_GET;

    protected function getUri(): string
    {
        return '/guide/' . $this->getGuideId();
    }

    protected function process(array $data): void
    {
        $attributes = Validator::make(current($data), [
            'grammes1' => 'sometimes|nullable|array',
            'grammes2' => 'sometimes|nullable|array',
            'grammes3' => 'sometimes|nullable|array',
            'entities' => 'sometimes|nullable|array',
        ])->validate();

        $attributes['status'] = StatusEnum::DONE;

        $guide = Guide::where('yourtextguru_guide_id', $this->getGuideId())
            ->firstOrFail();

        $guide->update($attributes);
    }

    protected function getGuideId(): mixed
    {
        return Arr::get($this->requestBody, 'guide_id');
    }
}
