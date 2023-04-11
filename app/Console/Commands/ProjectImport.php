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

namespace App\Console\Commands;

use App\Console\Commands\Traits\TenantMandatory;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\DbSnapshots\Commands\Concerns\AsksForSnapshotName;

class ProjectImport extends Command
{
    use TenantMandatory;
    use AsksForSnapshotName;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:import {filename?} {--tenant=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump project database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $project = Project::current();

        if (! $project) {
            throw new ModelNotFoundException();
        }

        $this->line('Processing ' . $project->tenant_key);

        $connection = config('multitenancy.tenant_database_connection_name');

        $filename = ($this->argument('filename') ?: $this->askForSnapshotName());

        return $this->call('snapshot:load', [
            'name' => $filename,
            '--connection' => $connection,
        ]);
    }
}
