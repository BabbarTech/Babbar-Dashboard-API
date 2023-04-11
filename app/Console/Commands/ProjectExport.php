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

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class ProjectExport extends Command
{
    use TenantAware;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:export {--tenant=*}';

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

        return $this->call('snapshot:create', [
            'name' => $project->database,
            '--compress' => true,
            '--connection' => $connection,
        ]);
    }
}
