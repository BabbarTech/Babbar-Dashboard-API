<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Todo: add auth:sanctum protection middleware
Route::prefix('v1/{project}')
    ->as('api.v1.')
    ->middleware('tenant')
    ->group(function () {

        Route::get('ranking-factors/keywords-in-common', [\App\Http\Controllers\Api\V1\RankingFactors\KeywordsInCommonApiController::class, 'competitors'])
            ->name('ranking-factors.keywords-in-common');

        Route::get('ranking-factors/keywords-in-common/{host}/common-keywords', [\App\Http\Controllers\Api\V1\RankingFactors\KeywordsInCommonApiController::class, 'commonKeywords'])
            ->name('ranking-factors.keywords-in-common.common-keywords');

        Route::get('ranking-factors/keywords-present', [\App\Http\Controllers\Api\V1\RankingFactors\KeywordsPresentApiController::class, 'currentKeywords'])
            ->name('ranking-factors.keywords-present');

        Route::get('ranking-factors/keywords-not-present', [\App\Http\Controllers\Api\V1\RankingFactors\KeywordsNotPresentApiController::class, 'currentKeywords'])
            ->name('ranking-factors.keywords-not-present');

        Route::get('ranking-factors/keywords-guides', [\App\Http\Controllers\Api\V1\RankingFactors\KeywordsGuidesApiController::class, 'guides'])
            ->name('ranking-factors.keywords-guides');

        Route::get('ranking-factors/keywords-guides/{yourtextguru_guide_id}/stats', [\App\Http\Controllers\Api\V1\RankingFactors\KeywordsGuidesApiController::class, 'stats'])
            ->name('ranking-factors.keywords-guides.stats');

        Route::get('ranking-factors/keywords-ranks-distribution/{host_id}', [\App\Http\Controllers\Api\V1\RankingFactors\KeywordsRankApiController::class, 'keywordsRanksDistribution'])
            ->name('ranking-factors.keywords-ranks-distribution');

        Route::get('ranking-factors/backlinks/{host}', [\App\Http\Controllers\Api\V1\RankingFactors\BacklinksApiController::class, 'index'])
            ->name('ranking-factors.backlinks');

        Route::get('ranking-factors/backlinks/{host}/{backlink}/source-keywords', [\App\Http\Controllers\Api\V1\RankingFactors\BacklinksApiController::class, 'sourceKeywords'])
            ->name('ranking-factors.backlinks.source-keywords');

        Route::get('ranking-factors/backlinks/{host}/{backlink}/source-backlinks', [\App\Http\Controllers\Api\V1\RankingFactors\BacklinksApiController::class, 'sourceBacklinks'])
            ->name('ranking-factors.backlinks.source-backlinks');
    });

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
