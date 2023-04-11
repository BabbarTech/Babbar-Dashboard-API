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

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\Job;

class PreventApiTooManyRequests
{
    protected string $cacheKey;

    public function __construct(string $cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * Process the queued job.
     *
     * @param  Job  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {

        // Handle API Too many request (HTTP CODE 429)
        $babbarApiLockedUntil = Cache::get($this->cacheKey);
        if ($babbarApiLockedUntil) {
            //var_dump('BABBAR API LOCKED AFTER TOO MANY ATTEMPTS, release after : ' . $babbarApiLockedUntil);
            $job->release($babbarApiLockedUntil - time());
        } else {
            $next($job);
        }
    }
}
