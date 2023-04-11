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

namespace App\Models;

use App\Enums\StatusEnum;
use App\Events\BenchmarkProcessingDone;
use App\Exceptions\GatewayApiAccountNotFoundException;
use App\Jobs\BenchmarkStepDispatcherJob;
use App\Models\Contracts\Benchmarkable;
use App\Models\Traits\HasProcessing;
use App\Services\BenchmarkService\BenchmarkStepHandler;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Throwable;

/**
 * @property Benchmark $benchmark
 * @property Project $project
 * @property int $total_jobs
 * @property int $finished_jobs
 * @property string $handler
 */
class BenchmarkStep extends Model
{
    use HasFactory;
    use HasProcessing;
    use UsesTenantConnection;

    protected ?array $params;

    protected int $nbBatchErrors;

    protected $fillable = [
        'position',
        'handler',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function benchmark(): BelongsTo
    {
        return $this->belongsTo(Benchmark::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(BenchmarkStepBatch::class);
    }

    public function batchErrors(): HasManyThrough
    {
        return $this->hasManyThrough(
            BenchmarkStepBatchError::class,
            BenchmarkStepBatch::class
        );
    }

    public function getProject(): Project
    {
        $project = app('currentProject');

        if (! $project) {
            throw new ModelNotFoundException();
        }

        return $project;
    }

    public function benchmarked(): Benchmarkable
    {
        $benchmarked = $this->benchmark->benchmarkable()->first();

        if (! $benchmarked instanceof Benchmarkable) {
            throw new \Exception('Model must implement Benchmarkable interface');
        }

        return $benchmarked;
    }

    public function addBatch(Batch $batch): BenchmarkStepBatch
    {
        return $this->batches()->updateOrCreate([
            'batch_id' => $batch->id,
        ], [
            'batch_total_jobs' => $batch->totalJobs,
        ]);
    }

    public function getNbBatchErrorsAttribute(): int
    {
        if (!isset($this->nbBatchErrors)) {
            $this->nbBatchErrors = $this->batchErrors()->count();
        }

        return $this->nbBatchErrors;
    }

    public function hasAllStepBatchesDone(): bool
    {
        // Check if all batch are done
        return $this->batches()
            ->whereNotIn('status', [StatusEnum::DONE, StatusEnum::SKIPPED])
            ->doesntExist();
    }

    public function hasAllStepBatchesPending(): bool
    {
        // Check if all batch are pending
        return $this->batches()
            ->whereIn('status', StatusEnum::allExceptPending())
            ->doesntExist();
    }

    public function getNextStep(): ?BenchmarkStep
    {
        return self::where('benchmark_id', $this->benchmark_id)
            ->where('position', '>', $this->position)
            ->orderBy('position')
            ->first();
    }


    public function process(): void
    {
        // Prevent processing multiple time
        if ($this->isProcessing() || ! empty($this->dispatcher_batch_id)) {
            return ;
        }

        $batchName = implode(':', [
            'DISPATCH',
            class_basename($this->handler),
            $this->getKey(),
            $this->getProject()->id,
        ]);

        $batch = Bus::batch([
            new BenchmarkStepDispatcherJob($this->id),
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
            //$batch?->delete();
        })->catch(function (Batch $batch, Throwable $e) {
            logger($e->getMessage(), [
                'job' => BenchmarkStepDispatcherJob::class,
                'batch_id' => $batch->id,
                'batch_name' => $batch->name,
            ]);
            // First batch job failure detected...
        })->finally(function (Batch $batch) {
            // The batch has finished executing...
        })->onQueue('high')
            ->name($batchName)
            ->dispatch();

        $this->dispatcher_batch_id = $batch->id;
        $this->save();
    }

    public function skip(): void
    {
        $this->status = StatusEnum::SKIPPED;
        $this->finished_at = now();
        $this->save();
    }

    public function getBabbarApiAccount(): Gateway
    {
        $babbarApiAccount = $this->getProject()->user->getBabbarApiAccount();

        if (! $babbarApiAccount) {
            throw new GatewayApiAccountNotFoundException();
        }

        return $babbarApiAccount;
    }

    public function getGatewayApiAccount(string $gatewayKey): Gateway
    {
        $gatewayApiAccount = $this->getProject()->user->getGatewayApiAccount($gatewayKey);

        if (! $gatewayApiAccount) {
            throw GatewayApiAccountNotFoundException::for($gatewayKey);
        }

        return $gatewayApiAccount;
    }

    public function getParams(): ?array
    {
        if (! isset($this->params)) {
            $this->params = $this->benchmark->getBenchmarkService()
                ->getStep($this->position - 1);
        }

        return $this->params;
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        $params = $this->getParams();
        if (! $params) {
            return null;
        }

        return Arr::get($params, $key, $default);
    }

    public function getExtraArguments(): ?array
    {
        $params = $this->getParams();
        if (! $params) {
            return null;
        }

        return Arr::except($params, ['job', 'payload']);
    }

    public function processingEstimateTimeRemaining(): int
    {
        $nbJobsProcessingPerMinute = $this->getBabbarApiAccount()->x_ratelimit_limit ?? 6;

        return (int) ceil($this->getPendingJobsRemaining() / $nbJobsProcessingPerMinute);
    }

    public function getPendingJobsRemaining(): int
    {
        if ($this->status !== StatusEnum::PROCESSING) {
            return 0;
        }

        return $this->total_jobs - $this->finished_jobs;
    }
}
