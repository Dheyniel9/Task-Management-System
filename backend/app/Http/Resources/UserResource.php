<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    // Turn the user data into an array that can be sent as JSON
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'tasks_count' => $this->when($this->tasks_count !== null, $this->tasks_count),
            'completed_tasks_count' => $this->when($this->completed_tasks_count !== null, $this->completed_tasks_count),
            'pending_tasks_count' => $this->when($this->pending_tasks_count !== null, $this->pending_tasks_count),
        ];
    }
}
