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

use App\Models\Host;
use App\Models\Project;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;

class HostRepository
{
    protected Host $model;

    public function __construct(Host $model)
    {
        $this->model = $model;
    }

    public function getHostsWithSameKeywordsQuery(Host $host): EloquentBuilder
    {
        return $this->model
            ->where('id', '!=', $host->getKey())
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('serps as s')
                    ->whereColumn('s.host_id', 'hosts.id');
            });
    }

    public function getNumberOfKeywordsInCommonInTheTop20BenchmarkQuery(Host $host): Builder
    {
        return DB::connection('tenant')
            ->table('hosts_keywords as hk')
            ->select([
                'r.host_id',
                'hosts.hostname',
                'hosts.nb_kw_pos_1_10',
                'hosts.nb_kw_pos_11_20',
                DB::raw('COALESCE(hosts.nb_kw_pos_1_10, 0) + COALESCE(hosts.nb_kw_pos_11_20, 0) as nb_kw_top20'),
                DB::raw('count(r.host_id) as nb_keywords_in_common'),
                's.score',
            ])
            ->join('serps as r', 'hk.keyword_id', '=', 'r.keyword_id')
            ->leftJoin('similar_hosts as s', function ($join) use ($host) {
                $join->on('s.similar_host_id', '=', 'r.host_id')
                    ->where('s.host_id', $host->getKey());
            })
            ->leftJoin('hosts', 'hosts.id', '=', 'r.host_id')
            ->where('hk.host_id', $host->getKey())
            ->where('r.host_id', '!=', $host->getKey())
            ->groupBy([
                    'r.host_id',
                    'hosts.hostname',
                    'hosts.nb_kw_pos_1_10',
                    'hosts.nb_kw_pos_11_20',
                    's.score'
            ]);
    }


    public function getHostKeywordsQuery(
        Host $host,
    ): Builder {
        return DB::connection('tenant')
            ->table('hosts_keywords as hk')
            ->select([
                'hk.keyword_id',
                'hk.rank',
                'k.keywords',
                'k.bks',
                'u.url',
            ])
            ->where('hk.host_id', $host->id)
            //->where('k.lang', $project->getLang())
            ->join('keywords as k', 'k.id', '=', 'hk.keyword_id')
            ->join('urls as u', 'u.id', '=', 'hk.url_id');
    }

    public function getHostKeywordsNotPresentQuery(
        Host $host,
    ): Builder {
        return DB::connection('tenant')
            ->table('hosts_keywords as compKW')
            ->select([
                'compKW.keyword_id',
                'compKW.rank',
                'k.keywords',
                'k.bks',
                'u.url',
            ])
            ->whereNotIn('keyword_id', function ($query) use ($host) {
                $query->select('keyword_id')
                    ->from('hosts_keywords')
                    ->where('hosts_keywords.host_id', $host->id);
            })

            //->where('k.lang', $project->getLang())
            ->join('keywords as k', 'k.id', '=', 'compKW.keyword_id')
            ->join('urls as u', 'u.id', '=', 'compKW.url_id');
    }


    public function getKeywordsInCommonCompetitionQuery(
        Host $host,
        int $competitorHostId,
    ): Builder {

        return DB::connection('tenant')
            ->table('serps as competitor')
            ->select([
                'competitor.*',
                'u.url',
                'k.keywords',
                'k.bks',
                'current.rank as current_rank',
                'u2.url as current_url',
                DB::raw('if(current.rank > competitor.rank, true, false) as competitor_has_better_rank')
            ])
            ->whereExists(function ($query) use ($host) {
                $query->select(DB::raw(1))
                    ->from('hosts_keywords')
                    ->whereColumn('competitor.keyword_id', 'hosts_keywords.keyword_id')
                    ->where('host_id', $host->id);
            })
            ->join('keywords as k', 'competitor.keyword_id', '=', 'k.id')
            ->join('hosts_keywords as current', function ($join) use ($host) {
                $join->on('k.id', '=', 'current.keyword_id')
                    ->where('current.host_id', $host->getKey());
            })
            ->join('urls as u', 'competitor.url_id', '=', 'u.id')
            ->join('urls as u2', 'current.url_id', '=', 'u2.id')
            ->where('competitor.host_id', $competitorHostId);
    }

    public function getHostkeywordsRanksDistributionQuery(int $hostId, int $maxRank = null): Builder
    {
        $query = DB::connection('tenant')
            ->table('hosts_keywords as hk')
            ->select([
                'hk.keyword_id',
                'hk.rank',
                'hk.subrank',
                'k.bks',
                'k.nb_words',
            ])
            ->join('keywords as k', 'k.id', '=', 'hk.keyword_id')
            ->where('hk.host_id', $hostId);

        if ($maxRank) {
            $query->where('hk.rank', '<=', $maxRank);
        }

        return $query;
    }
}
