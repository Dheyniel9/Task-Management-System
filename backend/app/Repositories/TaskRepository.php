<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Get all tasks for a user
     */
    public function getUserTasks(User $user, array $filters = []): Collection
    {
        $query = $user->tasks()->orderBy('order');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->get();
    }

    /**
     * Create a new task
     */
    public function create(array $data): Task
    {
        // Get the next order value for this user (starting from 0)
        $maxOrder = Task::where('user_id', $data['user_id'])->max('order');
        $nextOrder = $maxOrder === null ? 0 : $maxOrder + 1;

        return Task::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'priority' => $data['priority'] ?? 'medium',
            'order' => $nextOrder,
        ]);
    }

    /**
     * Update an existing task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    /**
     * Delete a task
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Reorder tasks for a user
     */
    public function reorder(int $userId, array $taskOrders): void
    {
        foreach ($taskOrders as $taskId => $order) {
            Task::where('user_id', $userId)
                ->where('id', $taskId)
                ->update(['order' => $order]);
        }
    }

    /**
     * Get task statistics for a user
     */
    public function getUserStatistics(User $user): array
    {
        $tasks = $user->tasks();

        return [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'high_priority' => $tasks->where('priority', 'high')->count(),
            'medium_priority' => $tasks->where('priority', 'medium')->count(),
            'low_priority' => $tasks->where('priority', 'low')->count(),
        ];
    }
}
