<?php

namespace App\Events;

use App\Models\Task;
use App\Http\Resources\TaskResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Task $task;

    // When I make a new event, I save the task that was created
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    // I tell Laravel which channels to broadcast this event on
    public function broadcastOn(): array
    {
        // I send the event to a channel just for the user who owns the task
        return [
            new PresenceChannel('user.' . $this->task->user_id),
        ];
    }

    // I decide what data to send with the event
    public function broadcastWith(): array
    {
        // I send the new task's info as JSON
        return [
            'task' => new TaskResource($this->task),
        ];
    }
}
