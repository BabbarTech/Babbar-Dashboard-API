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

namespace App\Http\Controllers\Api\V1\RankingFactors;

use App\Http\Controllers\Api\V1\Traits\HasAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommonKeywordCompetitorHostResource;
use App\Http\Resources\CommonKeywordCompetitorKeywordResource;
use App\Http\Resources\HostResource;
use App\Models\Host;
use App\Models\Project;
use App\Repositories\Filters\BooleanFilter;
use App\Repositories\Filters\GroupingFilter;
use App\Repositories\Filters\MinMaxFilter;
use App\Repositories\Filters\OrderBy;
use App\Repositories\Filters\SearchIncludeExcludeFilter;
use App\Repositories\HostRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class KeywordsInCommonApiController extends Controller
{
    use HasAction;

    protected HostRepository $repository;

    protected Pipeline $pipeline;

    public function __construct(HostRepository $repository, Pipeline $pipeline)
    {
        $this->repository = $repository;
        $this->pipeline = $pipeline;
    }

    public function competitors(Request $request, Project $project): mixed
    {
        $query = $this->repository
            ->getNumberOfKeywordsInCommonInTheTop20BenchmarkQuery($project->host);

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new SearchIncludeExcludeFilter('hostname'),
                (new MinMaxFilter('nb_kw_top20'))
                    ->setMethod('having'),
                (new MinMaxFilter('nb_keywords_in_common'))
                    ->setMethod('having'),
                (new MinMaxFilter('similar_score_percent'))
                    ->setColumn('score')
                    ->setRatio(0.01),
                new OrderBy('nb_keywords_in_common', 'desc'),
            ])
            ->thenReturn();

        return $this->processingAction($request, $query, 'r.host_id') ?:
            CommonKeywordCompetitorHostResource::collection($query->get());
    }

    public function commonKeywords(Request $request, Project $project, Host $host): mixed
    {
        /** @var int $competitorHostId */
        $competitorHostId = $host->getKey();

        $query = $this->repository->getKeywordsInCommonCompetitionQuery($project->host, $competitorHostId);

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new SearchIncludeExcludeFilter('keywords'),
                new MinMaxFilter('bks'),
                (new MinMaxFilter('current_rank', 'current_rank'))
                    ->setMethod('having'),
                new MinMaxFilter('competitor_rank', 'competitor.rank'),
                new BooleanFilter('competitor_has_better_rank'),
                (new GroupingFilter('group_keywords', 'hk.keyword_id'))
                    ->setApply(function (Builder $query) use ($competitorHostId) {

                        $bests = DB::connection('tenant')
                            ->table('serps')
                            ->select([
                                'serps.id',
                                'serps.url_id',
                                DB::raw('RANK() OVER (PARTITION BY serps.keyword_id ORDER BY serps.rank ASC) AS krank')
                            ])
                            ->where('serps.host_id', $competitorHostId);

                        return $query
                            ->joinSub($bests, 'best', function ($join) {
                                $join->on('competitor.id', '=', 'best.id')
                                    ->where('best.krank', 1)
                                ;
                            });
                    }),
                new OrderBy('competitor.rank'),
            ])
            ->thenReturn();

        return $this->processingAction($request, $query, 'k.id') ?:
            CommonKeywordCompetitorKeywordResource::collection($query->get())
                ->additional(['host' => new HostResource($host)]);
    }
}
