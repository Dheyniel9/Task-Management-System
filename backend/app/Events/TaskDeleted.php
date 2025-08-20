<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $taskId;
    public int $userId;

    public function __construct(int $taskId, int $userId)
    {
        $this->taskId = $taskId;
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('user.' . $this->userId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'task_id' => $this->taskId,
        ];
    }
}
