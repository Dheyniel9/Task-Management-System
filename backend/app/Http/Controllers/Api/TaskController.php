<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\ReorderTasksRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    /**
     * Show all of the logged-in user’s tasks.
     * (You can also filter them by status, priority, or search word)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Check that the filters entered are valid
        $filters = $request->validate([
            'status' => 'nullable|in:pending,completed',
            'priority' => 'nullable|in:low,medium,high',
            'search' => 'nullable|string|max:255',
        ]);

        // Get tasks from the service (with filters applied)
        $tasks = $this->taskService->getUserTasks($request->user(), $filters);

        return TaskResource::collection($tasks);
    }

    /**
     * Add a new task for the user.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        // Save the task using the validated data
        $task = $this->taskService->createTask(
            $request->user(),
            $request->validated()
        );

        // Tell other users in real-time that a new task was created
        broadcast(new \App\Events\TaskCreated($task))->toOthers();

        return response()->json([
            'data' => new TaskResource($task),
            'message' => 'Task created successfully',
        ], 201);
    }

    /**
     * Show a single task’s details.
     */
    public function show(Task $task): JsonResponse
    {
        // Make sure the user has permission to see this task
        $this->authorize('view', $task);

        return response()->json([
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Edit/update an existing task.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        // Make sure the user has permission to update this task
        $this->authorize('update', $task);

        // Update the task with new data
        $task = $this->taskService->updateTask(
            $task,
            $request->user(),
            $request->validated()
        );

        // Tell others in real-time that this task was updated
        broadcast(new \App\Events\TaskUpdated($task))->toOthers();

        return response()->json([
            'data' => new TaskResource($task),
            'message' => 'Task updated successfully',
        ]);
    }

    /**
     * Delete a task.
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        // Make sure the user has permission to delete this task
        $this->authorize('delete', $task);

        // Actually remove the task
        $this->taskService->deleteTask($task, $request->user());

        // Tell others in real-time that this task was deleted
        broadcast(new \App\Events\TaskDeleted($task->id, $task->user_id))->toOthers();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Change the order of the user’s tasks (like drag and drop).
     */
    public function reorder(ReorderTasksRequest $request): JsonResponse
    {
        // Save the new order of tasks
        $this->taskService->reorderTasks(
            $request->user(),
            $request->validated('tasks')
        );

        // Tell others in real-time that the order changed
        broadcast(new \App\Events\TasksReordered(
            $request->user()->id,
            $request->validated('tasks')
        ))->toOthers();

        return response()->json([
            'message' => 'Tasks reordered successfully',
        ]);
    }

    /**
     * Show task statistics for the user
     * (total, completed, pending, priorities).
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->taskService->getUserStatistics($request->user()->id);

        return response()->json([
            'data' => $stats,
        ]);
    }
}
