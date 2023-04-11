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
use App\Http\Resources\KeywordGuideResource;
use App\Http\Resources\KeywordGuideStatsResource;
use App\Models\Guide;
use App\Models\Project;
use App\Repositories\Filters\MinMaxFilter;
use App\Repositories\Filters\OrderBy;
use App\Repositories\Filters\SearchIncludeExcludeFilter;
use App\Repositories\GuideRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pipeline\Pipeline;

class KeywordsGuidesApiController extends Controller
{
    use HasAction;

    protected GuideRepository $repository;

    protected Pipeline $pipeline;

    public function __construct(GuideRepository $repository, Pipeline $pipeline)
    {
        $this->repository = $repository;
        $this->pipeline = $pipeline;
    }

    public function guides(Request $request, Project $project): mixed
    {
        $query = $this->repository
            ->getGuidesQuery();

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new SearchIncludeExcludeFilter('keywords'),
                new MinMaxFilter('bks'),
                new OrderBy('keywords'),
            ])
            ->thenReturn();

        return $this->processingAction($request, $query, 'g.id') ?:
            KeywordGuideResource::collection($query->get());
    }

    public function stats(Request $request, Project $project, int $yourtextguruGuideId): mixed
    {
        $query = $this->repository->getGuideStatsQuery($yourtextguruGuideId);

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new OrderBy('rank'),
            ])
            ->thenReturn();

        return $this->processingAction($request, $query, 'k.id') ?:
            KeywordGuideStatsResource::collection($query->get());
    }
}
