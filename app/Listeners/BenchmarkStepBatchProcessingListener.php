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
use App\Events\BenchmarkStepBatchProcessingCanceled;
use App\Events\BenchmarkStepBatchProcessingDone;
use App\Events\BenchmarkStepBatchProcessingFailed;
use App\Events\BenchmarkStepBatchProcessingStart;
use App\Events\BenchmarkStepProcessingDone;
use App\Events\BenchmarkStepProcessingFailed;
use App\Events\BenchmarkStepProcessingStart;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class BenchmarkStepBatchProcessingListener
{
    public function onStart(BenchmarkStepBatchProcessingStart $event): void
    {
        if ($event->benchmarkStepBatch->benchmarkStep->hasAllStepBatchesPending()) {
            event(new BenchmarkStepProcessingStart($event->benchmarkStepBatch->benchmarkStep));
        }

        if ($event->batch->processedJobs() === 0) {
            $event->benchmarkStepBatch->processingStart();
        }

        /*
        if ($event->benchmarkStepBatch->benchmarkStep->status === StatusEnum::ERROR) {
            $event->benchmarkStepBatch->benchmarkStep->retryProcessing();
            $event->benchmarkStepBatch->benchmarkStep->benchmark->retryProcessing();
        }
        */
    }

    public function onError(BenchmarkStepBatchProcessingFailed $event): void
    {
        $event->benchmarkStepBatch->processingError($event->exception);

        event(new BenchmarkStepProcessingFailed(
            $event->benchmarkStepBatch->benchmarkStep,
            $event->exception
        ));
    }

    public function onFinish(BenchmarkStepBatchProcessingDone $event): void
    {
        if (! $event->batch->finished()) {
            return ;
        }

        if (
            $event->benchmarkStepBatch->status !== StatusEnum::ERROR
            && $event->benchmarkStepBatch->status !== StatusEnum::CANCELLED
        ) {
            $event->benchmarkStepBatch->processingDone();
        }

        if ($event->benchmarkStepBatch->benchmarkStep->hasAllStepBatchesDone()) {
            event(new BenchmarkStepProcessingDone($event->benchmarkStepBatch->benchmarkStep));
        }
    }

    public function onCancel(BenchmarkStepBatchProcessingCanceled $event): void
    {
        $event->batch->cancel();
        $event->batch->delete();
        $event->benchmarkStepBatch->processingCancel();
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            BenchmarkStepBatchProcessingStart::class,
            'App\Listeners\BenchmarkStepBatchProcessingListener@onStart'
        );

        $events->listen(
            BenchmarkStepBatchProcessingFailed::class,
            'App\Listeners\BenchmarkStepBatchProcessingListener@onError'
        );

        $events->listen(
            BenchmarkStepBatchProcessingDone::class,
            'App\Listeners\BenchmarkStepBatchProcessingListener@onFinish'
        );

        $events->listen(
            BenchmarkStepBatchProcessingCanceled::class,
            'App\Listeners\BenchmarkStepBatchProcessingListener@onCancel'
        );
    }
}
