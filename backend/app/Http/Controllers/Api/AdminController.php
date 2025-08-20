<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\AdminTaskResource;
use App\Models\User;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    /**
     * Show a list of all users and how many tasks they have done or still need to do.
     */
    public function users(Request $request): JsonResponse
    {
        // Get a list of users, including how many tasks they finished and how many are left
        $users = User::withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
            $query->where('status', 'completed');
        }, 'tasks as pending_tasks_count' => function ($query) {
            $query->where('status', 'pending');
        }])->paginate(15);

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Show all tasks from every user.
     */
    public function allTasks(Request $request): JsonResponse
    {
        // Check if the filter options sent by the user are correct
        $filters = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,completed',
            'priority' => 'nullable|in:low,medium,high',
            'search' => 'nullable|string|max:255',
        ]);

        // Start building the search for tasks
        $query = Task::with('user');

        // Add filters if the user asked for them
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Get a list of tasks, split into pages
        $tasks = $query->ordered()->paginate(20);

        return response()->json([
            'data' => AdminTaskResource::collection($tasks),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    /**
     * Show detailed stats for one user.
     */
    public function userStatistics(User $user): JsonResponse
    {
        $stats = $this->taskService->getUserStatistics($user->id);

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'statistics' => $stats,
            ],
        ]);
    }

    /**
     * Show general stats for the whole system.
     */
    public function systemStatistics(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'total_tasks' => Task::count(),
            'tasks_by_status' => [
                'pending' => Task::where('status', 'pending')->count(),
                'completed' => Task::where('status', 'completed')->count(),
            ],
            'tasks_by_priority' => [
                'low' => Task::where('priority', 'low')->count(),
                'medium' => Task::where('priority', 'medium')->count(),
                'high' => Task::where('priority', 'high')->count(),
            ],
            'recent_tasks' => Task::with('user')
                ->latest()
                ->take(10)
                ->get()
                ->map(fn($task) => new AdminTaskResource($task)),
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }
}
