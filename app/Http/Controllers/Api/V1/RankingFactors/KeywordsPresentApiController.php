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
use App\Http\Resources\ProjectKeywordSerpResource;
use App\Models\Project;
use App\Repositories\Filters\MinMaxFilter;
use App\Repositories\Filters\OrderBy;
use App\Repositories\Filters\SearchIncludeExcludeFilter;
use App\Repositories\HostRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pipeline\Pipeline;

class KeywordsPresentApiController extends Controller
{
    use HasAction;

    protected HostRepository $repository;
    protected Pipeline $pipeline;

    public function __construct(HostRepository $repository, Pipeline $pipeline)
    {
        $this->repository = $repository;
        $this->pipeline = $pipeline;
    }

    public function currentKeywords(Request $request, Project $project): mixed
    {
        $query = $this->repository->getHostKeywordsQuery($project->host);

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new SearchIncludeExcludeFilter('keywords'),
                new SearchIncludeExcludeFilter('urls', 'u.url'),
                new MinMaxFilter('bks'),
                new MinMaxFilter('rank', 'rank'),
                new OrderBy('keywords'),
            ])
            ->thenReturn();

        return $this->processingAction($request, $query, 'hk.keyword_id') ?:
            ProjectKeywordSerpResource::collection($query->get());
    }
}
