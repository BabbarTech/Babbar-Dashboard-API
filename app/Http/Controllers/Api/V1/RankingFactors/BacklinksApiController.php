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
use App\Http\Resources\BacklinkDetailResource;
use App\Http\Resources\BacklinkResource;
use App\Models\Host;
use App\Models\Backlink;
use App\Models\Project;
use App\Repositories\Filters\MinMaxFilter;
use App\Repositories\Filters\OrderBy;
use App\Repositories\Filters\SearchIncludeExcludeFilter;
use App\Repositories\Filters\OptionFilter;
use App\Repositories\BacklinkRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pipeline\Pipeline;

class BacklinksApiController extends Controller
{
    use HasAction;

    protected BacklinkRepository $repository;
    protected Pipeline $pipeline;

    public function __construct(BacklinkRepository $repository, Pipeline $pipeline)
    {
        $this->repository = $repository;
        $this->pipeline = $pipeline;
    }

    public function index(Request $request, Project $project, Host $host): mixed
    {
        $query = $this->repository->getHostBacklinksQuery($host);

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new SearchIncludeExcludeFilter('source_urls', 's.url'),
                new SearchIncludeExcludeFilter('target_urls', 't.url'),
                new MinMaxFilter('page_value'),
                new MinMaxFilter('page_trust'),
                new MinMaxFilter('semantic_value'),
                new MinMaxFilter('babbar_authority_score'),
                new MinMaxFilter('induced_strength'),
                (new MinMaxFilter('source_nb_keywords_in_top20'))->setMethod('having'),
                new MinMaxFilter('source_nb_backlinks'),
                new OptionFilter('induced_strength_confidence'),
                new OrderBy('semantic_value', 'desc'),
            ])
            ->thenReturn();

        return $this->processingAction($request, $query, 'bl.id') ?:
            BacklinkResource::collection($query->get());
    }

    public function sourceKeywords(Request $request, Project $project, Host $host, Backlink $backlink): mixed
    {
        $sourceUrl = $backlink->sourceUrl()->firstOrFail();

        $query = $sourceUrl->keywords()->getQuery()->toBase();

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new OrderBy('bks', 'desc'),
            ])
            ->thenReturn();

        return new BacklinkDetailResource($query->get());
    }

    public function sourceBacklinks(Request $request, Project $project, Host $host, Backlink $backlink): mixed
    {
        $query = $this->repository->getSourceBacklinksQuery($backlink);

        /** @var Builder $query */
        $query = $this->pipeline->send($query)
            ->through([
                new OrderBy('induced_strength', 'desc'),
            ])
            ->thenReturn();

        return new BacklinkDetailResource($query->get());
    }
}
