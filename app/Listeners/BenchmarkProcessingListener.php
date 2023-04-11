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
use App\Events\BenchmarkProcessingCanceled;
use App\Events\BenchmarkProcessingDone;
use App\Events\BenchmarkProcessingFailed;
use App\Events\BenchmarkProcessingStart;
use App\Events\BenchmarkStepProcessingCanceled;
use App\Models\BenchmarkStep;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class BenchmarkProcessingListener
{
    public function onStart(BenchmarkProcessingStart $event): void
    {
        if ($event->benchmark->status === StatusEnum::PENDING) {
            $event->benchmark->processingStart();
            $event->benchmark->increment('attempts');
        }
    }

    public function onError(BenchmarkProcessingFailed $event): void
    {
        $event->benchmark->processingError($event->exception);
    }

    public function onFinish(BenchmarkProcessingDone $event): void
    {
        $event->benchmark->processingDone();
    }

    public function onCancel(BenchmarkProcessingCanceled $event): void
    {
        $event->benchmark->processingCancel();

        // Cancel all steps not processed
        $event->benchmark->steps()
            ->whereIn('status', [StatusEnum::PROCESSING, StatusEnum::PENDING])
            ->each(function ($benchmarkStep) {
                /** @var BenchmarkStep $benchmarkStep */
                event(new BenchmarkStepProcessingCanceled($benchmarkStep));
            });
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            BenchmarkProcessingStart::class,
            'App\Listeners\BenchmarkProcessingListener@onStart'
        );

        $events->listen(
            BenchmarkProcessingFailed::class,
            'App\Listeners\BenchmarkProcessingListener@onError'
        );

        $events->listen(
            BenchmarkProcessingDone::class,
            'App\Listeners\BenchmarkProcessingListener@onFinish'
        );

        $events->listen(
            BenchmarkProcessingCanceled::class,
            'App\Listeners\BenchmarkProcessingListener@onCancel'
        );
    }
}
