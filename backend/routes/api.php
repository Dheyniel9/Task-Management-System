<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Think of this file as the "map" for your API.
| It decides which URL should go to which controller function.
*/

/*
|-------------------------
| Public routes (no login)
|-------------------------
| These routes can be used without logging in.
| Example: Registering or Logging in.
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']); // Create account
    Route::post('/login', [AuthController::class, 'login']);       // Login
});

/*
|-----------------------------
| Protected routes (need login)
|-----------------------------
| These routes can only be used if the user is logged in.
| They are protected with "auth:sanctum".
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    | Auth-related actions for logged-in users
    | Example: Logout, check your own profile
    */
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']); // Logout
        Route::get('/user', [AuthController::class, 'user']);      // Get logged-in user info
    });

    /*
    | Task routes
    | Example: Create, update, delete, or view tasks
    */
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);          // List all tasks
        Route::post('/', [TaskController::class, 'store']);         // Create a task
        Route::get('/statistics', [TaskController::class, 'statistics']); // Task stats
        Route::post('/reorder', [TaskController::class, 'reorder']);      // Change order
        Route::get('/{task}', [TaskController::class, 'show']);     // View one task
        Route::put('/{task}', [TaskController::class, 'update']);   // Update task
        Route::patch('/{task}', [TaskController::class, 'update']); // Update task (partial)
        Route::delete('/{task}', [TaskController::class, 'destroy']); // Delete task
    });

    /*
    | Admin-only routes
    | These can only be used by users with "admin" access.
    | Example: View all users, all tasks, or overall statistics.
    */
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);                     // See all users
        Route::get('/users/{user}/statistics', [AdminController::class, 'userStatistics']); // Stats per user
        Route::get('/tasks', [AdminController::class, 'allTasks']);                 // See all tasks
        Route::get('/statistics', [AdminController::class, 'systemStatistics']);    // System-wide stats
    });
});
