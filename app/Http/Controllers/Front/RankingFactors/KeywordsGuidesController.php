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
use App\Models\Project;
use App\Repositories\Actions\ExportToCsvAction;
use App\Repositories\Actions\KeywordsGuidesExportToCsvAction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeywordsGuidesController extends Controller
{
    public function __invoke(Request $request, Project $project): View
    {
        $mainDatasetPayload = collect([
            'filters' => [
                'keywords' => [
                    'include' => null,
                    'exclude' => null,
                ],
                'bks' => [
                    'min' => null,
                    'max' => null,
                ],
            ],
            'orderBy' => '-yourtextguru_guide_id'
        ]);

        $detailDatasetPayload = collect([
            'orderBy' => 'rank'
        ]);

        $mainActions = collect([
            new ExportToCsvAction(),
            new KeywordsGuidesExportToCsvAction(),
        ]);

        $detailActions = collect([]);

        return view('resources.ranking-factors.keywords-guides', [
            'project' => $project,
            'mainDatasetPayload' => $mainDatasetPayload,
            'detailDatasetPayload' => $detailDatasetPayload,
            'mainActions' => $mainActions,
            'detailActions' => $detailActions,
        ]);
    }
}
