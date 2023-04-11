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

namespace App\Console;

use App\Console\Commands\TenantCleanFinishedBatch;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(TenantCleanFinishedBatch::class)->everyMinute();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('horizon:terminate')->hourly();
        $schedule->command('queue:prune-batches --hours=2 --unfinished=48')->hourly();
        $schedule->command('queue:prune-failed --hours=24')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
