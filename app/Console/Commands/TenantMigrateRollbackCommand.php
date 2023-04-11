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
use Symfony\Component\Console\Input\InputOption;

class TenantMigrateRollbackCommand extends Command
{
    use TenantAware;

    protected $name = 'tenants:migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last database migration on tenant(s)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Rollback ' . Project::current()?->name);

        $options = [];
        foreach ($this->options() as $name => $params) {
            if ($name === 'tenant') {
                continue;
            }

            $options['--' . $name] = $params;
        }

        return $this->call('migrate:rollback', $options);
    }

    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use', 'tenant'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],

            ['path', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The path(s) to the migrations files to be executed', ['database/migrations/tenant']],

            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],

            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run'],

            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted'],

            ['tenant', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Tenant(s)'],
        ];
    }
}
