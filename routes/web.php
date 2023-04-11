<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/installation', [\App\Http\Controllers\Front\InstallationController::class, 'index'])
    ->name('installation');

Route::post('/installation', [\App\Http\Controllers\Front\InstallationController::class, 'register'])
    ->name('installation.register');


Route::middleware(['auth', 'app-installed'])
    ->group(function () {

        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

        Route::get('/', [App\Http\Controllers\Front\HomeController::class, 'index'])
            ->name('home');

        Route::post('retry-failed-jobs', function () {
            \Illuminate\Support\Facades\Artisan::call('queue:retry all');
            return redirect()->back()->withSuccess('Failed queue jobs reloaded into queue');
        })->name('retry-failed-jobs');

        Route::resource('projects', \App\Http\Controllers\Front\ProjectController::class)
            ->names('projects');

        Route::group([
            'middleware' => 'tenant',
            'prefix' => '{project}',
            'as' => 'projects.',
        ], function () {

            Route::get('benchmarks', [App\Http\Controllers\Front\ProjectBenchmarkController::class, 'index'])
                ->name('benchmarks.index');

            Route::get('benchmarks/{benchmark}', [App\Http\Controllers\Front\ProjectBenchmarkController::class, 'show'])
                ->name('benchmarks.show');

            Route::get('benchmarks/{benchmark}/{benchmark_step}/batch-errors', [App\Http\Controllers\Front\ProjectBenchmarkController::class, 'batchErrors'])
                ->name('benchmarks.steps.batch-errors');

            Route::post('benchmarks/{benchmark}/cancel', [App\Http\Controllers\Front\ProjectBenchmarkController::class, 'cancel'])
                ->name('benchmarks.cancel');

            Route::get('benchmarks/fire/{benchmarkServiceName}', [App\Http\Controllers\Front\ProjectBenchmarkController::class, 'fire'])
                ->name('benchmarks.fire');

            Route::post('actions/fetch-competitor-keywords', \App\Actions\FetchCompetitorKeywordsAction::class)
                ->name('actions.fetch-competitor-keywords');

            Route::get('ranking-factors/keywords-in-common', \App\Http\Controllers\Front\RankingFactors\KeywordsInCommonController::class)
                ->name('ranking-factors.keywords-in-common');

            Route::get('ranking-factors/keywords-present', \App\Http\Controllers\Front\RankingFactors\KeywordsPresentController::class)
                ->name('ranking-factors.keywords-present');

            Route::get('ranking-factors/keywords-not-present', \App\Http\Controllers\Front\RankingFactors\KeywordsNotPresentController::class)
                ->name('ranking-factors.keywords-not-present');

            Route::get('ranking-factors/keywords-guides', \App\Http\Controllers\Front\RankingFactors\KeywordsGuidesController::class)
                ->name('ranking-factors.keywords-guides');

            Route::get('ranking-factors/keywords-ranks', \App\Http\Controllers\Front\RankingFactors\KeywordsRankController::class)
                ->name('ranking-factors.keywords-ranks');

            Route::post('ranking-factors/keywords-ranks/export-csv/{type}', \App\Actions\KeywordsRankExportToCSVAction::class)
                ->name('ranking-factors.keywords-ranks.actions.export-csv')
                ->whereIn('type', ['per-page', 'per-rank']);

            Route::get('ranking-factors/backlinks', \App\Http\Controllers\Front\RankingFactors\BacklinksController::class)
                ->name('ranking-factors.backlinks');
        });
    });


Route::middleware(['is_admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('api-tokens', [\App\Http\Controllers\Front\Admin\ApiTokenController::class, 'index'])
            ->name('api_tokens');

        Route::post('api-tokens', [\App\Http\Controllers\Front\Admin\ApiTokenController::class, 'store'])
            ->name('api_tokens.store');
    });

Route::get('/mock', function () {
    // defining a route in Laravel
    set_time_limit(0);              // making maximum execution time unlimited
    ob_implicit_flush(1);           // Send content immediately to the browser on every statement which produces output
    ob_end_flush();                 // deletes the topmost output buffer and outputs all of its contents

    sleep(1);
    echo json_encode(['data' => 'test 1']);

    sleep(2);
    echo json_encode(['data' => 'test 2']);

    sleep(1);
    echo json_encode(['data' => 'test 3']);
    die(1);
});
