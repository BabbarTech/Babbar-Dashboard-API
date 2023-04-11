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
use App\Models\Contracts\Benchmarkable;
use App\Models\Traits\HasProcessing;
use App\Services\BenchmarkService\Contracts\BenchmarkService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property StatusEnum $status
 * @property string $error
 * @property Benchmarkable $benchmarkable
 */
class Benchmark extends Model
{
    use HasFactory;
    use HasProcessing;
    use UsesTenantConnection;

    protected BenchmarkService $benchmarkService;

    protected $fillable = [
        'type',
        'params',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'params' => 'array',
    ];

    protected static function booted(): void
    {
        static::created(function ($benchmark) {
            $position = 1;
            foreach ($benchmark->getBenchmarkService()->steps() as $step) {
                $benchmark->steps()->create([
                    'handler' => $step['job'],
                    //'params' => $step['params'] ?? null,
                    'position' => $position++
                ]);
            }
        });
    }

    public function benchmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function steps(): HasMany
    {
        return $this->hasMany(BenchmarkStep::class)
            ->orderBy('position');
    }

    public function getBenchmarkService(): BenchmarkService
    {
        if (! isset($this->benchmarkService)) {
            // Instantiate benchmark service
            $this->benchmarkService = resolve($this->type)
                ->setBenchmarkInstance($this);
        }

        return $this->benchmarkService;
    }

    public function getBenchmarkableTitle(): string
    {
        return $this->benchmarkable->title;
    }

    public function process(): void
    {
        $this->steps()
            ->orderBy('position')
            ->first()
            ?->process();
    }

    public function hasAllStepsDone(): bool
    {
        return $this->steps()
            ->whereNotIn('status', [StatusEnum::DONE, StatusEnum::SKIPPED])
            ->doesntExist();
    }
}
