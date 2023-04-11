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

use App\Actions\Traits\KeywordsRankHelpers;
use App\Http\Controllers\Api\V1\Traits\HasAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\KeywordRankPerPageResource;
use App\Models\Project;
use App\Repositories\HostRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KeywordsRankApiController extends Controller
{
    use HasAction;
    use KeywordsRankHelpers;

    protected HostRepository $repository;

    public function __construct(HostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function keywordsRanksDistribution(Request $request, Project $project, int $hostId): JsonResource
    {
        $rankMax = intval(config('benchmarks.keywords-ranks.max', 100));
        $query = $this->repository->getHostkeywordsRanksDistributionQuery($hostId, $rankMax);

        $data = $query->get();

        $collection = collect([
            'per-page' => $this->keywordsRanksPerPage($data),
            'per-rank' => $this->keywordsRanksPerPosition($data),
        ]);

        return KeywordRankPerPageResource::collection($collection);
    }
}
