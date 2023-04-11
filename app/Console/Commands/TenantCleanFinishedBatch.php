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

use App\Enums\StatusEnum;
use App\Events\BenchmarkStepBatchProcessingDone;
use App\Models\BenchmarkStepBatch;
use App\Models\Project;
use Illuminate\Console\Command;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Illuminate\Support\Facades\Log;

class TenantCleanFinishedBatch extends Command
{
    use TenantAware;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:fix-endless-step {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force Done status to finished benchmark step';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line(Project::current()?->name . '---------------------------------');
        BenchmarkStepBatch::where('status', StatusEnum::PROCESSING)
            ->whereRaw('batch_finished_jobs >= batch_total_jobs')
            ->get()
            ->map(function (BenchmarkStepBatch $benchmarkStepBatch) {

                $message = 'Force finished BenchmarkStepBatch id : ' . $benchmarkStepBatch->id;
                info($message);
                $this->line($message);

                $batch = $benchmarkStepBatch->getQueueBatch();
                if ($batch) {
                    event(new BenchmarkStepBatchProcessingDone($batch));
                    $batch->delete();
                } else {
                    Log::warning('Batch #' . $benchmarkStepBatch->batch_id . ' to finish not found !');
                }
            });
    }
}
