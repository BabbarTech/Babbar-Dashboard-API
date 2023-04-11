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

namespace App\Models\Traits;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;

trait HasProcessing
{
    public function processingStart(): void
    {
        $this->status = StatusEnum::PROCESSING;
        $this->started_at = now();
        $this->save();
    }

    public function processingDone(): void
    {
        $this->status = StatusEnum::DONE;
        $this->finished_at = now();
        $this->save();
    }

    public function processingCancel(): void
    {
        $this->status = StatusEnum::CANCELLED;
        $this->save();
    }

    public function processingError(\Throwable $error): void
    {
        $this->status = StatusEnum::ERROR;
        $this->error = $error->getMessage();
        $this->save();
    }

    public function retryProcessing(): void
    {
        $this->increment('attempts', 1, [
            'status' => StatusEnum::PROCESSING
        ]);
    }

    public function isDone(): bool
    {
        return $this->status === StatusEnum::DONE;
    }

    public function isProcessing(): bool
    {
        return $this->status === StatusEnum::PROCESSING;
    }

    public function isPending(): bool
    {
        return $this->status === StatusEnum::PENDING;
    }

    public function isFail(): bool
    {
        return $this->status === StatusEnum::ERROR;
    }

    public function isCancelled(): bool
    {
        return $this->status === StatusEnum::CANCELLED;
    }

    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::DONE);
    }

    public function scopeNotDone(Builder $query): Builder
    {
        return $query->where('status', '!=', StatusEnum::DONE);
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::PROCESSING);
    }

    public function scopeFail(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::ERROR);
    }
}
