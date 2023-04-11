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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class BabbarFetchHostBacklinksUrlJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/host/backlinks/url';

    protected int $nbBacklinksLimit = 20;

    protected function getDefaultRequestBody(): array
    {
        return [
            'host' => null,
            'limit' => $this->nbBacklinksLimit,
            'sort' => 'desc',
            'type' => 'semanticValue',
        ];
    }

    protected function process(array $data): void
    {
        $validator = Validator::make($data, [
            'numBacklinksUsed' => 'nullable|integer',
            'numBacklinksTotal' => 'nullable|integer',
            'links' => 'array',
        ]);

        if ($validator->fails()) {
            info('CAN NOT FETCH DATA', $this->getDefaultRequestBody());
            info($validator->errors());
            return ;
        }

        $validated = $validator->validate();

        /** @var array $entries */
        $entries = Arr::get($validated, 'links');

        foreach ($entries as $item) {
            if (! is_array($item)) {
                continue;
            }

            $validator = Validator::make($item, [
                'target' => 'required|url',
                'source' => 'required|url',
                'BabbarConnect' => 'required|boolean',
                'linkType' => 'required|string|nullable',
                'linkText' => 'string|nullable',
                'linkRels' => 'required|array',
                'language' => 'string|nullable',
                'ip' => 'ip|nullable',
                'fi.score' => 'sometimes|nullable|integer',
                'fi.confidence' => 'sometimes|nullable|string',
            ]);

            if ($validator->fails()) {
                // Todo : Log ?
                info('ENTRY FAIL', $item);
                info($validator->errors());
                continue;
            }

            $entry = $validator->validate();

            $sourceUrl = Url::firstOrCreate([
                'url' => $entry['source']
            ]);

            $targetUrl = Url::firstOrCreate([
                'url' => $entry['target']
            ]);

            $linkRels = is_array($entry['linkRels']) ? implode(' ', $entry['linkRels']) : null;

            $backlink = Backlink::firstOrNew([
                'host_id' => $targetUrl->host_id,
                'source_url_id' => $sourceUrl->getKey(),
                'target_url_id' => $targetUrl->getKey(),
                'link_type' => $entry['linkType'],
                'link_text' => $entry['linkText'],
                'language' => $entry['language'],
            ]);

            $backlink->ip = $entry['ip'];

            if (! empty($linkRels)) {
                $backlink->link_rels = $linkRels;
            }

            $fiScore = Arr::get($entry, 'fi.score');
            $fiConfidence = Arr::get($entry, 'fi.confidence');

            if (is_int($fiScore)) {
                $backlink->induced_strength = $fiScore;
            }

            if (is_string($fiConfidence)) {
                $backlink->induced_strength_confidence = $fiConfidence;
            }

            $backlink->save();
        }
    }
}
