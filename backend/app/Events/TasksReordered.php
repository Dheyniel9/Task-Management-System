<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TasksReordered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public array $taskOrders;

    public function __construct(int $userId, array $taskOrders)
    {
        $this->userId = $userId;
        $this->taskOrders = $taskOrders;
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
            'task_orders' => $this->taskOrders,
        ];
    }
}
