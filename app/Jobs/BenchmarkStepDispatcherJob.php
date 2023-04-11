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

namespace App\Jobs;

use App\Events\BenchmarkStepBatchProcessingDone;
use App\Events\BenchmarkStepBatchProcessingFailed;
use App\Events\BenchmarkStepProcessingFailed;
use App\Models\BenchmarkStep;
use App\Services\BenchmarkService\Contracts\BenchmarkStepPayloadCollection;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Spatie\Multitenancy\Jobs\TenantAware;
use Closure;
use Throwable;

class BenchmarkStepDispatcherJob implements ShouldQueue, TenantAware
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected BenchmarkStep $benchmarkStep;

    protected int $delta;

    protected Collection $payloadCollection;

    protected int $totalJobs;

    protected int $nbJobsPerBatch = 20;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $benchmarkStepId, int $delta = 0)
    {
        $this->benchmarkStep = BenchmarkStep::findOrfail($benchmarkStepId);
        $this->delta = $delta;
    }

    public function backoff(): array
    {
        //return [1, 2, 3]; // for testing
        return [10, 30, 60, 180];
    }

    public function retryUntil(): Carbon
    {
        //return now()->addMinute(); // for testing
        return now()->addDay();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->isCancelled()) {
            return;
        }

        $payloadCollection = $this->getPayloadCollection();

        if ($payloadCollection->isEmpty()) {
            $this->benchmarkStep->skip();
            $this->benchmarkStep->getNextStep()?->process();
            return;
        }

        // Start benchmark step
        if ($this->delta === 0) {
            $this->benchmarkStep->attempts += 1;
            $this->benchmarkStep->total_jobs = $this->getTotalJobs();
            $this->benchmarkStep->processingStart();
        }

        $jobCollection = $this->makeJobCollection($payloadCollection);
        $this->dispatchBatchJobs($jobCollection);

        $nextDelta = $this->delta + 1;
        $nbJobsDispatched = $nextDelta * $this->getNbJobsPerBatch();

        if ($nbJobsDispatched < $this->getTotalJobs()) {
            $this->batch()?->add(new self($this->benchmarkStep->id, $nextDelta));
        }
    }

    protected function isCancelled(): bool
    {
        if ($this->batch()?->cancelled()) {
            $this->batch()->delete();
            return true;
        }

        if ($this->benchmarkStep->isCancelled()) {
            return true;
        }

        return false;
    }

    protected function getNbJobsPerBatch(): int
    {
        return $this->nbJobsPerBatch;
    }

    protected function getTotalJobs(): int
    {
        return $this->totalJobs;
    }

    protected function getPayloadCollection(): Collection
    {
        if (! isset($this->payloadCollection)) {
            $payloadHandler = $this->benchmarkStep->getParam('payload');

            if (is_array($payloadHandler)) {
                $this->payloadCollection = $this->makePayloadCollectionFromArray($payloadHandler);
            } elseif ($payloadHandler instanceof BenchmarkStepPayloadCollection) {
                $this->payloadCollection = $this->makePayloadCollectionFromInterface($payloadHandler);
            } elseif ($payloadHandler instanceof \Closure) {
                $this->payloadCollection = $this->makePayloadCollectionFromClosure($payloadHandler);
            } else {
                throw new \Exception('Invalid Benchmark Step payloadCollection source');
            }
        }

        return $this->payloadCollection;
    }

    protected function makePayloadCollectionFromInterface(BenchmarkStepPayloadCollection $payloadHandler): Collection
    {
        $this->totalJobs = $payloadHandler->total();
        $this->nbJobsPerBatch = $payloadHandler->nbJobsPerBatch();

        return $payloadHandler->getPayloadCollection($this->delta);
    }

    protected function makePayloadCollectionFromArray(array $payloadHandler): Collection
    {
        $payloadCollection = Arr::isAssoc($payloadHandler) ? [$payloadHandler] : $payloadHandler;

        $this->totalJobs = count($payloadCollection);

        $page = $this->delta + 1;
        return collect($payloadCollection)
            ->forPage($page, $this->nbJobsPerBatch);
    }


    protected function makePayloadCollectionFromClosure(Closure $payloadHandler): Collection
    {
        $sources = call_user_func($payloadHandler, $this->benchmarkStep);

        if ($sources === null) {
            $sources = [];
        }

        if ($sources instanceof Collection) {
            $sources = $sources->filter()
                ->values()
                ->toArray();
        }

        if (! is_array($sources)) {
            throw new \Exception('Closure can not return an payload array');
        }

        return $this->makePayloadCollectionFromArray($sources);
    }


    protected function makeJobCollection(Collection $payloadCollection): Collection
    {
        $jobClass = $this->benchmarkStep->handler;
        $extraArguments = $this->benchmarkStep->getExtraArguments();
        $dispatchDelay = now()->addSeconds($this->calculateDispatchDelayInSeconds());

        return $payloadCollection->map(function ($payload, $delta) use ($jobClass, $extraArguments, $dispatchDelay) {
            $job = new $jobClass($this->benchmarkStep->id, $payload, $delta, $extraArguments);

            // Encapsulate benchmark step job into closure for delaying processing
            return ($job)->delay($dispatchDelay); /** @phpstan-ignore-line */
        });
    }

    protected function dispatchBatchJobs(Collection $jobCollection): void
    {

        $batch = Bus::batch($jobCollection)
            ->then(function (Batch $batch) {
                //
            })->catch(function (Batch $batch, Throwable $e) {
                event(new BenchmarkStepBatchProcessingFailed($batch, $e));
            })->finally(function (Batch $batch) {
                if ($batch->pendingJobs <= 0) {
                    if ($batch->failedJobs <= 0) {
                        event(new BenchmarkStepBatchProcessingDone($batch));
                    }

                    $batch->delete();
                }
            })
            ->name(class_basename($this->benchmarkStep->handler))
            ->dispatch();

        $this->benchmarkStep->addBatch($batch);
    }

    protected function calculateDispatchDelayInSeconds(): int
    {
        try {
            $seconds = intval(ceil($this->totalJobs / $this->nbJobsPerBatch) / 8);
        } catch (\Exception $e) {
            $seconds = 1;
        }

        if ($seconds < 1) {
            $seconds = 1;
        }

        return $seconds;
    }


    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        event(new BenchmarkStepProcessingFailed($this->benchmarkStep, $exception));
    }
}
