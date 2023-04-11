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

namespace App\Providers;

use App\Exceptions\GatewayApiAccountNotFoundException;
use App\Models\Gateway;
use App\Models\Project;
use App\Observers\ProjectObserver;
use App\Services\Api\Contracts\GatewayApiClient;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Project::observe(ProjectObserver::class);

        Relation::enforceMorphMap([
            'domain' => 'App\Models\Domain',
            'host' => 'App\Models\Host',
            'url' => 'App\Models\Url',
            'project' => 'App\Models\Project',
            'keyword' => 'App\Models\Keyword',
        ]);

        Paginator::useBootstrapFive();

        Blade::if('isAdmin', function ($guard = null) {
            $user = auth($guard)->user();
            return ($user && $user->is_admin);
        });


        RateLimiter::for(GatewayApiClient::class, function ($job) {
            return $job->getRateLimit();
        });
    }
}
