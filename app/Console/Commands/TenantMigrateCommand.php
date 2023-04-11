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
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class TenantMigrateCommand extends Command
{
    use TenantAware;

    protected $signature = 'tenants:migrate {--database=tenant : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path=database/migrations/tenant : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--schema-path= : The path to a schema dump file}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}
                {--tenant=*}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the database migrations on tenant(s)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Migrate ' . Project::current()?->name);

        $options = [];
        foreach ($this->options() as $name => $params) {
            if ($name === 'tenant') {
                continue;
            }

            $options['--' . $name] = $params;
        }

        return $this->call('migrate', $options);
    }
}
