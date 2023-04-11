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

class BabbarFetchHostBacklinksUrlListJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/host/backlinks/url/list';

    protected ?int $limit = 100000;

    protected function getDefaultRequestBody(): array
    {
        return [
            'host' => null,
            'offset' => $this->offset,
            'n' => $this->nbItemsRequested,
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

            $backlink = Backlink::updateOrCreate([
                'host_id' => $targetUrl->host_id,
                'source_url_id' => $sourceUrl->getKey(),
                'target_url_id' => $targetUrl->getKey(),
                'link_type' => $entry['linkType'],
                'link_text' => $entry['linkText'],
                'language' => $entry['language'],
            ], [
                'link_rels' => $linkRels,
                'ip' => $entry['ip'],
            ]);
        }

        $this->setNbItemsReturned(count($entries));
    }
}
