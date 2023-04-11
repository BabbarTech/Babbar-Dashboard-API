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

namespace App\Events;

use App\Models\BenchmarkStepBatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class BenchmarkStepBatchProcessingFailed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Batch $batch;

    public BenchmarkStepBatch $benchmarkStepBatch;

    public Throwable $exception;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Batch $batch, Throwable $exception)
    {
        $this->batch = $batch;
        $this->benchmarkStepBatch = BenchmarkStepBatch::findByBatch($batch);
        $this->exception = $exception;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('benchmarkStepBatch');
    }
}
