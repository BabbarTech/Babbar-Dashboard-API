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

namespace App\Services\Tenant;

use Illuminate\Http\Request;
use Spatie\Multitenancy\TenantFinder\TenantFinder;
use App\Models\Project;

class TenantProjectFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Project
    {
        if (preg_match('#^\/api\/v1/([0-9]+)/#', $request->getRequestUri(), $matches)) {
            return Project::where('id', $matches[1])->first();
        }

        if (preg_match('#^/([0-9]+)/#', $request->getRequestUri(), $matches)) {
            return Project::where('id', $matches[1])->first();
        }

        return null;
    }
}
