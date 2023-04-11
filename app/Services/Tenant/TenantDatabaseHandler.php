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

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class TenantDatabaseHandler
{
    public const TENANT_DATABASE_PREFIX = 'project_';

    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getPrefix(): string
    {
        /** @var string $prefix */
        $prefix = env('DB_TENANT_PREFIX', self::TENANT_DATABASE_PREFIX);

        return $prefix;
    }

    public function makeDatabaseName(): string
    {
        $cleanedHostname = preg_replace('#(\.|-)#', '_', $this->project->hostname);

        $name = implode('_', [
            $cleanedHostname,
            date('YmdHi'),
            $this->project->serp->value,
        ]);

        return $this->getPrefix() . $name;
    }

    public function createDatabase(): void
    {
        DB::statement('CREATE DATABASE ' . $this->project->database);

        Artisan::call('tenants:artisan', [
            'artisanCommand' => 'migrate --database=tenant --path=database/migrations/tenant --force',
            '--tenant' => $this->project->tenant_key,
        ]);
    }

    public function dropDatabase(): bool
    {
        if (! preg_match('/^' . $this->getPrefix() . '/', $this->project->database)) {
            throw new \Exception('Drop Database forbidden');
        }

        return DB::statement('DROP DATABASE IF EXISTS ' . $this->project->database);
    }
}
