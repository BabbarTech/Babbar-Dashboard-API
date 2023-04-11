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
use App\Models\HostKeyword;
use App\Models\Keyword;
use App\Models\Url;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class BabbarFetchHostKeywordsJob extends AbstractBabbarFetchJob
{
    protected string $uri = '/host/keywords';

    protected int $keywordRankMax = 20;

    protected ?Url $url;

    protected ?Host $host;

    protected function getDefaultRequestBody(): array
    {
        return [
            'host' => null,
            'lang' => $this->getProject()->getLang(),
            'country' => $this->getProject()->getCountry(),
            //'date' => '2021-11-01',
            'offset' => $this->offset,
            'n' => $this->nbItemsRequested,
            'min' => 1,
            'max' => $this->keywordRankMax,
        ];
    }

    protected function process(array $data): void
    {
        $validated = Validator::make($data, [
            'entries' => 'array',
            'request.date' => 'required|date',
        ])->validate();

        /** @var array $entries */
        $entries = Arr::get($validated, 'entries');
        $sourceDate = Arr::get($validated, 'request.date');
        $now = now();

        $lang = $this->getProject()->getLang();

        collect($entries)
            ->chunk(50)
            ->each(function ($chunk) use ($sourceDate, $lang, $now) {

                $keywordCollection = [];
                $hostKeywordCollection = new Collection();

                foreach ($chunk as $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $validator = Validator::make($item, [
                        'keywords' => 'required|string',
                        'url' => 'required|url',
                        'rank' => 'required|integer',
                        'subRank' => 'required|integer',
                        'bks' => 'sometimes|nullable|integer',
                        'numberOfWordsInKeyword' => 'required|integer',
                    ]);

                    if ($validator->fails()) {
                        // Todo : Log ?
                        info('ENTRY FAIL', $item);
                        info($validator->errors());
                        continue;
                    }

                    $entry = $validator->validate();

                    $keywordCollection[] = [
                        'keywords' => $entry['keywords'],
                        'lang' => $lang,
                        'bks' => $entry['bks'] ?? null,
                        'nb_words' => $entry['numberOfWordsInKeyword'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];


                    // Save Url only in top 20
                    $this->url = null;
                    if ($entry['rank'] <= $this->keywordRankMax) {
                        $this->url = Url::firstOrCreate([
                            'url' => $entry['url']
                        ]);
                    }

                    $hostKeywordCollection->push([
                        'host_id' => $this->getHost()?->getKey(),
                        'keyword_id' => null,
                        'keywords' => $entry['keywords'],
                        'rank' => $entry['rank'],
                        'url_id' => $this->url?->getKey(),
                        'source_date' => $sourceDate,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                Keyword::insertOrIgnore($keywordCollection);

                // Retrieve keywords id previously upserted
                $upsertedKeywords = Keyword::where('lang', $lang)
                    ->whereIn('keywords', collect($keywordCollection)->pluck('keywords'))
                    ->pluck('id', 'keywords');

                // Set keywords id to collection
                $hostKeywordCollection = $hostKeywordCollection->map(function ($hostKeyword) use ($upsertedKeywords) {
                    $hostKeyword['keyword_id'] = $upsertedKeywords->get($hostKeyword['keywords']);

                    if (! $hostKeyword['keyword_id']) {
                        info('missing : ' . $hostKeyword['keywords']);
                        throw new \Exception('UpsertedKeywords not found !!!');
                    }

                    unset($hostKeyword['keywords']);

                    return $hostKeyword;
                })->toArray();

                // Upsert pivot table
                HostKeyword::insertOrIgnore($hostKeywordCollection);
            });



        $this->setNbItemsReturned(count($entries));
    }

    protected function getHost(): ?Host
    {
        if (! isset($this->host)) {
            $this->host = $this->findHostByRequestBody();
        }

        return $this->host;
    }

    protected function findHostByRequestBody(): ?Host
    {
        $requestBody = $this->getRequestBody();

        if (isset($requestBody['host'])) {
            return Host::where('hostname', $requestBody['host'])
                ->firstOrFail();
        }

        return null;
    }
}
