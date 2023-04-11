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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BacklinksController extends Controller
{
    public function __invoke(Request $request, Project $project): View
    {
        $mainDatasetPayload = collect([
            'filters' => [
                'source_urls' => [
                    'include' => null,
                    'exclude' => null,
                ],
                'target_urls' => [
                    'include' => null,
                    'exclude' => null,
                ],
                'page_value' => [
                    'min' => null,
                    'max' => null,
                ],
                'page_trust' => [
                    'min' => null,
                    'max' => null,
                ],
                'semantic_value' => [
                    'min' => null,
                    'max' => null,
                ],
                'babbar_authority_score' => [
                    'min' => null,
                    'max' => null,
                ],
                'source_nb_keywords_in_top20' => [
                    'min' => null,
                    'max' => null,
                ],
                'source_nb_backlinks' => [
                    'min' => null,
                    'max' => null,
                ],
                'induced_strength' => [
                    'min' => null,
                    'max' => null,
                ],
                'induced_strength_confidence' => null,
            ],
            'orderBy' => null,
        ]);

        $detailDatasetPayload = collect([
            'filters' => [],
            'orderBy' => null,
        ]);

        $mainActions = collect([
            new ExportToCsvAction(),
        ]);

        return view('resources.ranking-factors.backlinks', [
            'project' => $project,
            'mainDatasetPayload' => $mainDatasetPayload,
            'detailDatasetPayload' => $detailDatasetPayload,
            'mainActions' => $mainActions,
        ]);
    }
}
