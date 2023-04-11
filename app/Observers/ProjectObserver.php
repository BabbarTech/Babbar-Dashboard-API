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

namespace App\Observers;

use App\Models\Host;
use App\Models\Project;
use App\Models\ProjectDetail;
use App\Repositories\ProjectRepository;
use App\Services\Tenant\TenantDatabaseHandler;
use Illuminate\Support\Arr;

class ProjectObserver
{
    protected ProjectRepository $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function creating(Project $project): void
    {
        if (empty($project->hostname)) {
            $project->hostname = $this->repository->getHostnameForUrl($project->url);
        }

        if (empty($project->domain)) {
            $project->domain = $this->repository->getDomainForUrl($project->url);
        }

        // Fake until created
        $project->host_id = 1;

        $project->tenant_key = $project->makeTenantKey();

        $project->database = (new TenantDatabaseHandler($project))
            ->makeDatabaseName();
    }


    public function created(Project $project): void
    {
        $tenantDatabaseHandler = new TenantDatabaseHandler($project);
        try {
            $tenantDatabaseHandler->createDatabase();
        } catch (\Exception $e) {
            $tenantDatabaseHandler->dropDatabase();
            $project->forceDelete();
            throw $e;
        }

        $project->makeCurrent();

        $projectDetailAttributes = Arr::only($project->toArray(), [
            'domain',
            'hostname',
            'url',
            'database',
            'serp',
            'description'
        ]);

        ProjectDetail::create($projectDetailAttributes);

        $host = Host::firstOrCreate(['hostname' => $project->hostname]);
        $project->update(['host_id' => $host->id]);
    }

    public function deleting(Project $project): void
    {
        $tenantDatabaseHandler = new TenantDatabaseHandler($project);
        $tenantDatabaseHandler->dropDatabase();
    }

    /*
    public function forceDeleted(Project $project): void
    {
        $tenantDatabaseHandler = new TenantDatabaseHandler($project);
        $tenantDatabaseHandler->dropDatabase();
    }
    */
}
