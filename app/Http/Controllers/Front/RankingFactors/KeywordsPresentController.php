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
use App\Http\Resources\ProjectKeywordSerpResource;
use App\Models\Project;
use App\Repositories\Actions\ExportToCsvAction;
use App\Repositories\Actions\YtgFetchGuideAction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeywordsPresentController extends Controller
{
    public function __invoke(Request $request, Project $project): View
    {
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
                    'min' => 10,
                    'max' => null,
                ],
            ],
            'orderBy' => 'rank'
        ]);

        $mainActions = collect([
            new YtgFetchGuideAction(),
            (new ExportToCsvAction())
                ->setParams([
                    'transformer' => ProjectKeywordSerpResource::class,
                ]),
        ]);

        return view('resources.ranking-factors.keywords-present', [
            'project' => $project,
            'mainDatasetPayload' => $mainDatasetPayload,
            'mainActions' => $mainActions,
        ]);
    }
}
