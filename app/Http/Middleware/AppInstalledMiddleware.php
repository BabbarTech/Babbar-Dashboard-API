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

use App\Models\Gateway;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AppInstalledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Redirect if no user admin exists in database
        if (! $this->hasAdminUser()) {
            return redirect()->route('installation');
        }

        // Redirect to API Tokens configuration page if no BABBAR token is set
        $user = $request->user();
        if (! $this->hasBabbarApiToken() && $user) {
            return $user->isAdmin() ?
                redirect()->route('admin.api_tokens') :
                abort(403, 'App not installed yet !');
        }

        return $next($request);
    }

    protected function isAppInstalled(): bool
    {
        if ($this->hasAdminUser() && $this->hasBabbarApiToken()) {
            return true;
        }

        return false;
    }

    protected function hasAdminUser(): bool
    {
        return User::where('is_admin', true)->count() > 0;
    }

    protected function hasBabbarApiToken(): bool
    {
        return Gateway::where('name', Gateway::BABBAR)
            ->whereNotNull('api_token')
            ->count() > 0;
    }
}
