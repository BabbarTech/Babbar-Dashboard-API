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

namespace App\Repositories;

use App\Enums\StatusEnum;
use App\Models\Host;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;

class ProjectRepository
{
    protected Project $model;

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    public function getHostnameForUrl(string $url): string
    {
        $hostname = parse_url($url, PHP_URL_HOST);

        if (! is_string($hostname)) {
            throw new \Exception('Hostname parsing error');
        }

        return $hostname;
    }

    public function getDomainForUrl(string $url): string
    {
        $domain = get_domain($url);

        if (! is_string($domain)) {
            throw new \Exception('Domain parsing error');
        }

        return $domain;
    }

    /**
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBenchmarksLatestStatus(Project $project)
    {
        return $project->host
            ->benchmarks()
            ->select('benchmarks.*')
            ->leftJoin('benchmarks as b2', function ($join) {
                $join->on('benchmarks.type', '=', 'b2.type')
                    ->whereRaw('benchmarks.id < b2.id');
            })
            ->whereNull('b2.id')
            ->get();
    }



    public function getCompetitorHostnameNotAlreadyBenchmarkedQuery(Project $project): EloquentBuilder
    {
        return $this->getCompetitorHostnameQuery($project, false);
    }

    public function getCompetitorHostnameBenchmarkedQuery(Project $project): EloquentBuilder
    {
        return $this->getCompetitorHostnameQuery($project, true);
    }

    protected function getCompetitorHostnameQuery(Project $project, bool $isBenchmarked): EloquentBuilder
    {
        $method = $isBenchmarked ? 'whereExists' : 'whereNotExists';

        return Host::where('id', '!=', $project->host_id)
            ->orderBy('hostname')
            ->$method(function ($query) {
                $query->select(DB::raw(1))
                    ->from('benchmarks')
                    ->where('type', 'like', '%RankingFactorCompetitorsKeywordsBenchmark')
                    ->where('benchmarks.benchmarkable_type', 'host')
                    //->where('benchmarks.status', StatusEnum::DONE)
                    ->whereRaw('benchmarks.benchmarkable_id = hosts.id');
            });
    }
}
