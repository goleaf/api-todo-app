<?php

use App\Http\Controllers\Api\V1\AsyncApiController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\CategoryApiController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use App\Http\Controllers\Api\V1\ProfileApiController;
use App\Http\Controllers\Api\V1\TaskApiController;
use App\Http\Controllers\Api\V1\UserApiController;
use App\Http\Controllers\Api\V1\TagApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Documentation route
Route::get('/documentation', function () {
    return response()->json([
        'success' => true,
        'message' => 'API documentation available at /api/docs',
        'data' => [
            'version' => '1.0',
            'endpoints' => [
                'auth' => ['/register', '/login', '/logout', '/me', '/refresh'],
                'users' => ['/users', '/users/{id}', '/users/statistics'],
                'tasks' => ['/tasks', '/tasks/{id}', '/tasks/statistics', '/tasks/{id}/tags', '/tasks/{id}/tags/bulk', '/tasks/by-tag/{tagName}'],
                'categories' => ['/categories', '/categories/{id}', '/categories/task-counts'],
                'tags' => ['/tags', '/tags/{id}', '/tags/popular', '/tags/task-counts', '/tags/{id}/tasks', '/tags/merge', '/tags/suggestions', '/tags/batch'],
                'profile' => ['/profile', '/profile/password', '/profile/photo'],
                'dashboard' => ['/dashboard'],
            ],
        ],
    ]);
});

// Public routes
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::post('/refresh', [AuthApiController::class, 'refresh']);
    Route::get('/me', [AuthApiController::class, 'me']);

    // User routes - full CRUD
    Route::prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index']);
        Route::post('/', [UserApiController::class, 'store']);
        Route::get('/statistics', [UserApiController::class, 'statistics']);
        Route::get('/{id}', [UserApiController::class, 'show']);
        Route::put('/{id}', [UserApiController::class, 'update']);
        Route::delete('/{id}', [UserApiController::class, 'destroy']);
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileApiController::class, 'show']);
        Route::put('/', [ProfileApiController::class, 'update']);
        Route::put('/password', [ProfileApiController::class, 'updatePassword']);
        Route::post('/photo', [ProfileApiController::class, 'uploadPhoto']);
        Route::delete('/photo', [ProfileApiController::class, 'deletePhoto']);
    });

    // Task routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskApiController::class, 'index']);
        Route::post('/', [TaskApiController::class, 'store']);
        Route::get('/statistics', [TaskApiController::class, 'statistics']);
        Route::get('/due-today', [TaskApiController::class, 'dueToday']);
        Route::get('/overdue', [TaskApiController::class, 'overdue']);
        Route::get('/upcoming', [TaskApiController::class, 'upcoming']);
        Route::get('/by-tag/{tagName}', [TaskApiController::class, 'findByTag']);
        Route::get('/{id}', [TaskApiController::class, 'show']);
        Route::put('/{id}', [TaskApiController::class, 'update']);
        Route::delete('/{id}', [TaskApiController::class, 'destroy']);
        Route::patch('/{id}/toggle', [TaskApiController::class, 'toggleCompletion']);
        Route::get('/{id}/tags', [TaskApiController::class, 'tags']);
        Route::put('/{id}/tags', [TaskApiController::class, 'updateTags']);
        Route::post('/{id}/tags', [TaskApiController::class, 'bulkTagOperation']);
    });

    // Category routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryApiController::class, 'index']);
        Route::post('/', [CategoryApiController::class, 'store']);
        Route::get('/task-counts', [CategoryApiController::class, 'taskCounts']);
        Route::get('/{id}', [CategoryApiController::class, 'show']);
        Route::put('/{id}', [CategoryApiController::class, 'update']);
        Route::delete('/{id}', [CategoryApiController::class, 'destroy']);
    });

    // Tag routes
    Route::prefix('tags')->group(function () {
        Route::get('/', [TagApiController::class, 'index']);
        Route::post('/', [TagApiController::class, 'store']);
        Route::get('/popular', [TagApiController::class, 'popular']);
        Route::get('/task-counts', [TagApiController::class, 'taskCounts']);
        Route::post('/merge', [TagApiController::class, 'merge']);
        Route::get('/suggestions', [TagApiController::class, 'suggestions']);
        Route::post('/batch', [TagApiController::class, 'batchCreate']);
        Route::get('/{id}', [TagApiController::class, 'show']);
        Route::put('/{id}', [TagApiController::class, 'update']);
        Route::delete('/{id}', [TagApiController::class, 'destroy']);
        Route::get('/{id}/tasks', [TagApiController::class, 'tasks']);
    });

    // Dashboard routes
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
});

// API fallback - 404 for invalid routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'errors' => ['endpoint' => 'The requested endpoint does not exist'],
    ], 404);
});

/*
|--------------------------------------------------------------------------
| Hypervel Async API Routes
|--------------------------------------------------------------------------
|
| These routes demonstrate the use of Hypervel for asynchronous API operations.
| They use the AsyncApiController which leverages coroutines for concurrent
| processing and improved performance.
|
*/

Route::middleware('auth:sanctum')->prefix('async')->group(function () {
    Route::get('/dashboard-stats', [AsyncApiController::class, 'getDashboardStats']);
    Route::get('/external-apis', [AsyncApiController::class, 'fetchExternalApis']);
    Route::post('/process-tasks', [AsyncApiController::class, 'bulkProcessTasks']);
});
