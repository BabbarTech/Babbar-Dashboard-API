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

namespace App\Actions;

use App\Actions\Traits\KeywordsRankHelpers;
use App\Http\Controllers\Controller;
use App\Models\Host;
use App\Models\Project;
use App\Repositories\HostRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KeywordsRankExportToCSVAction extends Controller
{
    use KeywordsRankHelpers;

    protected HostRepository $repository;

    protected int $rankMax;

    protected string $csvSeparator;

    public function __construct(HostRepository $repository)
    {
        $this->repository = $repository;
        $this->rankMax = intval(config('benchmarks.keywords-ranks.max', 100));
        $this->csvSeparator = strval(config('export.csv.separator'));
    }

    public function __invoke(Request $request, Project $project, string $exportType): StreamedResponse
    {
        $validated = $request->validate([
            'hostnames' => 'required|array',
        ]);

        // create csv
        return response()->streamDownload(function () use ($validated, $exportType) {
            /** @var resource $file */
            $file = fopen('php://output', 'w+');

            if ($exportType === 'per-page') {
                $perAttribute = 'page';
                $perMethod = 'keywordsRanksPerPage';
            } else {
                $perAttribute = 'rank';
                $perMethod = 'keywordsRanksPerPosition';
            }

            $collection = Host::whereIn('hostname', $validated['hostnames'])
            ->get()
            ->map(function ($host) use ($perAttribute, $perMethod) {
                $ranksDistribution = $this->repository
                    ->getHostkeywordsRanksDistributionQuery($host->id, $this->rankMax)
                    ->get();

                return $this->{$perMethod}($ranksDistribution)
                    ->map(function ($stat) use ($host, $perAttribute) {

                        $nbKeywords = Arr::get($stat, 'nb_keywords');

                        if (empty($nbKeywords)) {
                            return null;
                        }

                        return [
                            $perAttribute => Arr::get($stat, $perAttribute),
                            'hostname' => $host->hostname,
                            'nb_keywords' => $nbKeywords,
                            'median_bks' => Arr::get($stat, 'median_bks'),
                            'average_bks' => Arr::get($stat, 'average_bks'),
                            'average_nb_words' => Arr::get($stat, 'average_nb_words'),
                        ];
                    });
            })->collapse()
                ->filter()
                ->sortBy($perAttribute)
                ->values();

            $collection->each(function ($data, $delta) use ($file) {
                /** @var array $data */

                // Push columns header
                if ($delta === 0) {
                    $columns = array_keys($data);
                    fputcsv($file, $columns, $this->csvSeparator);
                }

                fputcsv($file, $data, $this->csvSeparator);
            });

            fclose($file);
        }, $this->getFilename($project, $exportType));
    }

    protected function getFilename(Project $project, string $exportType): string
    {
        return implode('__', [
            $project->hostname,
            'keywords_rank',
            $exportType,
            date('Y-m-d'),
        ]) . '.csv';
    }
}
