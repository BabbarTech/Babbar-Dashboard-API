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

namespace App\Listeners;

use App\Enums\StatusEnum;
use App\Events\BenchmarkProcessingDone;
use App\Events\BenchmarkProcessingFailed;
use App\Events\BenchmarkProcessingStart;
use App\Events\BenchmarkStepBatchProcessingCanceled;
use App\Events\BenchmarkStepProcessingCanceled;
use App\Events\BenchmarkStepProcessingDone;
use App\Events\BenchmarkStepProcessingFailed;
use App\Events\BenchmarkStepProcessingStart;
use App\Models\BenchmarkStep;
use App\Models\BenchmarkStepBatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class BenchmarkStepProcessingListener
{
    public function onStart(BenchmarkStepProcessingStart $event): void
    {
        if ($event->benchmarkStep->position === 1) {
            event(new BenchmarkProcessingStart($event->benchmarkStep->benchmark));
        }

        if ($event->benchmarkStep->status === StatusEnum::PENDING) {
            $event->benchmarkStep->increment('attempts');
            $event->benchmarkStep->processingStart();
        }
    }

    public function onError(BenchmarkStepProcessingFailed $event): void
    {
        $event->benchmarkStep->processingError($event->exception);

        $this->cancelJobBatches($event->benchmarkStep);

        event(new BenchmarkProcessingFailed($event->benchmarkStep->benchmark, $event->exception));
    }

    public function onFinish(BenchmarkStepProcessingDone $event): void
    {
        $event->benchmarkStep->processingDone();

        // Prevent multiple process
        if ($event->benchmarkStep->wasChanged()) {
            $event->benchmarkStep->getNextStep()?->process();

            // Check if all steps are done
            if ($event->benchmarkStep->benchmark->hasAllStepsDone()) {
                event(new BenchmarkProcessingDone($event->benchmarkStep->benchmark));
            }
        }
    }

    public function onCancel(BenchmarkStepProcessingCanceled $event): void
    {
        // Cancel all steps not processed
        $this->cancelJobBatches($event->benchmarkStep);

        $event->benchmarkStep->processingCancel();
    }

    protected function cancelJobBatches(BenchmarkStep $benchmarkStep): void
    {
        $benchmarkStep->batches()
            ->whereIn('status', [StatusEnum::PROCESSING, StatusEnum::PENDING])
            ->each(function ($benchmarkStepBatch) {
                /** @var BenchmarkStepBatch $benchmarkStepBatch */

                $batch = $benchmarkStepBatch->getQueueBatch();
                if (! empty($batch)) {
                    event(new BenchmarkStepBatchProcessingCanceled($batch, $benchmarkStepBatch));
                }
            });
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            BenchmarkStepProcessingStart::class,
            'App\Listeners\BenchmarkStepProcessingListener@onStart'
        );

        $events->listen(
            BenchmarkStepProcessingFailed::class,
            'App\Listeners\BenchmarkStepProcessingListener@onError'
        );

        $events->listen(
            BenchmarkStepProcessingDone::class,
            'App\Listeners\BenchmarkStepProcessingListener@onFinish'
        );

        $events->listen(
            BenchmarkStepProcessingCanceled::class,
            'App\Listeners\BenchmarkStepProcessingListener@onCancel'
        );
    }
}
