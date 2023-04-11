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
use App\Models\Traits\HasProcessing;
use Illuminate\Bus\Batch;
use Illuminate\Bus\BatchRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property benchmarkStep $benchmarkStep
 */
class BenchmarkStepBatch extends Model
{
    use HasFactory;
    use HasProcessing;
    use UsesTenantConnection;

    protected $fillable = [
        'batch_id',
        'batch_total_jobs',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public static function findByBatch(Batch $batch): BenchmarkStepBatch
    {
        return static::where('batch_id', $batch->id)->firstOrFail();
    }

    public function benchmarkStep(): BelongsTo
    {
        return $this->belongsTo(BenchmarkStep::class);
    }

    public function errors(): HasMany
    {
        return $this->hasMany(BenchmarkStepBatchError::class);
    }

    public function getQueueBatch(): ?Batch
    {
        return app(BatchRepository::class)->find($this->batch_id);
    }
}
