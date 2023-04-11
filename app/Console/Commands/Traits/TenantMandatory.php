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

namespace App\Console\Commands\Traits;

use App\Models\Project;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

trait TenantMandatory
{
    use TenantAware {
        execute as spatieExecute;
    }

    protected function execute(InputInterface $input, OutputInterface $output): mixed
    {
        $tenant = Arr::wrap($this->option('tenant'));

        if (empty($tenant)) {
            $this->input->setOption('tenant', $this->askForTenantKey());
        }

        return $this->spatieExecute($input, $output);
    }


    public function askForTenantKey(): string|array
    {
        $names = Project::latest()->pluck('database')->toArray();

        return $this->choice('Which project?', $names, "0");
    }
}
