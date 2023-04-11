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

use App\Models\BenchmarkStep;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BenchmarkStepProcessingCanceled
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public BenchmarkStep $benchmarkStep;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BenchmarkStep $benchmarkStep)
    {
        $this->benchmarkStep = $benchmarkStep;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('benchmarkStep');
    }
}
