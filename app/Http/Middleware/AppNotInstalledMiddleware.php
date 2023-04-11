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

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class AppNotInstalledMiddleware extends AppInstalledMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $this->isAppInstalled()) {
            return $next($request);
        }

        $routeName = $request->user() ? 'home' : 'login';

        return redirect()
            ->route($routeName)
            ->with('warning', __('App already installed'));
    }
}
