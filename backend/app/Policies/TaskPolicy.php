<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    // Check if the user is allowed to see this task
    public function view(User $user, Task $task): bool
    {
        // You can see the task if it's yours, or if you're an admin
        return $user->id === $task->user_id || $user->isAdmin();
    }

    // Check if the user is allowed to make changes to this task
    public function update(User $user, Task $task): bool
    {
        // You can change the task if it's yours, or if you're an admin
        return $user->id === $task->user_id || $user->isAdmin();
    }

    // Check if the user is allowed to delete this task
    public function delete(User $user, Task $task): bool
    {
        // You can delete the task if it's yours, or if you're an admin
        return $user->id === $task->user_id || $user->isAdmin();
    }
}
