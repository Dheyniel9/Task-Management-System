<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {}

    /**
     * Get all tasks for a user.
     * You can also narrow them down by status, priority, or a search word.
     */
    public function getUserTasks(User $user, array $filters = []): Collection
    {
        $query = Task::where('user_id', $user->id);

        // Filter tasks by status (ex: completed, pending)
        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        // Filter tasks by priority (ex: low, medium, high)
        if (!empty($filters['priority'])) {
            $query->priority($filters['priority']);
        }

        // Filter tasks by keyword in title or description
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->ordered()->get();
    }

    /**
     * Add a new task for a user.
     */
    public function createTask(User $user, array $data): Task
    {
        $data['user_id'] = $user->id;
        return $this->taskRepository->create($data);
    }

    /**
     * Update a task only if the user owns it,
     * unless the user is an admin.
     */
    public function updateTask(Task $task, User $user, array $data): Task
    {
        // If not admin and not the owner, stop them
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            throw new \Exception('Unauthorized to update this task');
        }

        return $this->taskRepository->update($task, $data);
    }

    /**
     * Delete a task only if the user owns it,
     * or if they are an admin.
     */
    public function deleteTask(Task $task, User $user): bool
    {
        // If not admin and not the owner, stop them
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            throw new \Exception('Unauthorized to delete this task');
        }

        return $this->taskRepository->delete($task);
    }

    /**
     * Change the order of the user’s tasks.
     * (like dragging tasks up and down in a list)
     */
    public function reorderTasks(User $user, array $taskOrders): void
    {
        DB::transaction(function () use ($user, $taskOrders) {
            $this->taskRepository->reorder($user->id, $taskOrders);
        });
    }

    /**
     * Get a quick summary of the user’s tasks:
     * - Total number of tasks
     * - How many are completed
     * - How many are pending
     * - How many by priority (low, medium, high)
     */
    public function getUserStatistics(int $userId): array
    {
        $tasks = Task::where('user_id', $userId)->get();

        return [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'by_priority' => [
                'low' => $tasks->where('priority', 'low')->count(),
                'medium' => $tasks->where('priority', 'medium')->count(),
                'high' => $tasks->where('priority', 'high')->count(),
            ],
        ];
    }
}
