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

namespace App\Http\Controllers\Front\RankingFactors;

use App\Http\Controllers\Controller;
use App\Models\Host;
use App\Models\Project;
use App\Repositories\Actions\ExportToCsvAction;
use App\Repositories\Actions\YtgFetchGuideAction;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeywordsNotPresentController extends Controller
{
    public function __invoke(Request $request, ProjectRepository $repository, Project $project): View
    {
        $competitorsBenchmarked = $repository->getCompetitorHostnameBenchmarkedQuery($project)
            ->select('hostname', 'id')
            ->get();

        $mainDatasetPayload = collect([
            'filters' => [
                'keywords' => [
                    'include' => null,
                    'exclude' => null,
                ],
                'urls' => [
                    'include' => null,
                    'exclude' => null,
                ],
                'bks' => [
                    'min' => 20,
                    'max' => 60,
                ],
                'rank' => [
                    'min' => null,
                    'max' => null,
                ],
                'competitors' => [],
            ],
            'orderBy' => 'rank'
        ]);

        $mainActions = collect([
            new YtgFetchGuideAction(),
            new ExportToCsvAction(),
        ]);

        return view('resources.ranking-factors.keywords-not-present', [
            'project' => $project,
            'mainDatasetPayload' => $mainDatasetPayload,
            'mainActions' => $mainActions,
            'competitorsBenchmarked' => $competitorsBenchmarked->map(function ($competitor) {
                /** @var \stdClass $competitor */
                return [
                    'label' => $competitor->hostname,
                    'value' => $competitor->id,
                ];
            }),
        ]);
    }
}
