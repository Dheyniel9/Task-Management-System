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
use Illuminate\Support\Facades\Hash;

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


/**
 * Create a new user
 */
public function createUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'is_admin' => 'boolean'
    ]);

    try {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin ?? false,
            'email_verified_at' => now(), // Auto-verify admin created users
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating user',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Update an existing user
 */
public function updateUser(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'is_admin' => 'boolean'
    ]);

    try {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->is_admin ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating user',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Delete a user
 */
public function deleteUser(User $user)
{
    try {
        // Prevent deleting the last admin user
        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the last admin user'
            ], 422);
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        // Delete user's tasks first (or handle cascade)
        $user->tasks()->delete();
        
        // Delete the user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting user',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get detailed user information
 */
public function getUserDetails(User $user)
{
    try {
        $userWithStats = User::withCount([
            'tasks as tasks_count',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            },
            'tasks as pending_tasks_count' => function ($query) {
                $query->where('status', 'pending');
            },
            'tasks as in_progress_tasks_count' => function ($query) {
                $query->where('status', 'in_progress');
            }
        ])->find($user->id);

        return response()->json([
            'success' => true,
            'data' => $userWithStats
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching user details',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Update task status (for drag-and-drop)
 */
public function updateTaskStatus(Request $request, Task $task)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed'
    ]);

    try {
        $task->status = $request->status;
        
        // Set completion timestamp if marking as completed
        if ($request->status === 'completed') {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }

        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'data' => $task
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating task status',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Bulk delete users
 */
public function bulkDeleteUsers(Request $request)
{
    $request->validate([
        'user_ids' => 'required|array',
        'user_ids.*' => 'exists:users,id'
    ]);

    try {
        $currentUserId = auth()->id();
        $userIds = collect($request->user_ids);

        // Prevent self-deletion
        if ($userIds->contains($currentUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        // Check if trying to delete all admin users
        $adminUsers = User::whereIn('id', $userIds)->where('is_admin', true)->count();
        $totalAdmins = User::where('is_admin', true)->count();
        
        if ($adminUsers >= $totalAdmins) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete all admin users'
            ], 422);
        }

        // Delete users and their tasks
        $deletedCount = 0;
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->tasks()->delete();
                $user->delete();
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} users"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting users',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Export users data
 */
public function exportUsers(Request $request)
{
    try {
        $format = $request->get('format', 'csv');
        $users = User::withCount([
            'tasks as tasks_count',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ])->get();

        if ($format === 'csv') {
            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'ID', 'Name', 'Email', 'Role', 'Total Tasks', 
                    'Completed Tasks', 'Join Date', 'Last Updated'
                ]);

                // CSV data
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->is_admin ? 'Admin' : 'User',
                        $user->tasks_count ?? 0,
                        $user->completed_tasks_count ?? 0,
                        $user->created_at->format('Y-m-d H:i:s'),
                        $user->updated_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // JSON export fallback
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error exporting users',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
